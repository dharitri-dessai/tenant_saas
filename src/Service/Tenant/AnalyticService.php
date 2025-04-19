<?php

namespace App\Service\Tenant;

class AnalyticsService
{
    public function processData(array $config): array
    {      
        // Example: Process tenant-specific data
        return [
            'name' => $config['name'],
            'color' => $config['color']
        ];
    }
}