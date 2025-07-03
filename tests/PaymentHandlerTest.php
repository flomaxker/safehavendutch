<?php

use PHPUnit\Framework\TestCase;
use App\Payment\PaymentHandler;
use App\Models\Package;
use App\Models\Purchase;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeObject;

class PaymentHandlerTest extends TestCase
{
    private $pdoMock;
    private $packageModelMock;
    private $purchaseModelMock;
    private $userModelMock;
    private $stripeClientMock;
    private PaymentHandler $paymentHandler;

    protected function setUp(): void
    {
        // Create mock objects for dependencies
        $this->pdoMock = $this->createMock(PDO::class);
        $this->packageModelMock = $this->createMock(Package::class);
        $this->purchaseModelMock = $this->createMock(Purchase::class);
        $this->userModelMock = $this->createMock(User::class);
        $this->stripeClientMock = $this->getMockBuilder(\Stripe\StripeClient::class)
                                     ->addMethods(['checkout'])
                                     ->getMock();
        $this->stripeClientMock->checkout = $this->getMockBuilder(stdClass::class)->getMock();

        // Instantiate PaymentHandler with mocks
        $this->paymentHandler = new PaymentHandler(
            $this->pdoMock,
            $this->packageModelMock,
            $this->purchaseModelMock,
            $this->stripeClientMock,
            $this->userModelMock
        );
    }

    public function testCreateCheckoutSessionSuccessfully()
    {
        $userId = 1;
        $packageId = 101;
        $successUrl = 'http://localhost/success';
        $cancelUrl = 'http://localhost/cancel';
        $packageName = 'Test Package';
        $packageDescription = 'A test package';
        $packagePriceCents = 10000; // €100.00
        $purchaseId = 1;
        $sessionId = 'cs_test_123';
        $sessionUrl = 'https://checkout.stripe.com/c/pay/cs_test_123';

        // Configure package model mock
        $this->packageModelMock->expects($this->once())
            ->method('getById')
            ->with($packageId)
            ->willReturn([
                'id' => $packageId,
                'name' => $packageName,
                'description' => $packageDescription,
                'price_cents' => $packagePriceCents,
                'euro_value' => 100, // Example euro value
            ]);

        // Configure purchase model mock for create
        $this->purchaseModelMock->expects($this->once())
            ->method('create')
            ->with($userId, $packageId, '', $packagePriceCents, 'pending')
            ->willReturn($purchaseId);

        // Configure StripeClient mock
        $sessionsMock = $this->getMockBuilder(stdClass::class)
                             ->addMethods(['create'])
                             ->getMock();

        $checkoutMock = $this->createMock(stdClass::class);
        $checkoutMock->sessions = $sessionsMock;

        $this->stripeClientMock->checkout = $checkoutMock;

        $sessionObject = new stdClass();
        $sessionObject->id = $sessionId;
        $sessionObject->url = $sessionUrl;
        $sessionObject->metadata = (object)['purchase_id' => $purchaseId];

        $sessionsMock->expects($this->once())
            ->method('create')
            ->willReturn($sessionObject);
        $this->purchaseModelMock->expects($this->once())
            ->method('updateSessionId')
            ->with($purchaseId, $sessionId)
            ->willReturn(true);

        $result = $this->paymentHandler->createCheckoutSession($userId, $packageId, $successUrl, $cancelUrl);

        $this->assertEquals($sessionUrl, $result);
    }

    public function testHandleWebhookCheckoutSessionCompleted()
    {
        $payload = '{}';
        $sigHeader = 't=123,v1=abc';
        $endpointSecret = 'whsec_test';
        $purchaseId = 1;
        $userId = 10;
        $packageId = 101;
        $euroValue = 5000; // 50 euros

        // Mock the Stripe Event object
        $event = Event::constructFrom([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'metadata' => ['purchase_id' => $purchaseId],
                    'id' => 'cs_test_123',
                ],
            ],
        ]);

        // Mock the purchase model calls
        $this->purchaseModelMock->expects($this->once())
            ->method('updateStatusById')
            ->with($purchaseId, 'completed')
            ->willReturn(true);

        $this->purchaseModelMock->expects($this->once())
            ->method('getById')
            ->with($purchaseId)
            ->willReturn([
                'id' => $purchaseId,
                'user_id' => $userId,
                'package_id' => $packageId,
                'status' => 'completed',
            ]);

        // Mock the package model call
        $this->packageModelMock->expects($this->once())
            ->method('getById')
            ->with($packageId)
            ->willReturn([
                'id' => $packageId,
                'euro_value' => $euroValue,
            ]);

        // Mock the user model call
        $this->userModelMock->expects($this->once())
            ->method('updateEuroBalance')
            ->with($userId, $euroValue)
            ->willReturn(true);

        // Call the method under test
        $this->paymentHandler->handleWebhook($payload, $sigHeader, $endpointSecret, $event);
    }

    public function testHandleWebhookInvalidPayload()
    {
        $payload = 'invalid_json';
        $sigHeader = 't=123,v1=abc';
        $endpointSecret = 'whsec_test';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No signatures found matching the expected signature for payload');

        $this->paymentHandler->handleWebhook($payload, $sigHeader, $endpointSecret);
    }

    public function testHandleWebhookInvalidSignature()
    {
        $payload = '{}';
        $sigHeader = 'invalid_sig';
        $endpointSecret = 'whsec_test';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to extract timestamp and signatures from header');

        $this->paymentHandler->handleWebhook($payload, $sigHeader, $endpointSecret);
    }

    public function testHandleWebhookPurchaseIdNotFound()
    {
        $payload = '{}';
        $sigHeader = 't=123,v1=abc';
        $endpointSecret = 'whsec_test';
        $purchaseId = 1; // Define purchaseId here

        // Mock the Stripe Event object with missing metadata
        $event = Event::constructFrom([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                ],
            ],
        ]);

        // Expect no calls to purchaseModelMock or userModelMock
        $this->purchaseModelMock->expects($this->never())
            ->method('updateStatusById');
        $this->purchaseModelMock->expects($this->never())
            ->method('getById');
        $this->packageModelMock->expects($this->never())
            ->method('getById');
        $this->userModelMock->expects($this->never())
            ->method('updateEuroBalance');

        // Call the method under test
        $this->paymentHandler->handleWebhook($payload, $sigHeader, $endpointSecret, $event);
    }
}