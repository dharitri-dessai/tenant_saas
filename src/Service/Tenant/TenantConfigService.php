<?php

namespace App\Service\Tenant;

class TenantConfigService
{
    private array $tenantConfigs = [
        1 => [
            'name' => 'Tenant 1',
            'color' => '#FF5733',
            'users' => [
                'total' => 100,
                'active' => 80,
                'inactive' => 20,
            ],
        ],
        2 => [
            'name' => 'Tenant 2',
            'color' => '#33FF57',
            'users' => [
                'total' => 50,
                'active' => 40,
                'inactive' => 10,
            ],
        ],
    ];

    public function getConfig(int $tenantId): array
    {
        return $this->tenantConfigs[$tenantId] ?? [];
    }
}