<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use Exception;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Environment
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Modules
 */
class Environment
{

    /**
     * Shop Id in which will be prepared environment.
     *
     * @var int
     */
    protected $shopId;

    /**
     * Path to be used as sShopPath for testing
     *
     * @var string
     */
    protected $path = null;

    /**
     * Name of fixture directory containing the test modules
     *
     * @var string
     */
    protected $fixtureDirectory = 'TestData';

    /**
     * @var \PHPUnit\Framework\TestCase
     */
    protected $phpUnit;

    /**
     *
     */
    public function __construct($path = __DIR__)
    {
        $this->path = $path;
    }

    /**
     * Sets shop Id for modules environment.
     *
     * @param int $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        Registry::getConfig()->setShopId($shopId);
        $this->loadShopParameters();
    }

    /**
     * Returns shop Id.
     *
     * @return int
     */
    public function getShopId()
    {
        return is_null($this->shopId) ? 1 : $this->shopId;
    }

    /**
     * In the shop the modules get validated e.g. if they extend an edition namespace.
     * If you use this method, a mock prevents the validation of modules. This method has to be called
     * before the method prepare()
     *
     * @param \PHPUnit\Framework\TestCase
     */
    public function doNotValidateModules(\PHPUnit\Framework\TestCase $phpUnit)
    {
        $this->phpUnit = $phpUnit;
    }

    /**
     * Loads and activates modules by given IDs.
     *
     * @param null $modules
     *
     * @throws Exception
     */
    public function prepare($modules = null)
    {
        $modules = $this->refreshAvailableModules($modules);
        $this->activateModules($modules);
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    private function refreshAvailableModules($modules = null)
    {
        $this->clean();
        $this->setShopConfigParameters();

        if (is_null($modules)) {
            $modules = $this->getAllModules();
        }

        return $modules;
    }

    /**
     * Cleans modules environment.
     */
    public function clean()
    {
        $config = Registry::getConfig();
        $config->setConfigParam('aModules', null);
        $config->setConfigParam('aModuleTemplates', null);
        $config->setConfigParam('aDisabledModules', array());
        $config->setConfigParam('aModulePaths', array());
        $config->setConfigParam('aModuleFiles', null);
        $config->setConfigParam('aModuleVersions', null);
        $config->setConfigParam('aModuleEvents', null);
        $config->setConfigParam('aModuleControllers', null);

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->execute("DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'module:%' OR `oxvarname` LIKE '%Module%'");
        $database->execute('TRUNCATE `oxconfigdisplay`');
        $database->execute('TRUNCATE `oxtplblocks`');
    }

    /**
     * Set the shop config parameters shopId and sShopDir
     */
    public function setShopConfigParameters()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setShopId($this->getShopId());
        $oConfig->setConfigParam('sShopDir', $this->getPathToTestDataDirectory());
        \OxidEsales\Eshop\Core\Registry::get("oxConfigFile")->setVar("sShopDir", $this->getPathToTestDataDirectory());
    }

    /**
     * Activates given modules.
     *
     * @param array $modules
     *
     * @throws Exception If the module could not be loaded or activated.
     */
    public function activateModules($modules)
    {
        foreach ($modules as $moduleId) {
            $this->activateModuleById($moduleId);
        }
    }

    /**
     * Activate the module with the given ID.
     *
     * @param string $moduleId The ID of the module to activate.
     *
     * @throws Exception If the module could not be loaded or activated.
     */
    public function activateModuleById($moduleId)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        $module->load($moduleId);

        $moduleInstaller = $this->getModuleInstaller($module);
        $moduleInstaller->activate($module);
    }


    /**
     * Deactivate a module, given by its ID.
     *
     * @param string $moduleId The ID of the module we want to deactivate.
     *
     * @throws Exception
     */
    public function deactivateModuleById($moduleId)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        if ($module->load($moduleId)) {
            $moduleInstaller = $this->getModuleInstaller($module);

            if (!$moduleInstaller->deactivate($module)) {
                throw new Exception("Module $moduleId was not deactivated.");
            }
        } else {
            throw new Exception("Module $moduleId was not deactivated. Couldn't find/load the module.");
        }
    }

    /**
     * Returns fixtures directory.
     *
     * @return string
     */
    protected function getPathToTestDataDirectory()
    {
        return realpath($this->path) . '/' . $this->fixtureDirectory . '/';
    }

    /**
     * Scans directory and returns modules IDs.
     *
     * @return array
     */
    protected function getAllModules()
    {
        $modules = array_diff(scandir($this->getPathToTestDataDirectory() . 'modules'), array('..', '.'));

        return $modules;
    }

    /**
     * Loads config parameters from DB and sets to config.
     */
    protected function loadShopParameters()
    {
        $aParameters = array(
            'aModules', 'aModuleEvents', 'aModuleVersions', 'aModuleFiles', 'aDisabledModules', 'aModuleTemplates', 'aModuleControllers'
        );
        foreach ($aParameters as $sParameter) {
            Registry::getConfig()->setConfigParam($sParameter, $this->_getConfigValueFromDB($sParameter));
        }
    }

    /**
     * Returns config values from table oxconfig by field- oxvarname.
     *
     * @param string $sVarName
     *
     * @return array
     */
    protected function _getConfigValueFromDB($sVarName)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQuery = "SELECT " . Registry::getConfig()->getDecodeValueQuery() . "
                   FROM `oxconfig`
                   WHERE `OXVARNAME` = '{$sVarName}'
                   AND `OXSHOPID` = {$this->getShopId()}";

        $sResult = $db->getOne($sQuery);
        $aExtensionsToCheck = $sResult ? unserialize($sResult) : array();

        return $aExtensionsToCheck;
    }

    /**
     * Get the module installer with a given module set in the module cache.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module
     *
     * @return \OxidEsales\Eshop\Core\Module\ModuleInstaller|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getModuleInstaller($module)
    {
        $moduleCache = oxNew(\OxidEsales\Eshop\Core\Module\ModuleCache::class, $module);

        if (isset($this->phpUnit)) {
            return $this->phpUnit->getMock(
                \OxidEsales\Eshop\Core\Module\ModuleInstaller::class,
                ['validateMetadataExtendSection'],
                [$moduleCache]
            );
        }

        return oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, $moduleCache);
    }
}
