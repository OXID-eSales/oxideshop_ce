<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\BootstrapContainer;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @internal
 */
final class BootstrapContainerTest extends UnitTestCase
{
    public function testContainerBuilding(): void
    {
        $container = BootstrapContainerFactory::getBootstrapContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testContainerProvidesBootstrapServices(): void
    {
        $container = BootstrapContainerFactory::getBootstrapContainer();

        $this->assertInstanceOf(
            BasicContextInterface::class,
            $container->get(BasicContextInterface::class)
        );
    }

    public function testContainerDoesNotProvideNotBootstrapServices(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $container = BootstrapContainerFactory::getBootstrapContainer();
        $container->get(ProductRatingBridgeInterface::class);
    }
}
