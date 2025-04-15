<?php

namespace App\Event;

use App\Entity\Tenant;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class TenantUpdatedEvent extends Event
{
    private array $changes = [];
    private bool $notify = false;

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

    public function addChange(string $field, $oldValue, $newValue): self
    {
        $this->changes[$field] = [
            'old' => $oldValue,
            'new' => $newValue,
        ];
        return $this;
    }

    public function shouldNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): self
    {
        $this->notify = $notify;
        return $this;
    }
} 