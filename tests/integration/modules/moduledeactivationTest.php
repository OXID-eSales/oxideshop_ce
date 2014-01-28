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

require_once realpath(dirname(__FILE__).'/../../') . '/unit/OxidTestCase.php';
require_once realpath( dirname(__FILE__) ) . '/environmentvalidator.php';
require_once realpath( dirname(__FILE__) ) . '/environment.php';

class Integration_Modules_ModuleDeactivationTest extends OxidTestCase
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

    public function providerModuleDeactivation()
    {
        return array(
            $this->with_everything_module_deactivated(),
            $this->no_extending_module_deactivated(),
        );
    }

    /**
     * Data provider case with 7 modules installed and with_everything module deactivated
     *
     * @return array
     */
    private function with_everything_module_deactivated()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events'
            ),

            // module that will be deactivated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl' ),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                ),
                'settings'        => array(
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                ),
                'disabledModules' => array(
                    'with_everything'
                ),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                ),
                'events'          => array(
                    'extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 8 modules installed and no_extending module deactivated
     *
     * @return array
     */
    private function no_extending_module_deactivated()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events', 'no_extending'
            ),

            // module that will be deactivated
            'no_extending',

            // environment asserts
            array(
                'blocks'          => array(
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl' ),
                    array( 'template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl' ),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'with_everything' => array(
                        'myexception'  => 'with_everything/core/exception/myexception.php',
                        'myconnection' => 'with_everything/core/exception/myconnection.php',
                    ),
                ),
                'settings'        => array(
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                ),
                'disabledModules' => array(
                    'no_extending'
                ),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'with_everything' => array(
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ),
                'events'          => array(
                    'extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                    'with_everything'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Test check shop environment after module deactivation
     *
     * @dataProvider providerModuleDeactivation
     */
    public function testModuleDeactivation( $aInstallModules, $sModuleId, $aResultToAsserts )
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );

        $oModule = new oxModule();
        $oModule->load( $sModuleId );
        $oModule->deactivate();

        $this->_runAsserts( $aResultToAsserts, $sModuleId );
    }

    /**
     * Runs all asserts
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    private function _runAsserts( $aExpectedResult, $sModuleId )
    {
        $oValidator = new EnvironmentValidator();
        $oValidator->setConfig( $this->getConfig() );

        if( isset( $aExpectedResult['blocks'] ) ){
            $this->assertTrue( $oValidator->checkBlocks( $aExpectedResult['blocks'] ), 'Blocks do not match expectations' );
        }

        if( isset( $aExpectedResult['extend'] ) ){
            $this->assertTrue( $oValidator->checkExtensions( $aExpectedResult['extend'] ), 'Extensions do not match expectations' );
        }

        if( isset( $aExpectedResult['files'] ) ){
            $this->assertTrue( $oValidator->checkFiles( $aExpectedResult['files'] ), 'Files do not match expectations' );
        }

        if( isset( $aExpectedResult['events'] ) ){
            $this->assertTrue( $oValidator->checkEvents( $aExpectedResult['events'] ), 'Events do not match expectations' );
        }

        if( isset( $aExpectedResult['settings'] ) ){
            $this->assertTrue( $oValidator->checkConfigs( $aExpectedResult['settings'] ), 'Configs do not match expectations' );
        }

        if( isset( $aExpectedResult['versions'] ) ){
            $this->assertTrue( $oValidator->checkVersions( $aExpectedResult['versions'] ), 'Versions do not match expectations' );
        }

        if( isset( $aExpectedResult['templates'] ) ){
            $this->assertTrue( $oValidator->checkTemplates( $aExpectedResult['templates'] ), 'Templates do not match expectations' );
        }
    }


}
 