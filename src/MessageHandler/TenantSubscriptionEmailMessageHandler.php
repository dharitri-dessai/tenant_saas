<?php

namespace App\MessageHandler;

use App\Message\TenantSubscriptionEmailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Utils\Constants;
use PHPUnit\TextUI\XmlConfiguration\Constant;

#[AsMessageHandler]
final class TenantSubscriptionEmailMessageHandler{

    public function __construct(private readonly EntityManagerInterface $entityManager, private MailerInterface $mailer)
    {
        
    }

    public function __invoke(TenantSubscriptionEmailMessage $message)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['tenant' => $message->getTenantId()]);
      
        if (!$user instanceOf User) {
            throw new \RuntimeException(sprintf('Tenant with ID %d not found.', $message->getTenantId()));
        } else {
            // Send Mail
            $emailAddress = $user->getEmail();
           
            $email = (new Email())
                ->from(Constants::SUBSCRPTION_MAIL_FROM)
                ->to(Constants::SUBSCRPTION_MAIL_DUMMY_TO) //$emailAddress
                ->subject(Constants::SUBSCRPTION_MAIL_SUBJECT)
                ->html($this->getEmailTemplate($user));

            $this->mailer->send($email);
            
        }
    }

    private function getEmailTemplate(User $user): string
    {
        return <<<HTML
                <h1>Welcome to Our SaaS Platform!</h1>
                <p>Dear {$user->getFirstName()} {$user->getLastName()},</p>
                <p>Your tenant subscription has been updated successfully and is now {$user->getTenant()?->getSubscriptionStatus()}.</p>
                <p>Your Subscription: {$user->getTenant()?->getSubscription()?->getPlanId()}</p>
                <p>If you have any questions, please don't hesitate to contact our support team.</p>
                <p>Best regards,<br>Your SaaS Platform Team</p>
            HTML;
        }
}
