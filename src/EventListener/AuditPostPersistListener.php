<?php

// namespace App\Listener;

// use Doctrine\ORM\Events;
// use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
// use Doctrine\ORM\Event\PostPersistEventArgs;
// use App\Service\AuditLoggerService;

// #[AsDoctrineListener(event: Events::postPersist, priority: 0, connection: 'default')]
// class AuditPostPersistListener
// {
//     public function __construct(private AuditLoggerService $logger) {}

//     public function postPersist(PostPersistEventArgs $args): void 
//     {
//         $entity = $args->getObject();
//         $entityManager = $args->getObjectManager();
//         $this->logger->buildLoggingParameters($entity, 'insert', $entityManager);
//     }
// }