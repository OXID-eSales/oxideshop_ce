<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

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
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $this->installAndActivateModule($sModule);

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

        $this->prepareProjectConfigurationWitSubshops();

        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId, 2);
        }

        $this->installAndActivateModule($sModule, 1);
        $this->installAndActivateModule($sModule, 2);

        $this->deactivateModule(oxNew('oxModule'), $sModule);

        $environment = new Environment();
        $environment->setShopId(2);

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
                    \OxidEsales\Eshop\Application\Model\Order::class   => '' .
                                   'oeTest/extending_1_class/myorder&extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myorder&oeTest/extending_1_class_3_extensions/myorder1',
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
