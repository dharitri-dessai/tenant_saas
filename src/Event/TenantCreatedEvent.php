<?php

namespace App\Event;

use App\Entity\Tenant;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class TenantCreatedEvent extends Event
{
    public function __construct(
        private Tenant $tenant,
        private User $user
    ) {
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function getUser(): User
    {
        return $this->user;
    }
} 