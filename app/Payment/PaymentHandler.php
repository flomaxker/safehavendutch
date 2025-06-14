<?php

namespace App\Payment;

use PDO;
use Exception;
use Stripe\StripeClient;
use Stripe\Webhook;
use App\Models\Purchase;
use App\Models\Package;

/**
 * Handles Stripe checkout session creation and webhook events.
 */
class PaymentHandler
{
    /** @var StripeClient */
    private StripeClient $stripe;
    /** @var PDO */
    private PDO $pdo;
    /** @var Package */
    private Package $packageModel;
    /** @var Purchase */
    private Purchase $purchaseModel;

    /**
     * PaymentHandler constructor.
     */
    public function __construct(PDO $pdo, Package $packageModel, Purchase $purchaseModel, StripeClient $stripe)
    {
        $this->pdo = $pdo;
        $this->packageModel = $packageModel;
        $this->purchaseModel = $purchaseModel;
        $this->stripe = $stripe;
    }

    /**
     * Create a Stripe Checkout session for a package purchase.
     * @param int $userId
     * @param int $packageId
     * @param string $successUrl
     * @param string $cancelUrl
     * @return string
     * @throws Exception
     */
    public function createCheckoutSession(int $userId, int $packageId, string $successUrl, string $cancelUrl): string
    {
        $package = $this->packageModel->getById($packageId);
        if (!$package) {
            throw new Exception('Package not found');
        }

        $purchaseId = $this->purchaseModel->create(
            $userId,
            $packageId,
            '',
            $package['price_cents'],
            'pending'
        );

        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $package['name'],
                        'description' => $package['description'],
                    ],
                    'unit_amount' => $package['price_cents'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => ['purchase_id' => $purchaseId],
        ]);

        $this->purchaseModel->updateSessionId($purchaseId, $session->id);

        return $session->url;
    }

    /**
     * Handle incoming Stripe webhook payload.
     * @param string $payload
     * @param string $sigHeader
     * @param string $endpointSecret
     */
    public function handleWebhook(string $payload, string $sigHeader, string $endpointSecret): void
    {
        $event = Webhook::constructEvent(
            $payload,
            $sigHeader,
            $endpointSecret
        );

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $purchaseId = isset($session->metadata->purchase_id)
                ? (int)$session->metadata->purchase_id
                : null;
            if ($purchaseId) {
                $this->purchaseModel->updateStatusById($purchaseId, 'completed');

                $purchase = $this->purchaseModel->getById($purchaseId);
                $package = $this->packageModel->getById($purchase['package_id']);

                $stmt = $this->pdo->prepare(
                    'UPDATE users SET credit_balance = credit_balance + :credits WHERE id = :user_id'
                );
                $stmt->execute([
                    'credits' => $package['credit_amount'],
                    'user_id' => $purchase['user_id'],
                ]);
            }
        }
    }
}

