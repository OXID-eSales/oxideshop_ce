<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @internal
 */
trait ContainerTrait
{
    protected function get(string $serviceId)
    {
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();
        $this->setContainerDefinitionToPublic($container, $serviceId);
        $container->compile();
        return $container->get($serviceId);
    }

    private function setContainerDefinitionToPublic(SymfonyContainerBuilder $container, string $definitionId)
    {
        $definition = $container->getDefinition($definitionId);
        $definition->setPublic(true);
        $container->setDefinition($definitionId, $definition);
    }
}
