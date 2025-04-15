<?php

namespace App\EventSubscriber;

use App\Entity\Tenant;
use App\Event\TenantCreatedEvent;
use App\Event\TenantUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TenantSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TenantCreatedEvent::class => 'onTenantCreated',
            TenantUpdatedEvent::class => 'onTenantUpdated',
        ];
    }

    public function onTenantCreated(TenantCreatedEvent $event): void
    {
        // The tenant and user should already be created and persisted by the time this event is dispatched
        // This event subscriber is for post-creation actions like sending welcome emails
        // The user and tenant creation should happen in the controller or a dedicated service
        $tenant = $event->getTenant();
        
        // Send welcome email
        $email = (new Email())
            ->from('noreply@yourdomain.com')
            ->to($event->getUser()->getEmail())
            ->subject('Welcome to Our SaaS Platform')
            ->html($this->getWelcomeEmailTemplate($tenant));

        $this->mailer->send($email);
    }

    public function onTenantUpdated(TenantUpdatedEvent $event): void
    {
        $tenant = $event->getTenant();
       
        // Send notification email if needed
        if ($event->shouldNotify()) {
            $email = (new Email())
                ->from('noreply@yourdomain.com')
                ->to($event->getUser()->getEmail())
                ->subject('Your Tenant Account Has Been Updated')
                ->html($this->getUpdateEmailTemplate($tenant));

            $this->mailer->send($email);
        }
    }

    private function getWelcomeEmailTemplate(Tenant $tenant): string
    {
        return <<<HTML
            <h1>Welcome to Our SaaS Platform!</h1>
            <p>Dear {$tenant->getName()},</p>
            <p>Thank you for choosing our platform. Your tenant account has been created successfully.</p>
            <p>Your subdomain: {$tenant->getSubdomain()}</p>
            <p>If you have any questions, please don't hesitate to contact our support team.</p>
            <p>Best regards,<br>Your SaaS Platform Team</p>
        HTML;
    }

    private function getUpdateEmailTemplate(Tenant $tenant): string
    {
        return <<<HTML
            <h1>Tenant Account Update</h1>
            <p>Dear {$tenant->getName()},</p>
            <p>Your tenant account has been updated successfully.</p>
            <p>Current status: {$tenant->getSubscriptionStatus()}</p>
            <p>If you have any questions, please don't hesitate to contact our support team.</p>
            <p>Best regards,<br>Your SaaS Platform Team</p>
        HTML;
    }
} 