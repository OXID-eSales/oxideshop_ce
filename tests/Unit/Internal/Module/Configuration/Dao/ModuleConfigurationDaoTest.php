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
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationDaoTest extends TestCase
{
    private $shopId = 1;
    private $environment = 'test';

    public function testGetter()
    {
        $ModuleConfigurationDao = new ModuleConfigurationDao(
            $this->getProjectConfigurationDao(),
            $this->getContext()
        );

        $moduleConfiguration = $ModuleConfigurationDao->get('expectedModuleId', $this->shopId);

        $this->assertSame(
            'expectedModuleId',
            $moduleConfiguration->getId()
        );
    }

    private function getProjectConfigurationDao(): ProjectConfigurationDaoInterface
    {
        $expectedModuleConfiguration = new ModuleConfiguration();
        $expectedModuleConfiguration->setId('expectedModuleId');

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($expectedModuleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration($this->shopId, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration($this->environment, $environmentConfiguration);

        $projectConfigurationDao = $this->getMockBuilder(ProjectConfigurationDaoInterface::class)->getMock();
        $projectConfigurationDao
            ->method('getConfiguration')
            ->willReturn($projectConfiguration);

        return $projectConfigurationDao;
    }

    private function getContext(): ContextInterface
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getEnvironment')
            ->willReturn($this->environment);

        return $context;
    }
}
