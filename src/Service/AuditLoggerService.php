<?php

namespace App\Service;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuditLoggerService
{
    public function __construct(
        private EntityManagerInterface $entityManager, 
        private Security $security, 
        private RequestStack $requestStack,
        private NormalizerInterface $normalizer,
        private $removals = [])
    { }

    public function log(string $entityType, int $entityId, string $action, array $eventData) 
    {
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $log = new AuditLog;
       
        $log->setEntityId($entityId);
        $log->setEntityType($entityType);
        $log->setAction($action);
        $log->setEventData($eventData);
        $log->setUser($user);
        $log->setRequestRoute($request->get('_route'));
        $log->setIpAddress($request->getClientIp());
        $log->setCreatedAt(new \DateTimeImmutable);

        $this->entityManager->persist($log);
        $this->entityManager->flush();

    }
}