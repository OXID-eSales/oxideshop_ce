<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxRegistry;

class ModuleConfigsTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleIsActive()
    {
        return array(
            array(
                // modules to be activated during test preparation
                array(
                    'with_everything', 'no_extending', 'with_2_settings'
                ),

                // module that will be reactivated
                'with_everything',

                // Settings to be changed after first activation
                array(
                    array('name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'false'),
                    array('name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some different name'),
                ),

                // environment asserts
                array(
                    'settings_values' => array(
                        array('name' => 'blCheckConfirm', 'type' => 'bool', 'value' => false),
                        array('name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some different name'),
                    ),
                ),
            ),
        );
    }

    /**
     * Tests check if changed module config values are the same after deactivation / activation
     *
     * @dataProvider providerModuleIsActive
     *
     * @param array  $aInstallModules
     * @param string $sModuleId
     * @param array  $aConfigsToChange
     * @param array  $aResultToAsserts
     */
    public function testModuleConfigs($aInstallModules, $sModuleId, $aConfigsToChange, $aResultToAsserts)
    {
        $this->markTestSkipped('Wont work. Now we overwrite values in DB.');

        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');
        $oModule->load($sModuleId);

        $this->changeConfiguration($sModuleId, $aConfigsToChange);

        $this->deactivateModule($oModule);
        $this->installAndActivateModule($sModuleId);

        $this->runAsserts($aResultToAsserts);
    }

    /**
     * @param string $sModuleId
     * @param array  $aConfigsToChange
     */
    private function changeConfiguration($sModuleId, $aConfigsToChange)
    {
        $oConfig = oxRegistry::getConfig();
        foreach ($aConfigsToChange as $aConfig) {
            $sConfigName = $aConfig['name'];
            $sType = $aConfig['type'];
            $mValue = $aConfig['value'];
            $oConfig->saveShopConfVar($sType, $sConfigName, $mValue, null, 'module:' . $sModuleId);
        }
    }
}
