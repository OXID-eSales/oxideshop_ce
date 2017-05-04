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

class Integration_Modules_ModuleConfigsTest extends BaseModuleTestCase
{

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
     */
    public function testModuleConfigs($aInstallModules, $sModuleId, $aConfigsToChange, $aResultToAsserts)
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $oModule = new oxModule();
        $oModule->load($sModuleId);

        $this->_changeConfiguration($sModuleId, $aConfigsToChange);

        $this->_deactivateModule($oModule);
        $this->_activateModule($oModule);

        $this->_runAsserts($aResultToAsserts, $sModuleId);
    }

    /**
     * @param $sModuleId
     * @param $aConfigsToChange
     */
    private function _changeConfiguration($sModuleId, $aConfigsToChange)
    {
        $oConfig = oxRegistry::getConfig();
        foreach ($aConfigsToChange as $aConfig) {
            $sConfigName = $aConfig['name'];
            $sType = $aConfig['type'];
            $mValue = $aConfig['value'];
            $oConfig->saveShopConfVar($sType, $sConfigName, $mValue, null, $sModuleId);
        }
    }
}
 