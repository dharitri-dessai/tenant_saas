<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Event\TenantCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidArgumentException;


class UserService
{
    
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }   

    /**
     * Create a new user with the provided data
     *
     * @param string $email User's email
     * @param string $plainPassword User's plain password
     * @param string $userType Type of user ('tenant' or 'user')
     * @param Tenant|null $tenant Tenant entity if user type is 'user'
     * @param string $firstname User's first name
     * @param string $lastname User's last name
     * @return User The created user entity
     * @throws CustomUserMessageAuthenticationException If tenant is required but not provided
     */
    public function createUser(
        string $email,
        string $plainPassword,
        string $userType,
        ?Tenant $tenant = null,
        string $firstname = '',
        string $lastname = '',
        string $subdomain = ''
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        
        // Set roles based on user type
        if ($userType === 'tenant') {
            $tenant = new Tenant();
            $tenant->setName($firstname . ' ' . $lastname);            
            $user->setTenant($tenant);
            $user->setRoles(['ROLE_TENANT_ADMIN']);

            // Check if the subdomain is unique and not blank
            if (!$subdomain) {
                throw new CustomUserMessageAuthenticationException('Subdomain is required.');
            }
             $existingTenant = $this->entityManager->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain]);
             if ($existingTenant) {
                 throw new CustomUserMessageAuthenticationException('The subdomain is already in use. Please choose a different subdomain.');
             }

             $tenant->setSubdomain($subdomain);

            // persist the tenant   
            $this->entityManager->persist($tenant);
        } else {
            if (!$tenant) {
                throw new CustomUserMessageAuthenticationException('Tenant is required for regular users.');
            }
            $user->setRoles(['ROLE_USER']);
            $user->setTenant($tenant);
        }
        
        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        
      
        // Persist the user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

       // call event 
       if ($userType === 'tenant') {
            $event = new TenantCreatedEvent($tenant, $user);
            $this->eventDispatcher->dispatch($event);
       }
       

        return $user;
    }
} 