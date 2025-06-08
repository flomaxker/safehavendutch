<?php

class Container
{
    private array $instances = [];

    public function getPdo(): PDO
    {
        if (!isset($this->instances['pdo'])) {
            $db = new Database();
            $this->instances['pdo'] = $db->getConnection();
        }
        return $this->instances['pdo'];
    }

    public function getPackageModel(): Package
    {
        if (!isset($this->instances['packageModel'])) {
            $this->instances['packageModel'] = new Package($this->getPdo());
        }
        return $this->instances['packageModel'];
    }

    public function getPurchaseModel(): Purchase
    {
        if (!isset($this->instances['purchaseModel'])) {
            $this->instances['purchaseModel'] = new Purchase($this->getPdo());
        }
        return $this->instances['purchaseModel'];
    }

    public function getStripeClient(): \Stripe\StripeClient
    {
        if (!isset($this->instances['stripeClient'])) {
            $this->instances['stripeClient'] = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        }
        return $this->instances['stripeClient'];
    }

    public function getPaymentHandler(): PaymentHandler
    {
        if (!isset($this->instances['paymentHandler'])) {
            $this->instances['paymentHandler'] = new PaymentHandler(
                $this->getPdo(),
                $this->getPackageModel(),
                $this->getPurchaseModel(),
                $this->getStripeClient()
            );
        }
        return $this->instances['paymentHandler'];
    }
}

