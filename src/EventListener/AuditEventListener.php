<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use App\Service\AuditLoggerService;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Constants;

#[AsDoctrineListener(event: Events::postPersist, priority: 0, connection: 'default')]
#[AsDoctrineListener(event: Events::postRemove, priority: 0, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 0, connection: 'default')]
// #[AsDoctrineListener(event: Events::preRemove, priority: 0, connection: 'default')]
class AuditEventListener
{
    public function __construct(private AuditLoggerService $logger, private ObjectNormalizer $normalizer, private $removals = []) {}

    public function postPersist(PostPersistEventArgs $args): void 
    {
         $entity = $args->getObject();
         $entityManager = $args->getObjectManager();
         $this->buildLoggingParameters($entity, 'insert', $entityManager);
    }

    public function postRemove(PostRemoveEventArgs $args): void 
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        $this->buildLoggingParameters($entity, 'delete', $entityManager);
    }

    public function postUpdate(PostUpdateEventArgs $args): void 
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        $this->buildLoggingParameters($entity, 'update', $entityManager);
    }

    public function preRemove(PreRemoveEventArgs $args): void 
    {
        $entity = $args->getObject();
        $prevEntity = $this->normalizer->normalize($entity, null, [
            ObjectNormalizer::IGNORED_ATTRIBUTES => ['users', 'subscription']
        ]);
        $this->removals[] = $prevEntity;
    }

    public function buildLoggingParameters($entity, string $action,  EntityManagerInterface $em) : void 
    {
        $entityClass = get_class($entity);

        // If its audit class , ignore 
        if ($entityClass === Constants::AUDITLOG_ENTITY_CLASS) {
            return;
        }

        $entityId = $entity->getId();
        $entityType = str_replace('App\Entity\\', '', $entityClass);

        $uow = $em->getUnitOfWork();

        switch ($action) {
            case 'insert':
                $entityData = $this->normalizer->normalize($entity, null, [
                    // ObjectNormalizer::IGNORED_ATTRIBUTES => ['users', 'subscription'],
                    'circular_reference_handler' => function ($object) {
                        return $object->getId(); // Return the ID of the object to break the circular reference
                    },
                ]);
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
            break;
        }
        
        $this->logger->log($entityType, $entityId, $action, $entityData);
    }
}