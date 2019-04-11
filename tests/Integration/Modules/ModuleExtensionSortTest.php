<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxRegistry;

class ModuleExtensionSortTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleReorderExtensions()
    {
        return array(
            array(
                // Modules to be activated during test preparation
                array(
                    'extending_1_class', 'extending_3_classes_with_1_extension',
                    'extending_3_classes', 'extending_1_class_3_extensions',
                ),

                // Module that will be reactivated
                'extending_3_classes_with_1_extension',

                // Reordered extensions
                array(
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_3_classes_with_1_extension/mybaseclass&oeTest/extending_1_class/myorder&' .
                                   'extending_1_class_3_extensions/myorder1&extending_3_classes/myorder&' .
                                   'extending_1_class_3_extensions/myorder3&extending_1_class_3_extensions/myorder2',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes/myarticle&extending_3_classes_with_1_extension/mybaseclass',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),
                // Not reordered extensions
                array(
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&' .
                                   'extending_3_classes/myorder&extending_1_class_3_extensions/myorder1&' .
                                   'extending_1_class_3_extensions/myorder2&extending_1_class_3_extensions/myorder3',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),
            ),

            array(
                // Modules to be activated during test preparation
                array(
                    'extending_1_class_3_extensions',
                ),

                // Module that will be reactivated
                'extending_1_class_3_extensions',

                // Reordered extensions
                array(
                    \OxidEsales\Eshop\Application\Model\Order::class => 'extending_1_class_3_extensions/myorder2&' .
                                 'extending_1_class_3_extensions/myorder1&' .
                                 'extending_1_class_3_extensions/myorder3',
                ),
                // Not reordered extensions
                array(
                    \OxidEsales\Eshop\Application\Model\Order::class => 'extending_1_class_3_extensions/myorder1&' .
                                 'extending_1_class_3_extensions/myorder2&' .
                                 'extending_1_class_3_extensions/myorder3',
                ),
            )
        );
    }

    /**
     * Tests check if changed extensions order stays the same after deactivation / activation
     *
     * @dataProvider providerModuleReorderExtensions
     *
     * @param array  $aInstallModules
     * @param string $sModule
     * @param array  $aReorderedExtensions
     */
    public function testIsActive($aInstallModules, $sModule, $aReorderedExtensions)
    {
        $this->markTestSkipped('Wont work. The logic was changed, we change chain on module deactivation/activation.');

        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        // load reordered extensions
        oxRegistry::getConfig()->setConfigParam('aModules', $aReorderedExtensions);

        $oModule = oxNew('oxModule');
        $oModule->load($sModule);

        $this->deactivateModule($oModule);
        $this->installAndActivateModule($oModule->getId());

        $oValidator = new Validator(oxRegistry::getConfig());

        $this->assertTrue($oValidator->checkExtensions($aReorderedExtensions), 'Extension order changed');
    }

    /**
     * Tests check if changed extensions order stays the same after deactivation / activation
     *
     * @dataProvider providerModuleReorderExtensions
     *
     * @param array  $aInstallModules
     * @param string $sModule
     * @param array  $aReorderedExtensions
     * @param array  $aNotReorderedExtensions
     */
    public function testIfNotReorderedOnSubShop($aInstallModules, $sModule, $aReorderedExtensions, $aNotReorderedExtensions)
    {
        $this->markTestSkipped('Wont work. The logic was changed, we change chain on module deactivation/activation.');

        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }
        $oConfig = oxRegistry::getConfig();
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);
        $oValidator = new Validator($oConfig);
        $oModule = oxNew('oxModule');

        $oEnvironment->setShopId(2);
        $oEnvironment->activateModules($aInstallModules);

        // load reordered extensions for shop
        $oEnvironment->setShopId(1);
        $oConfig->setConfigParam('aModules', $aReorderedExtensions);

        $oModule->load($sModule);
        $this->deactivateModule($oModule);
        $this->installAndActivateModule();

        $oEnvironment->setShopId(2);
        $this->assertTrue($oValidator->checkExtensions($aNotReorderedExtensions), 'Extension order changed');
    }
}
