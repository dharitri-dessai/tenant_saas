<?php

namespace App\Service;

use App\Entity\Tenant;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TenantService
{
    private $entityManager;
    private $validator;
    private $slugger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->slugger = new AsciiSlugger();
    }

    /**
     * Create a new tenant with the provided data
     *
     * @param string $name Tenant name
     * @param User|null $adminUser Admin user for this tenant (optional)
     * @param string|null $subscriptionStatus Subscription status (optional)
     * @return Tenant The created tenant entity
     * @throws \InvalidArgumentException If validation fails
     */
    public function createTenant(
        string $name,
        ?User $adminUser = null,
        ?string $subscriptionStatus = 'trial'
    ): Tenant {
        // Create a new tenant
        $tenant = new Tenant();
        $tenant->setName($name);
        
        // Generate a subdomain from the name
        $subdomain = $this->generateSubdomain($name);
        $tenant->setSubdomain($subdomain);
        
        // Set subscription status
        $tenant->setSubscriptionStatus($subscriptionStatus);
        
        // Set active status
        $tenant->setIsActive(true);
        
        // Associate admin user if provided
        if ($adminUser) {
            // Since addUser is commented out in the Tenant entity,
            // we'll set the tenant on the user directly
            $adminUser->setTenant($tenant);
        }
        
        // Validate the tenant
        $errors = $this->validator->validate($tenant);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \InvalidArgumentException(implode(', ', $errorMessages));
        }
        
        // Persist the tenant
        $this->entityManager->persist($tenant);
        $this->entityManager->flush();
        
        return $tenant;
    }
    
    /**
     * Generate a unique subdomain from the tenant name
     *
     * @param string $name Tenant name
     * @return string Generated subdomain
     */
    private function generateSubdomain(string $name): string
    {
        // Convert to lowercase and replace spaces with hyphens
        $subdomain = strtolower($name);
        $subdomain = $this->slugger->slug($subdomain);
        
        // Check if subdomain already exists
        $existingTenant = $this->entityManager->getRepository(Tenant::class)
            ->findOneBy(['subdomain' => $subdomain]);
        
        // If subdomain exists, append a random number
        if ($existingTenant) {
            $subdomain .= '-' . rand(1000, 9999);
        }
        
        return $subdomain;
    }
    
    /**
     * Activate or deactivate a tenant
     *
     * @param Tenant $tenant The tenant to update
     * @param bool $active Whether the tenant should be active
     * @return Tenant The updated tenant
     */
    public function setTenantActiveStatus(Tenant $tenant, bool $active): Tenant
    {
        $tenant->setIsActive($active);
        $this->entityManager->flush();
        
        return $tenant;
    }
    
    /**
     * Update a tenant's subscription status
     *
     * @param Tenant $tenant The tenant to update
     * @param string $status The new subscription status
     * @return Tenant The updated tenant
     */
    public function updateSubscriptionStatus(Tenant $tenant, string $status): Tenant
    {
        $tenant->setSubscriptionStatus($status);
        $this->entityManager->flush();
        
        return $tenant;
    }
} 