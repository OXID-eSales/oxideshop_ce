<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ActiveClassExtensionChainResolver;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ActiveClassExtensionChainResolverTest extends TestCase
{
    public function testActiveExtensionChainGetter()
    {
        $activeModuleConfiguration1 = $this->getModuleConfiguration('activeModuleName', [
            'shopClassNamespace'        => 'activeModuleExtensionClass',
            'anotherShopClassNamespace' => 'activeModuleExtensionClass',
        ]);

        $activeModuleConfiguration2 = $this->getModuleConfiguration('activeModuleName2', [
            'shopClassNamespace'        => 'activeModule2ExtensionClass',
            'anotherShopClassNamespace' => 'activeModule2ExtensionClass',
        ]);

        $notActiveModuleConfiguration = $this->getModuleConfiguration('notActiveModuleName', [
            'shopClassNamespace'        => 'notActiveModuleExtensionClass',
            'anotherShopClassNamespace' => 'notActiveModuleExtensionClass',
        ]);

        $classExtensionChain = new Chain();
        $classExtensionChain->setName('classExtensions');
        $classExtensionChain->setChain([
            'shopClassNamespace' => [
                'activeModule2ExtensionClass',
                'activeModuleExtensionClass',
                'notActiveModuleExtensionClass',
            ],
            'anotherShopClassNamespace' => [
                'activeModuleExtensionClass',
                'notActiveModuleExtensionClass',
                'activeModule2ExtensionClass',
            ],
        ]);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->setChain('classExtensions', $classExtensionChain);

        $shopConfiguration->setModuleConfiguration('activeModuleName', $activeModuleConfiguration1);
        $shopConfiguration->setModuleConfiguration('activeModuleName2', $activeModuleConfiguration2);
        $shopConfiguration->setModuleConfiguration('notActiveModuleName', $notActiveModuleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->setShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setEnvironmentConfiguration('dev', $environmentConfiguration);

        $projectConfigurationDao = $this->getMockBuilder(ProjectConfigurationDaoInterface::class)->getMock();
        $projectConfigurationDao
            ->method('getConfiguration')
            ->willReturn($projectConfiguration);

        $moduleStateService = $this->getMockBuilder(ModuleStateServiceInterface::class)->getMock();
        $moduleStateService
            ->method('isActive')
            ->willReturnMap([
                ['activeModuleName', 1, true],
                ['activeModuleName2', 1, true],
                ['notActiveModuleName', 1, false],
            ]);

        $classExtensionChainService = new ActiveClassExtensionChainResolver(
            $projectConfigurationDao,
            $moduleStateService
        );

        $expectedChain = new Chain();
        $expectedChain
            ->setName('classExtensions')
            ->setChain(
                [
                    'shopClassNamespace' => [
                        'activeModule2ExtensionClass',
                        'activeModuleExtensionClass',
                    ],
                    'anotherShopClassNamespace' => [
                        'activeModuleExtensionClass',
                        'activeModule2ExtensionClass',
                    ],
                ]
            );

        $this->assertEquals(
            $expectedChain,
            $classExtensionChainService->getActiveExtensionChain(1)
        );
    }

    private function getModuleConfiguration(string $moduleName, array $extensions): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($moduleName)
            ->setSetting(
                new ModuleSetting(ModuleSetting::CLASS_EXTENSIONS, $extensions)
            );
        return $moduleConfiguration;
    }
}
