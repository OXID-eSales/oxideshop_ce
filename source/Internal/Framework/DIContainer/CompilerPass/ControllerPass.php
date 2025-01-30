<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('oxid.controller');
        $controllersMap = [];

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $controllersMap[$attributes['controller_key']] = $id;
            }
        }

        $container->setParameter('oxid.controllers_map', $controllersMap);
    }
}
