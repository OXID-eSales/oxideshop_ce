<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ActiveClassExtensionChainResolver;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
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

        $classExtensionChain = new ClassExtensionsChain();
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
        $shopConfiguration->setClassExtensionsChain($classExtensionChain);

        $shopConfiguration
            ->addModuleConfiguration($activeModuleConfiguration1)
            ->addModuleConfiguration($activeModuleConfiguration2)
            ->addModuleConfiguration($notActiveModuleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('dev', $environmentConfiguration);

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
            $moduleStateService,
            new ContextStub()
        );

        $expectedChain = new ClassExtensionsChain();
        $expectedChain
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
            ->addSetting(
                new ModuleSetting(ModuleSetting::CLASS_EXTENSIONS, $extensions)
            );
        return $moduleConfiguration;
    }
}
