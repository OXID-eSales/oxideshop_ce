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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__)) . '/basemoduleTestCase.php';

class Integration_Modules_ModuleExtensionSortTest extends BaseModuleTestCase
{

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
                    'oxorder'   => 'extending_3_classes_with_1_extension/mybaseclass&extending_1_class/myorder&'.
                                   'extending_1_class_3_extensions/myorder1&extending_3_classes/myorder&'.
                                   'extending_1_class_3_extensions/myorder3&extending_1_class_3_extensions/myorder2',
                    'oxarticle' => 'extending_3_classes/myarticle&extending_3_classes_with_1_extension/mybaseclass',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),

                // Not reordered extensions
                array(
                    'oxarticle' => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    'oxorder'   => 'extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&' .
                        'extending_3_classes/myorder&extending_1_class_3_extensions/myorder1&' .
                        'extending_1_class_3_extensions/myorder2&extending_1_class_3_extensions/myorder3',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                )
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
                    'oxorder'   => 'extending_1_class_3_extensions/myorder2&'.
                                   'extending_1_class_3_extensions/myorder1&'.
                                   'extending_1_class_3_extensions/myorder3',
                ),

                // Not reordered extensions
                array(
                    'oxorder'   => 'extending_1_class_3_extensions/myorder1&'.
                                   'extending_1_class_3_extensions/myorder2&'.
                                   'extending_1_class_3_extensions/myorder3',
                )
            )
        );
    }

    /**
     * Tests check if changed extensions order stays the same after deactivation / activation
     *
     * @dataProvider providerModuleReorderExtensions
     */
    public function testIsActive( $aInstallModules, $sModule, $aReorderedExtensions )
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );

        // load reordered extensions
        $this->_getConfig()->setConfigParam( 'aModules', $aReorderedExtensions );

        $oModule = new oxModule();
        $oModule->load( $sModule );

        $oModule->deactivate();
        $oModule->activate();

        $oValidator = new EnvironmentValidator( $this->getConfig() );

        $this->assertTrue( $oValidator->checkExtensions( $aReorderedExtensions ), 'Extension order changed' );
    }

    /**
     * Tests check if changed extensions order stays the same after deactivation / activation
     *
     * @dataProvider providerModuleReorderExtensions
     */
    public function testIfNotReorderedOnSubShop( $aInstallModules, $sModule, $aReorderedExtensions, $aNotReorderedExtensions )
    {
        $oConfig = $this->_getConfig();
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );
        $oValidator = new EnvironmentValidator( $oConfig );
        $oModule = new oxModule();

        $this->_activateGivenModulesOnShop( $aInstallModules, $oModuleEnvironment, 2 );

        // load reordered extensions for shop
        $oConfig->setShopId( 1 );
        $oConfig->setConfigParam( 'aModules', $aReorderedExtensions );

        $oModule->load( $sModule );
        $oModule->deactivate();
        $oModule->activate();

        $oConfig->setShopId( 2 );
        $this->assertTrue( $oValidator->checkExtensions( $aNotReorderedExtensions ), 'Extension order changed' );
    }

    /**
     * Activates modules on selected shop.
     *
     * @param $aInstallModules Array of modules to install
     * @param $oModuleEnvironment Modules environment object
     * @param $iShopId Shop id
     */
    private function _activateGivenModulesOnShop( $aInstallModules, $oModuleEnvironment, $iShopId )
    {
        $this->_getConfig()->setShopId( $iShopId );
        $this->_getConfig()->setConfigParam( 'aModules', array() );
        $oModuleEnvironment->activateModules( $aInstallModules );
    }

    /**
     * Returns shop config object.
     *
     * @return OxConfig
     */
    private function _getConfig()
    {
        $oConfig = oxRegistry::getConfig();

        return $oConfig;
    }
}
 