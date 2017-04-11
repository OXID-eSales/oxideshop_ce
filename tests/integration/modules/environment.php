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

class Environment
{

    /**
     * Shop Id in which will be prepared environment.
     *
     * @var int
     */
    private $_iShopId;

    /**
     * Sets shop Id for modules environment.
     *
     * @param int $iShopId
     */
    public function setShopId($iShopId)
    {
        $this->_iShopId = $iShopId;
        oxRegistry::getConfig()->setShopId($iShopId);
        $this->_loadShopParameters();
    }

    /**
     * Returns shop Id.
     *
     * @return int
     */
    public function getShopId()
    {
        return is_null($this->_iShopId) ? 1 : $this->_iShopId;
    }

    /**
     * Loads and activates modules by given IDs.
     *
     * @param null $aModules
     *
     * @throws Exception
     */
    public function prepare($aModules = null)
    {
        $this->clean();
        $oConfig = oxRegistry::getConfig();
        $oConfig->setShopId($this->getShopId());
        $oConfig->setConfigParam('sShopDir', $this->_getPathToTestDataDirectory());

        if (is_null($aModules)) {
            $aModules = $this->_getAllModules();
        }

        $this->activateModules($aModules);
    }

    /**
     * Cleans modules environment.
     */
    public function clean()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('aModules', null);
        $oConfig->setConfigParam('aModuleTemplates', null);
        $oConfig->setConfigParam('aDisabledModules', array());
        $oConfig->setConfigParam('aModuleFiles', null);
        $oConfig->setConfigParam('aModuleVersions', null);
        $oConfig->setConfigParam('aModuleEvents', null);

        $oDb = oxDb::getDb();
        $oDb->execute("DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'module:%' OR `oxvarname` LIKE '%Module%'");
        $oDb->execute('TRUNCATE `oxconfigdisplay`');
        $oDb->execute('TRUNCATE `oxtplblocks`');
    }

    /**
     * Activates given modules.
     *
     * @param $aModules
     *
     * @throws Exception
     */
    public function activateModules($aModules)
    {
        $oModule = new oxModule();
        foreach ($aModules as $sModuleId) {
            if ($oModule->load($sModuleId)) {
                /** @var oxModuleCache $oModuleCache */
                $oModuleCache = oxNew('oxModuleCache', $oModule);
                /** @var oxModuleInstaller $oModuleInstaller */
                $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

                if (!$oModuleInstaller->activate($oModule)) {
                    throw new Exception("Module $sModuleId was not activated.");
                }
            } else {
                throw new Exception("Module $sModuleId was not activated.");
            }

        }
    }

    /**
     * Returns fixtures directory.
     *
     * @return string
     */
    private function _getPathToTestDataDirectory()
    {
        return realpath(dirname(__FILE__)) . '/testData/';
    }

    /**
     * Scans directory and returns modules IDs.
     *
     * @return array
     */
    private function _getAllModules()
    {
        $aModules = array_diff(scandir($this->_getPathToTestDataDirectory() . 'modules'), array('..', '.'));

        return $aModules;
    }

    /**
     * Loads config parameters from DB and sets to config.
     */
    private function _loadShopParameters()
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
     * @param $sVarName
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