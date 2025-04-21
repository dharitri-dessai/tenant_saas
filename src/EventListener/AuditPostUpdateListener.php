<?php

// namespace App\Listener;

// use Doctrine\ORM\Events;
// use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
// use Doctrine\ORM\Event\PostUpdateEventArgs;
// use App\Service\AuditLoggerService;

// #[AsDoctrineListener(event: Events::postUpdate)]
// class AuditPostUpdateListener
// {
//     public function __construct(private AuditLoggerService $logger) {}

//     public function postUpdate(PostUpdateEventArgs $args): void 
//     {
//         $entity = $args->getObject();
//         $entityManager = $args->getObjectManager();
//         $this->logger->buildLoggingParameters($entity, 'update', $entityManager);
//     }
// }