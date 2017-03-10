<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_3_classes_with_1_extension/mybaseclass&extending_1_class/myorder&' .
                                   'extending_1_class_3_extensions/myorder1&extending_3_classes/myorder&' .
                                   'extending_1_class_3_extensions/myorder3&extending_1_class_3_extensions/myorder2',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes/myarticle&extending_3_classes_with_1_extension/mybaseclass',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),
                // Not reordered extensions
                array(
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&' .
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
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        // load reordered extensions
        oxRegistry::getConfig()->setConfigParam('aModules', $aReorderedExtensions);

        $oModule = oxNew('oxModule');
        $oModule->load($sModule);

        $this->deactivateModule($oModule);
        $this->activateModule($oModule);

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
        $this->activateModule($oModule);

        $oEnvironment->setShopId(2);
        $this->assertTrue($oValidator->checkExtensions($aNotReorderedExtensions), 'Extension order changed');
    }
}
