<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ModuleConfigurationMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;

class ModuleConfigurationMergingServiceTest extends TestCase
{
    use ContainerTrait;

    public function testMergeNewModuleConfiguration()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('newModule');

        $moduleConfigurationMergingService = $this->getMergingService();
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
        $moduleConfiguration->addClassExtension(
            new ClassExtension(
                'shopClass',
                'testModuleClassExtendsShopClass'
            )
        );

        $shopConfigurationWithChain = new ShopConfiguration();
        $chain = new ClassExtensionsChain();
        $chain->setChain([
            'shopClass'             => ['alreadyInstalledShopClass', 'anotherAlreadyInstalledShopClass'],
            'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
        ]);

        $shopConfigurationWithChain->setClassExtensionsChain($chain);

        $moduleConfigurationMergingService = $this->getMergingService();
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
            $shopConfiguration->getClassExtensionsChain()->getChain()
        );
        $this->assertEquals(
            $moduleConfiguration,
            $shopConfiguration->getModuleConfiguration('newModule')
        );
    }

    public function testMergeModuleConfigurationOfAlreadyInstalledModule()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');

        $moduleConfigurationMergingService = $this->getMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(
            $this->getShopConfigurationWithAlreadyInstalledModule(),
            $moduleConfiguration
        );

        $this->assertEquals(
            $moduleConfiguration,
            $shopConfiguration->getModuleConfiguration('installedModule')
        );
    }

    public function testMergeSetsModuleConfigurationIfNoExistingModuleConfigurationInstalled()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');

        $shopConfiguration = new ShopConfiguration();

        $moduleConfigurationMergingService = $this->getMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(
            $shopConfiguration,
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

        $classExtension = [
            'shopClass1' => 'extension1ToStayInNewModuleConfiguration',
            'shopClass2' => 'extension5',
            'shopClass5' => 'extension6'
        ];

        foreach ($classExtension as $namespace => $moduleExtension) {
            $moduleConfiguration->addClassExtension(
                new ClassExtension(
                    $namespace,
                    $moduleExtension
                )
            );
        }

        $moduleConfigurationMergingService = $this->getMergingService();
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
            $shopConfiguration->getClassExtensionsChain()->getChain()
        );
    }

    public function testSettingUpdatedAfterMergingAlreadyInstalledModule()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');

        $moduleSettings = [
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
                'name'     => 'completeNewOne',
                'type'     => 'string',
                'position' => '100500',
                'value'    => 'myValue'
            ]
        ];

        foreach ($moduleSettings as $settingData) {
            $setting = new Setting();

            $setting->setType($settingData['type']);
            $setting->setName($settingData['name']);

            if (isset($settingData['value'])) {
                $setting->setValue($settingData['value']);
            }

            if (isset($settingData['group'])) {
                $setting->setGroupName($settingData['group']);
            }

            if (isset($settingData['position'])) {
                $setting->setPositionInGroup((int)$settingData['position']);
            }

            if (isset($settingData['constraints'])) {
                $setting->setConstraints($settingData['constraints']);
            }

            $moduleConfiguration->addModuleSetting($setting);
        }

        $moduleConfigurationMergingService = $this->getMergingService();
        $shopConfiguration = $moduleConfigurationMergingService->merge(
            $this->getShopConfigurationWithAlreadyInstalledModule(),
            $moduleConfiguration
        );

        $mergedModuleConfiguration = $shopConfiguration->getModuleConfiguration('installedModule');

        $settings = [];

        foreach ($mergedModuleConfiguration->getModuleSettings() as $index => $setting) {
            if ($setting->getGroupName()) {
                $settings[$index]['group'] = $setting->getGroupName();
            }

            if ($setting->getName()) {
                $settings[$index]['name'] = $setting->getName();
            }

            if ($setting->getType()) {
                $settings[$index]['type'] = $setting->getType();
            }

            $settings[$index]['value'] = $setting->getValue();

            if ($setting->getPositionInGroup()) {
                $settings[$index]['position'] = $setting->getPositionInGroup();
            }

            if (!empty($setting->getConstraints())) {
                $settings[$index]['constraints'] = $setting->getConstraints();
            }
        }

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
            $settings
        );
    }

    private function getShopConfigurationWithAlreadyInstalledModule(): ShopConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('installedModule');

        $classExtension = [
            'shopClass1'            => 'extension1ToStayInNewModuleConfiguration',
            'shopClass2'            => 'extension2ToBeChanged',
            'shopClass3'            => 'extension3ToBeDeleted',
            'shopClass4ToBeDeleted' => 'extension4ToBeDeleted'
        ];

        foreach ($classExtension as $namespace => $moduleExtension) {
            $moduleConfiguration->addClassExtension(
                new ClassExtension(
                    $namespace,
                    $moduleExtension
                )
            );
        }

        $moduleSettings =
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
                    'name'     => 'willBeDeleted',
                    'type'     => 'str',
                    'position' => '100500',
                    'value'    => 'myValue1',
                ]
            ];

        foreach ($moduleSettings as $settingData) {
            $setting = new Setting();

            $setting->setType($settingData['type']);
            $setting->setName($settingData['name']);
            $setting->setValue($settingData['value']);

            if (isset($settingData['group'])) {
                $setting->setGroupName($settingData['group']);
            }

            if (isset($settingData['position'])) {
                $setting->setPositionInGroup((int)$settingData['position']);
            }

            if (isset($settingData['constraints'])) {
                $setting->setConstraints($settingData['constraints']);
            }

            $moduleConfiguration->addModuleSetting($setting);
        }

        $chain = new ClassExtensionsChain();
        $chain->setChain([
            'shopClass1'            => ['someOtherExtension1', 'extension1ToStayInNewModuleConfiguration'],
            'shopClass2'            => ['someOtherExtension2', 'extension2ToBeChanged', 'someOtherExtension3'],
            'shopClass3'            => ['extension3ToBeDeleted', 'someOtherExtension4'],
            'shopClass4ToBeDeleted' => ['extension4ToBeDeleted']
        ]);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);
        $shopConfiguration->setClassExtensionsChain($chain);

        return $shopConfiguration;
    }

    /**
     * @return ModuleConfigurationMergingServiceInterface
     */
    private function getMergingService(): ModuleConfigurationMergingServiceInterface
    {
        return $this->get(ModuleConfigurationMergingServiceInterface::class);
    }
}
