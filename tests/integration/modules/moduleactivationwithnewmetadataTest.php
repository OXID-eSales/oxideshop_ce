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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__)) . '/basemoduleTestCase.php';

class Integration_Modules_ModuleActivationWithNewMetaDataTest extends BaseModuleTestCase
{

    public function providerModuleNewMetaData()
    {
        return array(
            $this->_caseReactivatedWithRemovedExtension(),
            $this->_caseReactivatedWithAddExtension(),
            $this->_caseReactivatedWithRemovedTemplateAndBlock(),
            $this->_caseReactivatedWithRemovedAllExtendedInfo(),
            $this->_caseReactivatedWithChangedConfigs(),
            $this->_caseReactivatedWithRemovedConfigs(),
        );
    }

    /**
     * Tests when existing active module's meta data is changed and module activates and deactivates
     * development process
     *
     * @dataProvider providerModuleNewMetaData
     */
    public function testModuleActivationWithNewMetaData($aInstallModules, $sModule, $aMetaData, $aResultToAsserts)
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $oModule = new oxModule();
        $oModule->load($sModule);

        $this->_deactivateModule($oModule);

        $oModule->setModuleData($aMetaData);

        $this->_activateModule($oModule);

        $this->_runAsserts($aResultToAsserts, $sModule);
    }


    /**
     * Removed extension in metadata
     *
     * @return array
     */
    protected function _caseReactivatedWithRemovedExtension()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

            // new metadata
            array(
                'id'          => 'with_everything',
                'title'       => 'Test extending 1 shop class',
                'description' => 'Module testing extending 1 shop class',
                'thumbnail'   => 'picture.png',
                'version'     => '1.0',
                'author'      => 'OXID eSales AG',
                'extend'      => array(
                    'oxarticle' => 'with_everything/myarticle',
                    'oxorder'   => array(
                        'with_everything/myorder1',
                        'with_everything/myorder2',
                    ),
                    'oxuser'    => 'with_everything/myuser',
                ),
                'blocks'      => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'events'      => array(
                    'onActivate'   => 'MyEvents::onActivate',
                    'onDeactivate' => 'MyEvents::onDeactivate'
                ),
                'templates'   => array(
                    'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                    'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                ),
                'files'       => array(
                    'myexception'  => 'with_everything/core/exception/myexception.php',
                    'myconnection' => 'with_everything/core/exception/myconnection.php',
                ),
                'settings'    => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
            ),

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2',
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
            ),
        );
    }

    /**
     * Add extension in metadata
     *
     * @return array
     */
    protected function _caseReactivatedWithAddExtension()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

            // new metadata
            array(
                'id'          => 'with_everything',
                'title'       => 'Test extending 1 shop class',
                'description' => 'Module testing extending 1 shop class',
                'thumbnail'   => 'picture.png',
                'version'     => '1.0',
                'author'      => 'OXID eSales AG',
                'extend'      => array(
                    'oxarticle' => 'with_everything/myarticle',
                    'oxorder'   => array(
                        'with_everything/myorder1',
                        'with_everything/myorder2',
                    ),
                    'oxuser'    => 'with_everything/myuser',
                    'oxprice'   => 'with_everything/myprice',
                ),
                'blocks'      => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'events'      => array(
                    'onActivate'   => 'MyEvents::onActivate',
                    'onDeactivate' => 'MyEvents::onDeactivate'
                ),
                'templates'   => array(
                    'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                    'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                ),
                'files'       => array(
                    'myexception'  => 'with_everything/core/exception/myexception.php',
                    'myconnection' => 'with_everything/core/exception/myconnection.php',
                ),
                'settings'    => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
            ),

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                    'oxprice'   => 'with_everything/myprice',
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
            ),
        );
    }

    /**
     * Add extension in metadata
     *
     * @return array
     */
    protected function _caseReactivatedWithRemovedTemplateAndBlock()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

            // new metadata
            array(
                'id'          => 'with_everything',
                'title'       => 'Test extending 1 shop class',
                'description' => 'Module testing extending 1 shop class',
                'thumbnail'   => 'picture.png',
                'version'     => '1.0',
                'author'      => 'OXID eSales AG',
                'extend'      => array(
                    'oxarticle' => 'with_everything/myarticle',
                    'oxorder'   => array(
                        'with_everything/myorder1',
                        'with_everything/myorder2',
                    ),
                    'oxuser'    => 'with_everything/myuser',
                    'oxprice'   => 'with_everything/myprice',
                ),
                'events'      => array(
                    'onActivate'   => 'MyEvents::onActivate',
                    'onDeactivate' => 'MyEvents::onDeactivate'
                ),
                'files'       => array(
                    'myexception'  => 'with_everything/core/exception/myexception.php',
                    'myconnection' => 'with_everything/core/exception/myconnection.php',
                ),
                'settings'    => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
            ),

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                    'oxprice'   => 'with_everything/myprice',
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
            ),
        );
    }


    /**
     * Add extension in metadata
     *
     * @return array
     */
    protected function _caseReactivatedWithRemovedAllExtendedInfo()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

            // new metadata
            array(
                'id'          => 'with_everything',
                'title'       => 'Test extending 1 shop class',
                'description' => 'Module testing extending 1 shop class',
                'thumbnail'   => 'picture.png',
                'version'     => '1.0',
                'author'      => 'OXID eSales AG',
            ),

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder' => 'extending_1_class/myorder',
                ),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'with_events'  => array(
                        'myevents' => 'with_events/files/myevents.php',
                    ),
                ),
                'disabledModules' => array(),
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
                    'with_everything'    => null,
                ),
            ),
        );
    }

    /**
     * Add extension in metadata
     *
     * @return array
     */
    protected function _caseReactivatedWithChangedConfigs()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

            // new metadata
            array(
                'id'          => 'with_everything',
                'title'       => 'Test extending 1 shop class',
                'description' => 'Module testing extending 1 shop class',
                'thumbnail'   => 'picture.png',
                'version'     => '1.0',
                'author'      => 'OXID eSales AG',
                'extend'      => array(
                    'oxarticle' => 'with_everything/myarticle',
                    'oxorder'   => array(
                        'with_everything/myorder1',
                        'with_everything/myorder2',
                    ),
                    'oxuser'    => 'with_everything/myuser',
                ),
                'blocks'      => array(
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                ),
                'events'      => array(
                    'onActivate'   => 'MyEvents::onActivate',
                    'onDeactivate' => 'MyEvents::onDeactivate'
                ),
                'templates'   => array(
                    'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                    'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                ),
                'files'       => array(
                    'myexception'  => 'with_everything/core/exception/myexception.php',
                    'myconnection' => 'with_everything/core/exception/myconnection.php',
                ),
                'settings'    => array(
                    array('group' => 'my_checkconfirm_new', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                    array('group' => 'my_new', 'name' => 'blCheck', 'type' => 'bool', 'value' => 'true'),
                ),
            ),

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
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2',
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
                    array('group' => 'my_checkconfirm_new', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                    array('group' => 'my_new', 'name' => 'blCheck', 'type' => 'bool', 'value' => 'true'),
                ),
                'settings_values' => array(
                    array('group' => 'my_checkconfirm_new', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => true),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                    array('group' => 'my_new', 'name' => 'blCheck', 'type' => 'bool', 'value' => true),
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
            ),
        );
    }

    /**
     * Add extension in metadata
     *
     * @return array
     */
    protected function _caseReactivatedWithRemovedConfigs()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

            // new metadata
            array(
                'id'          => 'with_everything',
                'title'       => 'Test extending 1 shop class',
                'description' => 'Module testing extending 1 shop class',
                'thumbnail'   => 'picture.png',
                'version'     => '1.0',
                'author'      => 'OXID eSales AG',
                'extend'      => array(
                    'oxarticle' => 'with_everything/myarticle',
                    'oxorder'   => array(
                        'with_everything/myorder1',
                        'with_everything/myorder2',
                    ),
                    'oxuser'    => 'with_everything/myuser',
                ),
                'blocks'      => array(
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                ),
                'events'      => array(
                    'onActivate'   => 'MyEvents::onActivate',
                    'onDeactivate' => 'MyEvents::onDeactivate'
                ),
                'templates'   => array(
                    'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                    'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                ),
                'files'       => array(
                    'myexception'  => 'with_everything/core/exception/myexception.php',
                    'myconnection' => 'with_everything/core/exception/myconnection.php',
                ),
            ),

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
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2',
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
                'settings'        => array(),
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
            ),
        );
    }
}
 