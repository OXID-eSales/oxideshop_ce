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
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

/**
 * Base class for module integration tests.
 *
 * @group module
 */
abstract class BaseModuleTestCase extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Ensure a clean environment before each test
     */
    protected function setUp() {
        parent::setUp();

        $environment = new Environment();
        $environment->clean();
    }

    /**
     * Activates module.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module
     * @param string   $moduleId
     */
    protected function activateModule($module, $moduleId = null)
    {
        if ($moduleId) {
            $module->load($moduleId);
        }
        $moduleCache = oxNew(\OxidEsales\Eshop\Core\Module\ModuleCache::class, $module);
        $moduleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, $moduleCache);

        $moduleInstaller->activate($module);
    }

    /**
     * Deactivates module.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module
     * @param string   $moduleId
     */
    protected function deactivateModule($module, $moduleId = null)
    {
        if ($moduleId) {
            $module->load($moduleId);
        }
        $moduleCache = oxNew(\OxidEsales\Eshop\Core\Module\ModuleCache::class, $module);
        $moduleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, $moduleCache);

        $moduleInstaller->deactivate($module);
    }

    /**
     * Runs all asserts
     *
     * @param array $expectedResult
     */
    protected function runAsserts($expectedResult)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $validator = new Validator($config);

        if (isset($expectedResult['blocks'])) {
            $this->assertTrue($validator->checkBlocks($expectedResult['blocks']), 'Blocks do not match expectations');
        }

        if (isset($expectedResult['extend'])) {
            $this->assertTrue($validator->checkExtensions($expectedResult['extend']), 'Extensions do not match expectations');
        }

        if (isset($expectedResult['files'])) {
            $this->assertTrue($validator->checkFiles($expectedResult['files']), 'Files do not match expectations');
        }

        if (isset($expectedResult['controllers'])) {
            $this->assertTrue($validator->checkControllers($expectedResult['controllers']), 'Controllers do not match expectations');
        }

        if (isset($expectedResult['events'])) {
            $this->assertTrue($validator->checkEvents($expectedResult['events']), 'Events do not match expectations');
        }

        if (isset($expectedResult['settings'])) {
            $this->assertTrue($validator->checkConfigAmount($expectedResult['settings']), 'Configs do not match expectations');
        }

        if (isset($expectedResult['versions'])) {
            $this->assertTrue($validator->checkVersions($expectedResult['versions']), 'Versions do not match expectations');
        }

        if (isset($expectedResult['templates'])) {
            $this->assertTrue($validator->checkTemplates($expectedResult['templates']), 'Templates do not match expectations');
        }

        if (isset($expectedResult['disabledModules'])) {
            $this->assertTrue($validator->checkDisabledModules($expectedResult['disabledModules']), 'Disabled modules do not match expectations');
        }

        if (isset($expectedResult['settings_values'])) {
            $this->assertTrue(
                $validator->checkConfigValues($expectedResult['settings_values']),
                'Config values does not match expectations'
            );
        }
    }
}
