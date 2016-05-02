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
namespace Integration\Modules;

require_once __DIR__ . '/BaseModuleTestCase.php';

class ModuleActivationFirstTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return array(
            $this->caseFiveModulesPreparedActivatedWithEverything(),
            $this->caseOneModulePreparedActivatedWithEverything(),
            $this->caseThreeModulesPreparedActivatedExtendingThreeClassesWithOneExtension(),
            $this->caseSevenModulesPreparedActivatedNoExtending(),
            $this->caseOneModulePreparedActivatedWithTwoFiles(),
            $this->caseOneModulePreparedActivatedWithTwoSettings(),
            $this->caseOneModulePreparedActivatedWithTwoTemplates(),
        );
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $aInstallModules
     * @param string $sModule
     * @param array  $aResultToAsserts
     */
    public function testModuleActivation($aInstallModules, $sModule, $aResultToAsserts)
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $oModule = oxNew('oxModule');
        $this->activateModule($oModule, $sModule);

        $this->runAsserts($aResultToAsserts);
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $aInstallModules
     * @param string $sModule
     * @param array  $aResultToAsserts
     */
    public function testModuleActivationInMainShopDidNotActivatedInSubShop($aInstallModules, $sModule, $aResultToAsserts)
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);
        $oModule = oxNew('oxModule');

        $oEnvironment->setShopId(2);
        $oEnvironment->activateModules($aInstallModules);

        $oEnvironment->setShopId(1);
        $this->activateModule($oModule, $sModule);

        $oEnvironment->setShopId(2);
        $this->activateModule($oModule, $sModule);

        $oEnvironment->setShopId(1);
        $this->deactivateModule($oModule, $sModule);

        $oEnvironment->setShopId(2);
        $this->runAsserts($aResultToAsserts);
    }

    /**
     * Data provider case with 5 modules prepared and with_everything module activated
     *
     * @return array
     */
    protected function caseFiveModulesPreparedActivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'extending_3_blocks', 'with_events',
            ),

            // module that will be activated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files'    => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'with_everything' => array(
                        'myexception'  => 'with_everything/core/exception/myexception.php',
                        'myconnection' => 'with_everything/core/exception/myconnection.php',
                    ),
                    'with_events'     => array(
                        'myevents' => 'with_events/files/myevents.php',
                    ),
                ),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'with_everything'  => array(
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
                    'with_everything'    => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_everything module activated
     *
     * @return array
     */
    private function caseOneModulePreparedActivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxarticle' => 'with_everything/myarticle',
                    'oxorder'   => 'with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_everything' => array(
                        'myexception'  => 'with_everything/core/exception/myexception.php',
                        'myconnection' => 'with_everything/core/exception/myconnection.php',
                    )
                ),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_everything' => array(
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'no_extending'    => '1.0',
                    'with_everything' => '1.0',
                ),
                'events'          => array(
                    'no_extending'    => null,
                    'with_everything' => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }


    /**
     * Data provider case with 3 modules prepared and extending_3_classes_with_1_extension module activated
     *
     * @return array
     */
    private function caseThreeModulesPreparedActivatedExtendingThreeClassesWithOneExtension()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class',
                'extending_3_classes_with_1_extension', 'extending_3_classes'
            ),

            // module that will be activated
            'extending_1_class_3_extensions',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    'oxorder'   => '' .
                                   'extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&' .
                                   'extending_3_classes/myorder&extending_1_class_3_extensions/myorder1&' .
                                   'extending_1_class_3_extensions/myorder2&extending_1_class_3_extensions/myorder3',
                    'oxarticle' => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'extending_3_classes_with_1_extension' => '1.0',
                    'extending_1_class'                    => '1.0',
                    'extending_3_classes'                  => '1.0',
                    'extending_1_class_3_extensions'       => '1.0',
                ),
                'events'          => array(
                    'extending_3_classes_with_1_extension' => null,
                    'extending_1_class'                    => null,
                    'extending_3_classes'                  => null,
                    'extending_1_class_3_extensions'       => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 7 modules prepared and no_extending module activated
     *
     * @return array
     */
    private function caseSevenModulesPreparedActivatedNoExtending()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events'
            ),

            // module that will be activated
            'no_extending',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files'    => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'with_everything' => array(
                        'myexception'  => 'with_everything/core/exception/myexception.php',
                        'myconnection' => 'with_everything/core/exception/myconnection.php',
                    ),
                    'with_events'     => array(
                        'myevents' => 'with_events/files/myevents.php',
                    ),
                ),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'with_everything'  => array(
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'no_extending'       => '1.0',
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ),
                'events'          => array(
                    'extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_settings'    => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'no_extending'       => null,
                    'with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                    'with_everything'    => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_2_files module activated
     *
     * @return array
     */
    private function caseOneModulePreparedActivatedWithTwoFiles()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_2_files',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                ),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending' => '1.0',
                    'with_2_files' => '1.0',
                ),
                'events'          => array(
                    'no_extending' => null,
                    'with_2_files' => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_2_settings module activated
     *
     * @return array
     */
    private function caseOneModulePreparedActivatedWithTwoSettings()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_2_settings',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending'    => '1.0',
                    'with_2_settings' => '1.0',
                ),
                'events'          => array(
                    'no_extending'    => null,
                    'with_2_settings' => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_2_templates module activated
     *
     * @return array
     */
    private function caseOneModulePreparedActivatedWithTwoTemplates()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_2_templates',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'no_extending'     => '1.0',
                    'with_2_templates' => '1.0',
                ),
                'events'          => array(
                    'no_extending'     => null,
                    'with_2_templates' => null,
                ),
            )
        );
    }
}
