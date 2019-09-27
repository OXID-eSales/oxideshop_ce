<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ActiveClassExtensionChainResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
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

        $shopConfigurationDao = $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock();
        $shopConfigurationDao
            ->method('get')
            ->willReturn($shopConfiguration);

        $moduleStateService = $this->getMockBuilder(ModuleStateServiceInterface::class)->getMock();
        $moduleStateService
            ->method('isActive')
            ->willReturnMap([
                ['activeModuleName', 1, true],
                ['activeModuleName2', 1, true],
                ['notActiveModuleName', 1, false],
            ]);

        $classExtensionChainService = new ActiveClassExtensionChainResolver(
            $shopConfigurationDao,
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

        foreach ($extensions as $classNamespace => $moduleNamespace) {
            $classExtensions = new ClassExtension($classNamespace, $moduleNamespace);

            $moduleConfiguration
                ->setId($moduleName)
                ->addClassExtension($classExtensions);
        }

        return $moduleConfiguration;
    }
}
