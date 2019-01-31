<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Service\ModuleConfigurationMergingService;
use PHPUnit\Framework\TestCase;

class ModuleConfigurationMergingServiceTest extends TestCase
{
    public function testMergeNewModuleConfiguration()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('newModule');

        $moduleConfigurationMergingService = new ModuleConfigurationMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(new ShopConfiguration(), $moduleConfiguration);

        $this->assertSame(
            $moduleConfiguration,
            $shopConfiguration->getModuleConfiguration('newModule')
        );
    }

    public function testExtensionClassAppendToChainAfterMergingNewModuleConfiguration()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('newModule');
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::CLASS_EXTENSIONS,
            [
                'shopClass' => 'testModuleClassExtendsShopClass',
            ]
        ));

        $shopConfigurationWithChain = new ShopConfiguration();
        $chain = new Chain();
        $chain
            ->setName(Chain::CLASS_EXTENSIONS)
            ->setChain([
                'shopClass'             => ['alreadyInstalledShopClass', 'anotherAlreadyInstalledShopClass'],
                'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
            ]);

        $shopConfigurationWithChain->addChain($chain);

        $moduleConfigurationMergingService = new ModuleConfigurationMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge($shopConfigurationWithChain, $moduleConfiguration);

        $this->assertSame(
            [
                'shopClass'             => [
                    'alreadyInstalledShopClass',
                    'anotherAlreadyInstalledShopClass',
                    'testModuleClassExtendsShopClass',
                ],
                'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
            ],
            $shopConfiguration->getChain(Chain::CLASS_EXTENSIONS)->getChain()
        );
    }

    public function testMergeModuleConfigurationOfAlreadyInstalledModule()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');

        $moduleConfigurationMergingService = new ModuleConfigurationMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(
            $this->getShopConfigurationWithAlreadyInstalledModule(),
            $moduleConfiguration
        );

        $this->assertEquals(
            $moduleConfiguration,
            $shopConfiguration->getModuleConfiguration('installedModule')
        );
    }

    public function testExtensionClassChainUpdatedAfterMergingAlreadyInstalledModule()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::CLASS_EXTENSIONS,
            [
                'shopClass1' => 'extension1ToStayInNewModuleConfiguration',
                'shopClass2' => 'extension5',
                'shopClass5' => 'extension6'
            ]
        ));

        $moduleConfigurationMergingService = new ModuleConfigurationMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(
            $this->getShopConfigurationWithAlreadyInstalledModule(),
            $moduleConfiguration
        );

        $this->assertEquals(
            [
                'shopClass1' => ['someOtherExtension1', 'extension1ToStayInNewModuleConfiguration'],
                'shopClass2' => ['someOtherExtension2', 'extension5', 'someOtherExtension3'],
                'shopClass3' => ['someOtherExtension4'],
                'shopClass5' => ['extension6']
            ],
            $shopConfiguration->getChain(Chain::CLASS_EXTENSIONS)->getChain()
        );
    }

    public function testShopModuleSettingUpdatedAfterMergingAlreadyInstalledModule()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');
        $moduleConfiguration->addSetting(
            new ModuleSetting(
                ModuleSetting::SHOP_MODULE_SETTING,
                [
                    [
                        'name'     => 'existingValueIsTaken1',
                        'group'    => 'oldGroup',
                        'type'     => 'int',
                        'position' => '100500'
                    ],
                    [
                        'name'     => 'withTypeToChange',
                        'type'     => 'bool',
                        'position' => '100500',
                        'value'    => 'true',
                    ],
                    [
                        'name'     => 'existingValueIsTaken2',
                        'type'     => 'str',
                        'position' => '100500'
                    ],
                    [
                        'name'        => 'existingValueIsTaken3',
                        'type'        => 'select',
                        'constraints' => ['1', '2', '3'],
                        'position'    => '100500',
                    ],
                    [
                        'name'        => 'existingValueNotTaken',
                        'type'        => 'select',
                        'constraints' => ['1', '2'],
                        'position'    => '100500',
                        'value'       => '2'
                    ],
                    [
                        'name'          => 'completeNewOne',
                        'type'          => 'string',
                        'position'      => '100500',
                        'value' => 'myValue'
                    ]
                ]
            )
        );

        $moduleConfigurationMergingService = new ModuleConfigurationMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(
            $this->getShopConfigurationWithAlreadyInstalledModule(),
            $moduleConfiguration
        );

        $mergedModuleConfiguration = $shopConfiguration->getModuleConfiguration('installedModule');

        $this->assertEquals(
            [
                [
                    'name'     => 'existingValueIsTaken1',
                    'group'    => 'oldGroup',
                    'type'     => 'int',
                    'position' => '100500',
                    'value'    => '1'
                ],
                [
                    'name'     => 'withTypeToChange',
                    'type'     => 'bool',
                    'position' => '100500',
                    'value'    => 'true'
                ],
                [
                    'name'     => 'existingValueIsTaken2',
                    'type'     => 'str',
                    'position' => '100500',
                    'value'    => 'keep'
                ],
                [
                    'name'        => 'existingValueIsTaken3',
                    'type'        => 'select',
                    'constraints' => ['1', '2', '3'],
                    'position'    => '100500',
                    'value'       => '3',
                ],
                [
                    'name'        => 'existingValueNotTaken',
                    'type'        => 'select',
                    'constraints' => ['1', '2'],
                    'position'    => '100500',
                    'value'       => '2',
                ],
                [
                    'name'     => 'completeNewOne',
                    'type'     => 'string',
                    'position' => '100500',
                    'value'    => 'myValue'
                ]
            ],
            $mergedModuleConfiguration->getSetting(ModuleSetting::SHOP_MODULE_SETTING)->getValue()
        );
    }

    private function getShopConfigurationWithAlreadyInstalledModule(): ShopConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::CLASS_EXTENSIONS,
            [
                'shopClass1'            => 'extension1ToStayInNewModuleConfiguration',
                'shopClass2'            => 'extension2ToBeChanged',
                'shopClass3'            => 'extension3ToBeDeleted',
                'shopClass4ToBeDeleted' => 'extension4ToBeDeleted'
            ]
        ));

        $moduleConfiguration->addSetting(
            new ModuleSetting(
                ModuleSetting::SHOP_MODULE_SETTING,
                [
                    [
                        'name'     => 'existingValueIsTaken1',
                        'group'    => 'oldGroup',
                        'type'     => 'int',
                        'position' => '100500',
                        'value'    => '1',
                    ],
                    [
                        'name'     => 'withTypeToChange',
                        'type'     => 'str',
                        'position' => '100500',
                        'value'    => 'toDelete',
                    ],
                    [
                        'name'     => 'existingValueIsTaken2',
                        'type'     => 'str',
                        'position' => '100500',
                        'value'    => 'keep',
                    ],
                    [
                        'name'        => 'existingValueIsTaken3',
                        'type'        => 'select',
                        'constraints' => ['1', '2', '3'],
                        'position'    => '100500',
                        'value'       => '3',
                    ],
                    [
                        'name'        => 'existingValueNotTaken',
                        'type'        => 'select',
                        'constraints' => ['1', '2', '3'],
                        'position'    => '100500',
                        'value'       => '3',
                    ],
                    [
                        'name'        => 'willBeDeleted',
                        'type'        => 'str',
                        'position'    => '100500',
                        'value'       => 'myValue1',
                    ]
                ]
            )
        );

        $chain = new Chain();
        $chain
            ->setName(Chain::CLASS_EXTENSIONS)
            ->setChain([
                'shopClass1'            => ['someOtherExtension1', 'extension1ToStayInNewModuleConfiguration'],
                'shopClass2'            => ['someOtherExtension2', 'extension2ToBeChanged', 'someOtherExtension3'],
                'shopClass3'            => ['extension3ToBeDeleted', 'someOtherExtension4'],
                'shopClass4ToBeDeleted' => ['extension4ToBeDeleted']
            ]);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);
        $shopConfiguration->addChain($chain);

        return $shopConfiguration;
    }
}
