<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\Provider;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Provider\ModuleConfigurationProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationProviderTest extends TestCase
{
    public function testConfigurationGetter()
    {
        $expectedModuleConfiguration = new ModuleConfiguration();

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->setModuleConfiguration('testModuleId', $expectedModuleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->setShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setEnvironmentConfiguration('prod', $environmentConfiguration);

        $projectConfigurationDao = $this->getMockBuilder(ProjectConfigurationDaoInterface::class)->getMock();
        $projectConfigurationDao
            ->method('getConfiguration')
            ->willReturn($projectConfiguration);

        $moduleConfigurationProvider = new ModuleConfigurationProvider($projectConfigurationDao);

        $this->assertSame(
            $expectedModuleConfiguration,
            $moduleConfigurationProvider->getModuleConfiguration(
                'testModuleId',
                'prod',
                1
            )
        );
    }
}
