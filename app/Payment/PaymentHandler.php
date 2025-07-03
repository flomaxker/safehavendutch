<?php

namespace App\Payment;

use PDO;
use Exception;
use Stripe\StripeClient;
use Stripe\Webhook;
use App\Models\Purchase;
use App\Models\Package;
use App\Models\User;

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
    /** @var User */
    private User $userModel;

    /**
     * PaymentHandler constructor.
     */
    public function __construct(PDO $pdo, Package $packageModel, Purchase $purchaseModel, StripeClient $stripe, User $userModel)
    {
        $this->pdo = $pdo;
        $this->packageModel = $packageModel;
        $this->purchaseModel = $purchaseModel;
        $this->stripe = $stripe;
        $this->userModel = $userModel;
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
     * @param ?\Stripe\Event $event Optional: For testing purposes, allows injecting a mocked Stripe Event.
     */
    public function handleWebhook(string $payload, string $sigHeader, string $endpointSecret, ?\Stripe\Event $event = null): void
    {
        error_log("Stripe Webhook: Received payload.");

        if ($event === null) {
            try {
                $event = Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $endpointSecret
                );
            } catch (\UnexpectedValueException $e) {
                // Invalid payload
                error_log("Stripe Webhook Error: Invalid payload. " . $e->getMessage());
                throw $e;
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                error_log("Stripe Webhook Error: Invalid signature. " . $e->getMessage());
                throw $e;
            }
        }

        error_log("Stripe Webhook: Event type - " . $event->type);

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $purchaseId = isset($session->metadata->purchase_id)
                    ? (int)$session->metadata->purchase_id
                    : null;

                if ($purchaseId) {
                    error_log("Stripe Webhook: Processing checkout.session.completed for purchase ID: " . $purchaseId);
                    try {
                        $this->purchaseModel->updateStatusById($purchaseId, 'completed');
                        error_log("Stripe Webhook: Purchase status updated to completed for ID: " . $purchaseId);

                        $purchase = $this->purchaseModel->getById($purchaseId);
                        if ($purchase) {
                            $package = $this->packageModel->getById($purchase['package_id']);
                            if ($package) {
                                $this->userModel->updateEuroBalance($purchase['user_id'], $package['euro_value']);
                                error_log("Stripe Webhook: Euros added to user " . $purchase['user_id'] . " for purchase ID: " . $purchaseId);
                            } else {
                                error_log("Stripe Webhook Error: Package not found for purchase ID: " . $purchaseId);
                            }
                        }
                    } catch (Exception $e) {
                        error_log("Stripe Webhook Error: Database operation failed for purchase ID " . $purchaseId . ": " . $e->getMessage());
                    }
                } else {
                    error_log("Stripe Webhook Error: purchase_id not found in session metadata for checkout.session.completed event.");
                }
                break;
            case 'checkout.session.async_payment_succeeded':
                error_log("Stripe Webhook: Async payment succeeded for session ID: " . $event->data->object->id);
                // Handle post-payment fulfillment
                break;
            case 'checkout.session.async_payment_failed':
                error_log("Stripe Webhook: Async payment failed for session ID: " . $event->data->object->id);
                // Send email to user, etc.
                break;
            case 'charge.succeeded':
                error_log("Stripe Webhook: Charge succeeded for charge ID: " . $event->data->object->id);
                // Handle successful charge (e.g., update order status)
                break;
            case 'charge.failed':
                error_log("Stripe Webhook: Charge failed for charge ID: " . $event->data->object->id);
                // Handle failed charge (e.g., notify user, update order status)
                break;
            // ... handle other event types
            default:
                error_log("Stripe Webhook: Unhandled event type " . $event->type);
        }
    }
}
