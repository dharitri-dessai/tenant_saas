<?php

namespace App\Service\Payment;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class StripePaymentService 
{
    public function __construct(private HttpClientInterface $httpClient, private string $stripeApiKey)
    {
    }

    public function createPaymentIntent(float $amount, string $currency, string $description) : array {
        $response = $this->httpClient->request('POST', 'https://api.stripe.com/v1/payment_intents', [
            'auth_basic' => [$this->stripeApiKey, ''],
            'body' => [
                'amount' => $amount * 100, // Stripe expects the amount in cents
                'currency' => $currency,
                'description' => $description,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to create payment');
        }

        return $response->toArray();
    }
} 