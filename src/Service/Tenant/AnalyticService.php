<?php

namespace App\Service\Tenant;

class AnalyticsService
{
    public function processData(array $config): void
    {
        // Example: Process tenant-specific data
        echo sprintf(
            "Processing data for %s with color %s\n",
            $config['name'],
            $config['color']
        );
    }
}