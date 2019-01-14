<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\BootstrapContainer;

use OxidEsales\EshopCommunity\Internal\Application\BootstrapContainer\BootstrapContainerFactory;
use OxidEsales\TestingLibrary\UnitTestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class BootstrapContainerTest extends UnitTestCase
{

    public function testContainerBuilding()
    {
        $container = BootstrapContainerFactory::getBootstrapContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

}
