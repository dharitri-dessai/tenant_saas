<?php

// namespace App\Listener;

// use Doctrine\ORM\Events;
// use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
// use Doctrine\ORM\Event\PreRemoveEventArgs;
// use App\Service\AuditLoggerService;
// use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

// #[AsDoctrineListener(event: Events::preRemove)]
// class AuditPreRemoveListener
// {
//     public function __construct(private AuditLoggerService $logger, private NormalizerInterface $normalizer, private $removals = []) {}

//     public function preRemove(PreRemoveEventArgs $args): void 
//     {
//         $entity = $args->getObject();
//         $prevEntity = $this->normalizer->normalize($entity);
//         $this->logger->addPreviousEntity($prevEntity);
//     }
// }