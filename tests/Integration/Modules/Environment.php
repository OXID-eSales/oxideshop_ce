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
namespace Integration\Modules;

use Exception;
use oxDb;
use oxRegistry;

class Environment
{
    /**
     * Shop Id in which will be prepared environment.
     *
     * @var int
     */
    private $shopId;

    /**
     * Sets shop Id for modules environment.
     *
     * @param int $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        oxRegistry::getConfig()->setShopId($shopId);
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
     * Loads and activates modules by given IDs.
     *
     * @param null $modules
     *
     * @throws Exception
     */
    public function prepare($modules = null)
    {
        $this->clean();
        $oConfig = oxRegistry::getConfig();
        $oConfig->setShopId($this->getShopId());
        $oConfig->setConfigParam('sShopDir', $this->getPathToTestDataDirectory());

        if (is_null($modules)) {
            $modules = $this->getAllModules();
        }

        $this->activateModules($modules);
    }

    /**
     * Cleans modules environment.
     */
    public function clean()
    {
        $config = oxRegistry::getConfig();
        $config->setConfigParam('aModules', null);
        $config->setConfigParam('aModuleTemplates', null);
        $config->setConfigParam('aDisabledModules', array());
        $config->setConfigParam('aModuleFiles', null);
        $config->setConfigParam('aModuleVersions', null);
        $config->setConfigParam('aModuleEvents', null);

        $database = oxDb::getDb();
        $database->execute("DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'module:%' OR `oxvarname` LIKE '%Module%'");
        $database->execute('TRUNCATE `oxconfigdisplay`');
        $database->execute('TRUNCATE `oxtplblocks`');
    }

    /**
     * Activates given modules.
     *
     * @param array $modules
     *
     * @throws Exception
     */
    public function activateModules($modules)
    {
        $oModule = oxNew('oxModule');
        foreach ($modules as $moduleId) {
            if ($oModule->load($moduleId)) {
                $moduleCache = oxNew('oxModuleCache', $oModule);
                $moduleInstaller = oxNew('oxModuleInstaller', $moduleCache);

                if (!$moduleInstaller->activate($oModule)) {
                    throw new Exception("Module $moduleId was not activated.");
                }
            } else {
                throw new Exception("Module $moduleId was not activated.");
            }
        }
    }

    /**
     * Returns fixtures directory.
     *
     * @return string
     */
    private function getPathToTestDataDirectory()
    {
        return realpath(__DIR__) . '/TestData/';
    }

    /**
     * Scans directory and returns modules IDs.
     *
     * @return array
     */
    private function getAllModules()
    {
        $aModules = array_diff(scandir($this->getPathToTestDataDirectory() . 'modules'), array('..', '.'));

        return $aModules;
    }

    /**
     * Loads config parameters from DB and sets to config.
     */
    private function loadShopParameters()
    {
        $aParameters = array(
            'aModules', 'aModuleEvents', 'aModuleVersions', 'aModuleFiles', 'aDisabledModules', 'aModuleTemplates'
        );
        foreach ($aParameters as $sParameter) {
            oxRegistry::getConfig()->setConfigParam($sParameter, $this->_getConfigValueFromDB($sParameter));
        }
    }

    /**
     * Returns config values from table oxconfig by field- oxvarname.
     *
     * @param string $sVarName
     *
     * @return array
     */
    private function _getConfigValueFromDB($sVarName)
    {
        $oDb = oxDb::getDb();
        $sQuery = "SELECT " . oxRegistry::getConfig()->getDecodeValueQuery() . "
                   FROM `oxconfig`
                   WHERE `OXVARNAME` = '{$sVarName}'
                   AND `OXSHOPID` = {$this->getShopId()}";

        $sResult = $oDb->getOne($sQuery);
        $aExtensionsToCheck = $sResult ? unserialize($sResult) : array();

        return $aExtensionsToCheck;
    }
}
