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


require_once realpath(dirname(__FILE__)) . '/validator.php';
require_once realpath(dirname(__FILE__)) . '/environment.php';

abstract class BaseModuleTestCase extends OxidTestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $oEnvironment = new Environment();
        $oEnvironment->clean();
        parent::tearDown();
    }

    /**
     * Activates module.
     *
     * @param oxModule $oModule
     * @param string   $sModuleId
     */
    protected function _activateModule($oModule, $sModuleId = null)
    {
        if ($sModuleId) {
            $oModule->load($sModuleId);
        }
        /** @var oxModuleCache $oModuleCache */
        $oModuleCache = oxNew('oxModuleCache', $oModule);
        /** @var oxModuleInstaller $oModuleInstaller */
        $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

        $oModuleInstaller->activate($oModule);
    }

    /**
     * Deactivates module.
     *
     * @param oxModule $oModule
     * @param string   $sModuleId
     */
    protected function _deactivateModule($oModule, $sModuleId = null)
    {
        if ($sModuleId) {
            $oModule->load($sModuleId);
        }
        /** @var oxModuleCache $oModuleCache */
        $oModuleCache = oxNew('oxModuleCache', $oModule);
        /** @var oxModuleInstaller $oModuleInstaller */
        $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

        $oModuleInstaller->deactivate($oModule);
    }

    /**
     * Runs all asserts
     *
     * @param $aExpectedResult
     */
    protected function _runAsserts($aExpectedResult)
    {
        $oConfig = oxRegistry::getConfig();

        $oValidator = new Validator($oConfig);

        if (isset($aExpectedResult['blocks'])) {
            $this->assertTrue($oValidator->checkBlocks($aExpectedResult['blocks']), 'Blocks do not match expectations');
        }

        if (isset($aExpectedResult['extend'])) {
            $this->assertTrue($oValidator->checkExtensions($aExpectedResult['extend']), 'Extensions do not match expectations');
        }

        if (isset($aExpectedResult['files'])) {
            $this->assertTrue($oValidator->checkFiles($aExpectedResult['files']), 'Files do not match expectations');
        }

        if (isset($aExpectedResult['events'])) {
            $this->assertTrue($oValidator->checkEvents($aExpectedResult['events']), 'Events do not match expectations');
        }

        if (isset($aExpectedResult['settings'])) {
            $this->assertTrue($oValidator->checkConfigAmount($aExpectedResult['settings']), 'Configs do not match expectations');
        }

        if (isset($aExpectedResult['versions'])) {
            $this->assertTrue($oValidator->checkVersions($aExpectedResult['versions']), 'Versions do not match expectations');
        }

        if (isset($aExpectedResult['templates'])) {
            $this->assertTrue($oValidator->checkTemplates($aExpectedResult['templates']), 'Templates do not match expectations');
        }

        if (isset($aExpectedResult['disabledModules'])) {
            $this->assertTrue($oValidator->checkDisabledModules($aExpectedResult['disabledModules']), 'Disabled modules do not match expectations');
        }

        if (isset($aExpectedResult['settings_values'])) {
            $this->assertTrue(
                $oValidator->checkConfigValues($aExpectedResult['settings_values'])
                , 'Config values does not match expectations'
            );
        }
    }

}
 