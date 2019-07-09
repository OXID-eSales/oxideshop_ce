<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use oxModuleList;
use PHPUnit\Framework\MockObject\MockObject;

class ModuleRemoveTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleDeactivation()
    {
        return array(
            $this->caseSevenModulesPreparedRemovedOneExtensionWithEverything(),
            $this->caseSevenModulesPreparedRemovedAllExtensionWithEverything(),
            $this->caseSevenModulesPreparedRemovedOneExtensionWithMetadataV2(),

        );
    }

    /**
     * Test check shop environment after module deactivation
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array $aInstallModules
     * @param array $aRemovedExtensions
     * @param array $aResultToAssert
     */
    public function testModuleRemove($aInstallModules, $aRemovedExtensions, $aResultToAssert)
    {
        foreach ($aInstallModules as $id) {
            $this->installAndActivateModule($id);
        }

        /** @var oxModuleList|MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getDeletedExtensions'));
        $oModuleList->expects($this->any())->method('getDeletedExtensions')->will($this->returnValue($aRemovedExtensions));

        $oModuleList->cleanup();
        $this->runAsserts($aResultToAssert);
    }

    /**
     * Test check shop environment after module deactivation in subshop.
     *
     * @group quarantine
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array $aInstallModules
     * @param array $aRemovedExtensions
     * @param array $aResultToAssert
     */
    public function testModuleRemoveInSubShop($aInstallModules, $aRemovedExtensions, $aResultToAssert)
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }

        $this->prepareProjectConfigurationWitSubshops();

        foreach ($aInstallModules as $id) {
            $this->installAndActivateModule($id, 1);
        }

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsObject::class, null);

        $oEnvironment = new Environment();
        $oEnvironment->setShopId(2);
        $_POST['shp'] = 2;

        foreach ($aInstallModules as $id) {
            $this->installAndActivateModule($id, 2);
        }

        /** @var oxModuleList|MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getDeletedExtensions'));
        $oModuleList->expects($this->any())->method('getDeletedExtensions')->will($this->returnValue($aRemovedExtensions));

        $oModuleList->cleanup();

        //Assert on subshop
        $this->runAsserts($aResultToAssert);

        $this->markTestIncomplete('Skipped till cleanup for subshops will be fixed');

        //Assert on main shop
        $oEnvironment->setShopId(1);
        $this->runAsserts($aResultToAssert);
    }

    /**
     * @return array
     */
    private function caseSevenModulesPreparedRemovedOneExtensionWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events'
            ),

            // extensions that will be removed
            array(
                'with_everything' => array(
                    'extensions' => array(
                        'oxuser' => 'with_everything/myuser',
                    )
                )
            ),

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Order::class => 'oeTest/extending_1_class/myorder',
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
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    private function caseSevenModulesPreparedRemovedAllExtensionWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events'
            ),

            // extensions that will be removed
            array(
                'with_everything' => array(
                    'extensions' => array(
                        'oxarticle' => 'with_everything/myarticle',
                        'oxorder'   => 'with_everything/myorder1',
                        'oxuser'    => 'with_everything/myuser',
                    )
                )
            ),

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Order::class => 'oeTest/extending_1_class/myorder',
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
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    private function caseSevenModulesPreparedRemovedOneExtensionWithMetadataV2()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_metadata_v2', 'with_more_metadata_v2', 'with_events',
            ),

            // extensions that will be removed
            array(
                'with_more_metadata_v2' => array(
                    'extensions' => array(
                        'oxarticle' => 'with_more_metadata_v2/myarticle'
                    )
                )
            ),
            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder',
                    \OxidEsales\Eshop\Application\Model\Article::class =>'with_metadata_v2/myarticle'
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
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'with_metadata_v2' => array(
                        'order_special.tpl'      => 'with_metadata_v2/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl'   => 'with_metadata_v2/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_metadata_v2'   => '1.0',
                    'with_events'        => '1.0',
                ),
                'controllers'  => [
                    'with_metadata_v2' => [
                        'with_metadata_v2_mymodulecontroller' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\testData\modules\with_metadata_v2\MyModuleController',
                        'with_metadata_v2_myothermodulecontroller' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\testData\modules\with_metadata_v2\MyOtherModuleController'
                    ]
                ]
            ),
        );
    }

    private function prepareProjectConfigurationWitSubshops()
    {
        $projectConfigurationDao = $this->container->get(ProjectConfigurationDaoInterface::class);
        $projectConfiguration = $projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getEnvironmentConfigurations() as $environmentConfiguration) {
            $environmentConfiguration->addShopConfiguration(2, new ShopConfiguration());
        }

        $projectConfigurationDao->save($projectConfiguration);
    }
}
