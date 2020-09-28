<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;

final class ModuleActivationTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return [
            $this->caseReactivatedWithRemovedExtension(),
            $this->caseOneModulePreparedActivatedWithEverything(),
            $this->caseThreeModulesPreparedActivatedExtendingThreeClassesWithOneExtension(),
            $this->caseSevenModulesPreparedActivatedNoExtending(),
            $this->caseOneModulePreparedActivatedWithTwoEvents(),
            $this->caseOneModulePreparedActivatedWithTwoSettings(),
            $this->caseOneModulePreparedActivatedWithTwoTemplates(),
        ];
    }

    /**
     * @dataProvider providerModuleActivation
     */
    public function testModuleActivation(array $aInstallModules, string $sModule, array $aResultToAsserts): void
    {
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');
        $this->deactivateModule($oModule, $sModule);

        $this->installAndActivateModule($sModule);

        $this->runAsserts($aResultToAsserts);
    }

    public function testModuleDeactivationInMainShopDidNotDeactivateItInSubShop(): void
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }

        $this->prepareProjectConfigurationWitSubshops();

        $moduleId = 'with_everything';
        
        $this->installAndActivateModule($moduleId);

        $this->installAndActivateModule($moduleId, 2);

        $this->deactivateModule(oxNew('oxModule'), $moduleId, 1);

        $environment = new Environment();
        $environment->setShopId(2);

        $this->expectException(ModuleSetupException::class);
        $this->installAndActivateModule($moduleId, 2);
    }

    private function caseReactivatedWithRemovedExtension(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'extending_1_class',
                'with_2_templates',
                'with_everything',
                'extending_3_blocks',
                'with_events'
            ],

            // module that will be reactivated
            'with_everything',

            // environment asserts
            [
                'blocks'          => [
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                ],
                'extend'          => [
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&with_everything/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
                ],
                'settings'        => [
                    ['group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'],
                    ['group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'],
                ],
                'disabledModules' => [],
                'templates'       => [
                    'with_2_templates' => [
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ],
                    'with_everything'  => [
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ],
                ],
                'events'          => [
                    'with_everything' => [
                        'onActivate'   => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onActivate',
                        'onDeactivate' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onDeactivate'
                    ],
                    'with_events' => [
                        'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onActivate',
                        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onDeactivate'
                    ]
                ],
                'versions'        => [
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ],
            ]
        ];
    }

    private function caseOneModulePreparedActivatedWithEverything(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'no_extending',
                'with_everything'
            ],

            // module that will be activated
            'with_everything',

            // environment asserts
            [
                'blocks'          => [
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                ],
                'extend'          => [
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
                    \OxidEsales\Eshop\Application\Model\Order::class => 'with_everything/myorder1'
                ],
                'settings'        => [
                    ['group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'],
                    ['group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'],
                ],
                'disabledModules' => [],
                'templates'       => [
                    'with_everything' => [
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ],
                ],
                'versions'        => [
                    'no_extending'    => '1.0',
                    'with_everything' => '1.0',
                ],
            ]
        ];
    }

    private function caseThreeModulesPreparedActivatedExtendingThreeClassesWithOneExtension(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'extending_1_class',
                'extending_3_classes_with_1_extension',
                'extending_3_classes',
                'extending_1_class_3_extensions'
            ],

            // module that will be activated
            'extending_1_class_3_extensions',

            // environment asserts
            [
                'blocks'          => [],
                'extend'          => [
                    \OxidEsales\Eshop\Application\Model\Order::class   => '' .
                        'oeTest/extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myorder&oeTest/extending_1_class_3_extensions/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ],
                'settings'        => [],
                'disabledModules' => [],
                'templates'       => [],
                'versions'        => [
                    'extending_3_classes_with_1_extension' => '1.0',
                    'extending_1_class'                    => '1.0',
                    'extending_3_classes'                  => '1.0',
                    'extending_1_class_3_extensions'       => '1.0',
                ],
            ]
        ];
    }

    private function caseSevenModulesPreparedActivatedNoExtending(): array
    {
        return [
            // modules to be activated during test preparation
            [
                'extending_1_class',
                'with_2_templates',
                'with_2_settings',
                'extending_3_blocks',
                'with_everything',
                'with_events',
                'no_extending'
            ],

            // module that will be activated
            'no_extending',

            // environment asserts
            [
                'blocks'          => [
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                ],
                'extend'          => [
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&with_everything/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
                ],
                'settings'        => [
                    ['group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'],
                    ['group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'],
                    ['group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'],
                    ['group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'],
                ],
                'disabledModules' => [],
                'templates'       => [
                    'with_2_templates' => [
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ],
                    'with_everything'  => [
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ],
                ],
                'events'          => [
                    'with_everything' => [
                        'onActivate'   => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onActivate',
                        'onDeactivate' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onDeactivate'
                    ],
                    'with_events' => [
                        'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onActivate',
                        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onDeactivate'
                    ]
                ],
                'versions'        => [
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_settings'    => '1.0',
                    'extending_3_blocks' => '1.0',
                    'no_extending'       => '1.0',
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ],
            ]
        ];
    }

    private function caseOneModulePreparedActivatedWithTwoEvents(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'no_extending',
                'with_events'
            ],

            // module that will be activated
            'with_events',

            // environment asserts
            [
                'blocks'          => [],
                'extend'          => [],
                'settings'        => [],
                'disabledModules' => [],
                'templates'       => [],
                'events'          => [
                    'with_events' => [
                        'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onActivate',
                        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onDeactivate'
                    ]
                ],
                'versions'        => [
                    'no_extending' => '1.0',
                    'with_events' => '1.0',
                ],
            ]
        ];
    }

    private function caseOneModulePreparedActivatedWithTwoSettings(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'no_extending',
                'with_2_settings'
            ],

            // module that will be activated
            'with_2_settings',

            // environment asserts
            [
                'blocks'          => [],
                'extend'          => [],
                'settings'        => [
                    ['group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'],
                    ['group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'],
                ],
                'disabledModules' => [],
                'templates'       => [],
                'versions'        => [
                    'no_extending'    => '1.0',
                    'with_2_settings' => '1.0',
                ],
            ]
        ];
    }

    private function caseOneModulePreparedActivatedWithTwoTemplates(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'no_extending',
                'with_2_templates'
            ],

            // module that will be activated
            'with_2_templates',

            // environment asserts
            [
                'blocks'          => [],
                'extend'          => [],
                'settings'        => [],
                'disabledModules' => [],
                'templates'       => [
                    'with_2_templates' => [
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ],
                ],
                'versions'        => [
                    'no_extending'     => '1.0',
                    'with_2_templates' => '1.0',
                ],
            ]
        ];
    }

    private function prepareProjectConfigurationWitSubshops()
    {
        $projectConfigurationDao = $this->container->get(ProjectConfigurationDaoInterface::class);
        $projectConfiguration = $projectConfigurationDao->getConfiguration();

        $projectConfiguration->addShopConfiguration(2, new ShopConfiguration());

        $projectConfigurationDao->save($projectConfiguration);
    }
}
