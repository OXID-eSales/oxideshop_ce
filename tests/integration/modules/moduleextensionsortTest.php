<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

require_once realpath(dirname(__FILE__)) . '/basemoduleTestCase.php';

class Integration_Modules_ModuleExtensionSortTest extends BaseModuleTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->clean();
        parent::tearDown();
    }

    public function providerModuleReorderExtensions()
    {
        return array(
            array(
                // modules to be activated during test preparation
                array(
                    'extending_1_class', 'extending_3_classes_with_1_extension',
                    'extending_3_classes', 'extending_1_class_3_extensions',
                ),

                // module that will be reactivated
                'extending_3_classes_with_1_extension',

                // environment asserts
                array(
                    'oxorder'   => 'extending_3_classes_with_1_extension/mybaseclass&extending_1_class/myorder&'.
                                   'extending_1_class_3_extensions/myorder1&extending_3_classes/myorder&'.
                                   'extending_1_class_3_extensions/myorder3&extending_1_class_3_extensions/myorder2',
                    'oxarticle' => 'extending_3_classes/myarticle&extending_3_classes_with_1_extension/mybaseclass',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                )
            ),

            array(
                // modules to be activated during test preparation
                array(
                    'extending_1_class_3_extensions',
                ),

                // module that will be reactivated
                'extending_1_class_3_extensions',

                // environment asserts
                array(
                    'oxorder'   => 'extending_1_class_3_extensions/myorder2&'.
                                   'extending_1_class_3_extensions/myorder1&'.
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
    public function testIsActive( $aInstallModules, $sModule, $aReorderedExtensions)
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );

        // load reordered extensions
        $this->getConfig()->setConfigParam( 'aModules', $aReorderedExtensions );

        $oModule = new oxModule();
        $oModule->load( $sModule );

        $oModule->deactivate();
        $oModule->activate();

        $oValidator = new EnvironmentValidator();
        $oValidator->setConfig( $this->getConfig() );

        $this->assertTrue( $oValidator->checkExtensions( $aReorderedExtensions ), 'Extension order changed' );
    }
}
 