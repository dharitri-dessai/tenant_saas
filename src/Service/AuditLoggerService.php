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

    public function log(string $entityType, string $entityId, string $action, array $eventData) 
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

    public function addPreviousEntity($prevEntity) : void 
    {
        $this->removals[] = $prevEntity;
    }

    public function buildLoggingParameters($entity, $action,  EntityManagerInterface $em) : void 
    {
        $entityClass = get_class($entity);

        // If its audit class , ignore 
        if ($entityClass === 'App\Entity]AuditLog') {
            return;
        }

        $entityId = $entity->getId();
        $entityType = str_replace('App\Entity\\', '', $entityClass);

        $uow = $em->getUnitOfWork();

        switch ($action) {
            case 'insert':
                $entityData = $this->normalizer->normalize($entity);
            break;

            case 'update':
                $entityData = $uow->getEntityChangeSet($entity);
                foreach ($entityData as $field => $change) {
                    $entityData[$field] = [
                        'from' => $change[0],
                        'to' => $change[1],
                    ];
                }
            break;

            case 'delete':
                // get entity from the temporary array.
                $entityData = array_pop($this->removals);
                $entityId = $entityData['id'];
            break;

            default:
                throw new \InvalidArgumentException("Unsupported action: $action");

        }
        
        $this->log($entityType, $entityId, $action, $entityData);
    }
}