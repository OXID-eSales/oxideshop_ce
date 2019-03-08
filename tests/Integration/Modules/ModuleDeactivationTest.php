<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

class ModuleDeactivationTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleDeactivation()
    {
        return array(
            $this->caseSevenModulesPreparedDeactivatedWithEverything(),
            $this->caseTwoModulesPreparedDeactivatedWithEverything(),
            $this->caseFourModulesPreparedDeactivatedWithExtendedClasses(),
            $this->caseEightModulesPreparedDeactivatedWithoutExtending(),
            $this->caseTwoModulesPreparedDeactivatedWithFiles(),
            $this->caseTwoModulesPreparedDeactivatedWithTemplates(),
            $this->caseTwoModulesPreparedDeactivatedWithSettings(),
        );
    }

    /**
     * Test check shop environment after module deactivation
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array  $aInstallModules
     * @param string $sModuleId
     * @param array  $aResultToAssert
     */
    public function testModuleDeactivation($aInstallModules, $sModuleId, $aResultToAssert)
    {
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');
        $this->deactivateModule($oModule, $sModuleId);

        $this->runAsserts($aResultToAssert);
    }

    /**
     * Test check shop environment after module deactivation in subshop.
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array  $aInstallModules
     * @param string $sModuleId
     * @param array  $aResultToAssert
     */
    public function testModuleDeactivationInSubShop($aInstallModules, $sModuleId, $aResultToAssert)
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }

        $this->prepareProjectConfigurationWitSubshops();

        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');

        $oEnvironment = new Environment();
        $oEnvironment->setShopId(2);
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId, 2);
        }

        $this->deactivateModule($oModule, $sModuleId, 2);

        $this->runAsserts($aResultToAssert);
    }

    /**
     * Data provider case with 7 modules prepared and with_everything module deactivated
     *
     * @return array
     */
    private function caseSevenModulesPreparedDeactivatedWithEverything()
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
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_1_class/myorder',
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
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
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
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_everything module deactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedDeactivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_everything', 'no_extending'
            ),

            // module that will be deactivated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'with_everything'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending' => '1.0',
                ),
            )
        );
    }

    /**
     * Data provider case with 4 modules prepared and extending_3_classes_with_1_extension module deactivated
     *
     * @return array
     */
    private function caseFourModulesPreparedDeactivatedWithExtendedClasses()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class_3_extensions', 'extending_1_class',
                'extending_3_classes_with_1_extension', 'extending_3_classes'
            ),

            // module that will be deactivated
            'extending_1_class_3_extensions',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Order::class =>
                                   'extending_1_class/myorder&' .
                                   'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myorder',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'extending_1_class_3_extensions'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'extending_3_classes_with_1_extension' => '1.0',
                    'extending_1_class'                    => '1.0',
                    'extending_3_classes'                  => '1.0',
                ),
            )
        );
    }

    /**
     * Data provider case with 8 modules prepared and no_extending module deactivated
     *
     * @return array
     */
    private function caseEightModulesPreparedDeactivatedWithoutExtending()
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
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_1_class/myorder&with_everything/myorder1',
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
                'disabledModules' => array(
                    'no_extending'
                ),
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
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_files module deactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedDeactivatedWithFiles()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_files', 'no_extending'
            ),

            // module that will be deactivated
            'with_2_files',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'with_2_files'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending' => '1.0',
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_templates module deactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedDeactivatedWithTemplates()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_templates', 'no_extending'
            ),

            // module that will be deactivated
            'with_2_templates',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'with_2_templates'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending' => '1.0',
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_settings module deactivated
     *
     * @return array
     */
    private function caseTwoModulesPreparedDeactivatedWithSettings()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_settings', 'no_extending'
            ),

            // module that will be deactivated
            'with_2_settings',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'with_2_settings'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending' => '1.0',
                ),
            )
        );
    }

    private function prepareProjectConfigurationWitSubshops()
    {
        $projectConfigurationDao = $this->container->get(ProjectConfigurationDaoInterface::class);
        $projectConfiguration = $projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getEnvironmentConfigurations() as $environmentConfiguration) {
            $environmentConfiguration->addShopConfiguration(2, new ShopConfiguration());
        }

        $projectConfigurationDao->persistConfiguration($projectConfiguration);
    }
}
