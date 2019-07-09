<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

class ModuleIsActiveTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleIsActive()
    {
        return array(
            array(
                array('extending_1_class', 'with_2_templates', 'with_everything'),
                array('extending_1_class', 'with_everything'),
                array(
                    'active'    => array('with_2_templates'),
                    'notActive' => array('extending_1_class', 'with_everything'),
                )
            ),
            array(
                array('extending_1_class', 'with_2_templates', 'with_everything'),
                array(),
                array(
                    'active'    => array('extending_1_class', 'with_2_templates', 'with_everything'),
                    'notActive' => array(),
                )
            ),

            array(
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array(
                    'active'    => array(),
                    'notActive' => array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                )
            ),

            array(
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array(
                    'active'    => array(),
                    'notActive' => array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                )
            ),
            array(
                array('no_extending'),
                array(),
                array(
                    'active'    => array('no_extending'),
                    'notActive' => array(),
                )
            ),
            array(
                array('no_extending'),
                array('no_extending'),
                array(
                    'active'    => array(),
                    'notActive' => array('no_extending'),
                )
            ),

        );
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleIsActive
     *
     * @param array $aInstallModules
     * @param array $aDeactivateModules
     * @param array $aResultToAssert
     */
    public function testIsActive($aInstallModules, $aDeactivateModules, $aResultToAssert)
    {
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        //deactivation
        $oModule = oxNew('oxModule');

        foreach ($aDeactivateModules as $sModule) {
            $this->deactivateModule($oModule, $sModule);
        }

        //assertion
        foreach ($aResultToAssert['active'] as $sModule) {
            $oModule->load($sModule);
            $this->assertTrue($oModule->isActive());
        }

        foreach ($aResultToAssert['notActive'] as $sModule) {
            $oModule->load($sModule);
            $this->assertFalse($oModule->isActive());
        }
    }
}
