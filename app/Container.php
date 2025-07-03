<?php

namespace App;

use PDO;
use App\Database\Database;
use App\Models\Package;
use App\Models\Booking;
use App\Calendar\iCalGenerator;
use App\Calendar\iCalParser;
use App\Mail\Mailer;
use App\Models\Child;
use App\Models\Lesson;
use App\Models\Page;
use App\Models\Purchase;
use App\Models\Post;
use App\Models\User;
use App\Payment\PaymentHandler;
use App\Models\Setting;

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

    public function getChildModel(): Child
    {
        if (!isset($this->instances['childModel'])) {
            $this->instances['childModel'] = new Child($this->getPdo());
        }
        return $this->instances['childModel'];
    }

    public function getICalParser(): iCalParser
    {
        if (!isset($this->instances['icalParser'])) {
            $this->instances['icalParser'] = new iCalParser();
        }
        return $this->instances['icalParser'];
    }

    public function getICalGenerator(): iCalGenerator
    {
        if (!isset($this->instances['icalGenerator'])) {
            $this->instances['icalGenerator'] = new iCalGenerator();
        }
        return $this->instances['icalGenerator'];
    }

    public function getMailer(): Mailer
    {
        if (!isset($this->instances['mailer'])) {
            $this->instances['mailer'] = new Mailer();
        }
        return $this->instances['mailer'];
    }

    public function getBookingModel(): Booking
    {
        if (!isset($this->instances['bookingModel'])) {
            $this->instances['bookingModel'] = new Booking($this->getPdo());
        }
        return $this->instances['bookingModel'];
    }

    public function getLessonModel(): Lesson
    {
        if (!isset($this->instances['lessonModel'])) {
            $this->instances['lessonModel'] = new Lesson($this->getPdo());
        }
        return $this->instances['lessonModel'];
    }

    public function getPageModel(): Page
    {
        if (!isset($this->instances['pageModel'])) {
            $this->instances['pageModel'] = new Page($this->getPdo());
        }
        return $this->instances['pageModel'];
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

    public function getPostModel(): Post
    {
        if (!isset($this->instances['postModel'])) {
            $this->instances['postModel'] = new Post($this->getPdo());
        }
        return $this->instances['postModel'];
    }

    public function getUserModel(): User
    {
        if (!isset($this->instances['userModel'])) {
            $this->instances['userModel'] = new User($this->getPdo());
        }
        return $this->instances['userModel'];
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
                $this->getStripeClient(),
                $this->getUserModel()
            );
        }
        return $this->instances['paymentHandler'];
    }
}

