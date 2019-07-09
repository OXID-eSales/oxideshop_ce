<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Module\Module;

class ModuleActivationTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return array(
            $this->caseSixModulesPreparedReactivatedWithEverything(),
            $this->caseTwoModulesPreparedReactivatedWithEverything(),
            $this->caseFourModulesPreparedReactivatedExtendingThreeClassesWithOneExtension(),
            $this->caseEightModulesPreparedReactivatedNoExtending(),
            $this->caseTwoModulesPreparedReactivatedWithTwoFiles(),
            $this->caseTwoModulesPreparedReactivatedWithTwoSettings(),
            $this->caseTwoModulesPreparedReactivatedWithTwoTemplates(),
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
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew(Module::class);
        $oModule->load($sModule);
        $this->deactivateModule($oModule);
        $this->installAndActivateModule($oModule->getId());

        $this->runAsserts($aResultToAsserts);
    }

    /**
     * Data provider case with 6 modules prepared and with_everything module reactivated
     *
     * @return array
     */
    protected function caseSixModulesPreparedReactivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files',
                'with_everything', 'extending_3_blocks', 'with_events',
            ),

            // module that will be reactivated
            'with_everything',

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
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&with_everything/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
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
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_everything module reactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedReactivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_everything', 'no_extending'
            ),

            // module that will be reactivated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                ),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'with_everything/myorder1',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
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
            )
        );
    }


    /**
     * Data provider case with 4 modules prepared and extending_3_classes_with_1_extension module reactivated
     *
     * @return array
     */
    private function caseFourModulesPreparedReactivatedExtendingThreeClassesWithOneExtension()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'extending_3_classes_with_1_extension',
                'extending_3_classes', 'extending_1_class_3_extensions',
            ),

            // module that will be reactivated
            'extending_1_class_3_extensions',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(

                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myorder&oeTest/extending_1_class_3_extensions/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
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
            )
        );
    }

    /**
     * Data provider case with 8 modules prepared and no_extending module reactivated
     *
     * @return array
     */
    private function caseEightModulesPreparedReactivatedNoExtending()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'no_extending', 'extending_3_blocks', 'with_everything', 'with_events'
            ),

            // module that will be reactivated
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
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&with_everything/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
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
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_files module reactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedReactivatedWithTwoFiles()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_files', 'no_extending'
            ),

            // module that will be reactivated
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
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_settings module reactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedReactivatedWithTwoSettings()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_settings', 'no_extending'
            ),

            // module that will be reactivated
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
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_templates module reactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedReactivatedWithTwoTemplates()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_templates', 'no_extending'
            ),

            // module that will be reactivated
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
            )
        );
    }
}
