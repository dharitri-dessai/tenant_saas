<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataFormatterPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container) : void {

        // Check if the central formatter manager service exists
        if (!$container->has('app.data_formatter_manager')) {
            return;
        }

        $defination = $container->findDefinition('app.data_formatter_manager');

        // find all tags with app.data_formatter
        $taggedServices = $container->findTaggedServiceIds('app.data_formatter');

        foreach ($taggedServices as $id => $tags) {
            
            // Add each tagged service to data formatter
            $defination->addMethodCall('addFormatter', [new Reference($id)]);

        }

    }
}