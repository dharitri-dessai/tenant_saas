<?php

//namespace App\Listener;

// use Doctrine\ORM\Events;
// use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
// use Doctrine\ORM\Event\PostRemoveEventArgs;
// use App\Service\AuditLoggerService;

// #[AsDoctrineListener(event: Events::postRemove)]
// class AuditPostRemoveListener
// {
//     public function __construct(private AuditLoggerService $logger) {}

//     public function postRemove(PostRemoveEventArgs $args): void 
//     {
//         $entity = $args->getObject();
//         $entityManager = $args->getObjectManager();
//         $this->logger->buildLoggingParameters($entity, 'delete', $entityManager);
//     }
// }