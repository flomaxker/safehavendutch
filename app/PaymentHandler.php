<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Models/Purchase.php';
require_once __DIR__ . '/Models/Package.php';

/**
 * Handles Stripe checkout session creation and webhook events.
 */
class PaymentHandler
{
    /** @var \Stripe\StripeClient */
    private $stripe;
    /** @var PDO */
    private $pdo;

    /**
     * PaymentHandler constructor.
     * @param PDO $pdo
     * @param string $stripeSecretKey
     */
    public function __construct($pdo, $stripeSecretKey)
    {
        $this->pdo = $pdo;
        $this->stripe = new \Stripe\StripeClient($stripeSecretKey);
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
    public function createCheckoutSession($userId, $packageId, $successUrl, $cancelUrl)
    {
        $packageModel = new Package($this->pdo);
        $package = $packageModel->getById($packageId);
        if (!$package) {
            throw new Exception('Package not found');
        }

        $purchaseModel = new Purchase($this->pdo);
        $purchaseId = $purchaseModel->create(
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

        $purchaseModel->updateSessionId($purchaseId, $session->id);

        return $session->url;
    }

    /**
     * Handle incoming Stripe webhook payload.
     * @param string $payload
     * @param string $sigHeader
     * @param string $endpointSecret
     */
    public function handleWebhook($payload, $sigHeader, $endpointSecret)
    {
        $event = \Stripe\Webhook::constructEvent(
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
                $purchaseModel = new Purchase($this->pdo);
                $purchaseModel->updateStatusById($purchaseId, 'completed');

                $purchase = $purchaseModel->getById($purchaseId);
                $packageModel = new Package($this->pdo);
                $package = $packageModel->getById($purchase['package_id']);

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