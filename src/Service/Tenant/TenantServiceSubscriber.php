<?php

namespace App\Service\Tenant;

use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Psr\Container\ContainerInterface;

class TenantServiceSubscriber implements ServiceSubscriberInterface
{
    private ContainerInterface $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices(): array
    {
        return [
            TenantConfigService::class,
            AnalyticsService::class,
        ];
    
    }

    public function processTenantData(int $tenantId): array
    {
        // Fetch tenant-specific configuration
        $tenantConfigService = $this->locator->get(TenantConfigService::class);
        $config = $tenantConfigService->getConfig($tenantId);

        if (empty($config)) {
            throw new \RuntimeException(sprintf('Configuration for tenant ID %d is missing or invalid.', $tenantId));
        }

        // Process tenant data using the analytics service
        $analyticsService = $this->locator->get(AnalyticsService::class);
        $processedData = $analyticsService->processData($config);

        // Pass the processed data to the Twig template
        return $processedData;
    }
}