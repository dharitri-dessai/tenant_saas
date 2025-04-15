<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\Tenant;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubscriptionRepository $subscriptionRepository,
    ) {
    }

    public function createSubscription(Tenant $tenant, string $priceId): Subscription
    {
        try {            
            // Create local subscription record
            $subscription = new Subscription();
            $subscription->setTenant($tenant);
            $subscription->setPlanId($priceId);
            $subscription->setStatus($_ENV['SUBSCRIPTION_STATUS_ACTIVE']);
            $subscription->setCurrentPeriodStart(new \DateTimeImmutable('@' . time()));
            $subscription->setCurrentPeriodEnd(new \DateTimeImmutable('@' . time() + 3600 * 24 * 30));

            $this->entityManager->persist($subscription);
            $this->entityManager->flush();

            return $subscription;
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(Subscription $subscription): void
    {
        try {
            $subscription->setStatus('canceled');
            $this->entityManager->flush();
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    public function updateSubscription(Subscription $subscription, string $newPlanId, string $newStatus): void
    {
        try {
            $subscription->setPlanId($newPlanId);
            $subscription->setStatus($newStatus);
            $subscription->setCurrentPeriodStart(new \DateTimeImmutable('@' . time()));
            $subscription->setCurrentPeriodEnd(new \DateTimeImmutable('@' . time() + 3600 * 24 * 30));

            $this->entityManager->flush();
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to update subscription: ' . $e->getMessage());
        }
    }
} 