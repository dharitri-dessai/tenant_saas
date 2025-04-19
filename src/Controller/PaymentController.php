<?php

namespace App\Controller;

use App\Service\Payment\StripePaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/new', name: 'app_create_payment', methods: ['GET'])]
    public function createPayment(StripePaymentService $stripePaymentService): Response
    {
        $amount = 50.00;
        $currency = 'usd';
        $description = 'Subscription Payment for Tenant';

        try {
            $paymentIntent = $stripePaymentService->createPaymentIntent($amount, $currency, $$description);

        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('payment/index.html.twig', [
            'paymentIntent' => $paymentIntent,
        ]);
    }
}
