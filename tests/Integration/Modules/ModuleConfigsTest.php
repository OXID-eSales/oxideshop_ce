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
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $oModule = oxNew('oxModule');
        $oModule->load($sModuleId);

        $this->changeConfiguration($sModuleId, $aConfigsToChange);

        $this->deactivateModule($oModule);
        $this->activateModule($oModule);

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
            $oConfig->saveShopConfVar($sType, $sConfigName, $mValue, null, $sModuleId);
        }
    }
}
