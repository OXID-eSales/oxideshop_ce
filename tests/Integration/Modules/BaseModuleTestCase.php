<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
