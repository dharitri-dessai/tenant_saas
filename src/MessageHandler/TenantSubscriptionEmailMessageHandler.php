<?php

namespace App\MessageHandler;

use App\Message\TenantSubscriptionEmailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[AsMessageHandler]
final class TenantSubscriptionEmailMessageHandler{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        
    }

    public function __invoke(TenantSubscriptionEmailMessage $message)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['tenant' => $message->getTenantId()]);
      
        if (!$user instanceOf User) {
            throw new \RuntimeException(sprintf('Tenant with ID %d not found.', $message->getTenantId()));
        } else {
            // Send Mail
            $email = $user->getEmail();
        }
    }
}
