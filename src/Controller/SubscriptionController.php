<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {
    }

    #[Route('/new', name: 'app_subscription_new', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TENANT_ADMIN")'))]
    public function new(Request $request): Response
    {
        $form = $this->createForm(SubscriptionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var User $user */
            $user = $this->getUser();
            $tenant = $user->getTenant();

            try {
                $subscription = $this->subscriptionService->createSubscription(
                    $tenant,
                    $data['planId']
                );

                $this->addFlash('success', 'Subscription created successfully!');
                return $this->redirectToRoute('app_subscription_show', ['id' => $subscription->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error creating subscription: ' . $e->getMessage());
            }
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_subscription_show', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TENANT_ADMIN")'))]
    public function show(Subscription $subscription): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('subscription/show.html.twig', [
            'subscription' => $subscription,
            'tenant' => $user->getTenant(),
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_subscription_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function cancel(Subscription $subscription): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $this->subscriptionService->cancelSubscription($subscription);
            $this->addFlash('success', 'Subscription cancelled successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error cancelling subscription: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_subscription_show', ['id' => $subscription->getId()]);
    }

    #[Route('/{id}/update', name: 'app_subscription_update', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TENANT_ADMIN")'))]
    public function update(Request $request, Subscription $subscription): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $form = $this->createForm(SubscriptionType::class, [
            'planId' => $subscription->getPlanId(),
            'status' => $subscription->getStatus(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->subscriptionService->updateSubscription($subscription, $data['planId'], $data['status']);
                $this->addFlash('success', 'Subscription updated successfully!');

                $this->subscriptionService->dispatchMessage($user->getTenant()?->getId());

                return $this->redirectToRoute('app_subscription_show', ['id' => $subscription->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error updating subscription: ' . $e->getMessage());
            }
        }

        return $this->render('subscription/update.html.twig', [
            'subscription' => $subscription,
            'tenant' => $user->getTenant(),
            'form' => $form->createView(),
        ]);
    }
} 