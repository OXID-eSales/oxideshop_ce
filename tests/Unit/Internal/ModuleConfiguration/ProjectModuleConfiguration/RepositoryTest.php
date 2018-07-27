<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ModuleConfiguration\ProjectModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration\ConfigurationFactory;
use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration\ConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration\ConfigurationMapperInterface;
use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration\DataStorageInterface;
use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration\Repository;

/**
 * @internal
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfiguration()
    {
        $factory = new ConfigurationFactory();

        $dataStorage = $this
            ->getMockBuilder(DataStorageInterface::class)
            ->getMock();

        $mapper = $this
            ->getMockBuilder(ConfigurationMapperInterface::class)
            ->getMock();

        $mapper
            ->method('getConfiguration')
            ->willReturn(ConfigurationInterface::class);

        $repository = new Repository(
            $factory,
            $dataStorage,
            $mapper
        );

        $repository->getConfiguration();
    }
}
