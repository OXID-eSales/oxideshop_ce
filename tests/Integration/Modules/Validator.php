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

use oxConfig;
use oxDb;

class Validator
{
    /**
     * Config object.
     *
     * @var object
     */
    private $config;

    /**
     * Sets oxConfig and Shop ID
     *
     * @param oxConfig $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Returns config object.
     *
     * @return oxConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Asserts that module templates match expected templates
     *
     * @param array $aExpectedTemplates
     *
     * @return bool
     */
    public function checkTemplates($aExpectedTemplates)
    {
        $aTemplatesToCheck = $this->getConfig()->getConfigParam('aModuleTemplates');
        $aTemplatesToCheck = is_null($aTemplatesToCheck) ? array() : $aTemplatesToCheck;

        return ($aExpectedTemplates == $aTemplatesToCheck);
    }

    /**
     * Asserts that module blocks match expected blocks
     *
     * @param array $aExpectedBlocks
     *
     * @return bool
     */
    public function checkBlocks($aExpectedBlocks)
    {
        $oDb = oxDb::getDb();
        $sQuery = "select * from oxtplblocks where oxshopid = {$this->getConfig()->getShopId()}";
        $aBlocksToCheck = $oDb->getAll($sQuery);

        $blParamsCountMatch = count($aExpectedBlocks) == count($aBlocksToCheck);

        return $blParamsCountMatch && $this->_checkBlockValues($aExpectedBlocks, $aBlocksToCheck);
    }

    /**
     * Asserts that module extensions match expected extensions
     *
     * @param array $aExpectedExtensions
     *
     * @return bool
     */
    public function checkExtensions($aExpectedExtensions)
    {
        $aExtensionsToCheck = $this->getConfig()->getConfigParam('aModules');

        return ($aExpectedExtensions === $aExtensionsToCheck);
    }

    /**
     * Asserts that disabled module is in disabled modules list
     *
     * @param array $aExpectedDisabledModules
     *
     * @return bool
     */
    public function checkDisabledModules($aExpectedDisabledModules)
    {
        $aDisabledModules = $this->getConfig()->getConfigParam('aDisabledModules');

        return $aExpectedDisabledModules == $aDisabledModules;
    }

    /**
     * Asserts that module files match expected files
     *
     * @param array $aExpectedFiles
     *
     * @return bool
     */
    public function checkFiles($aExpectedFiles)
    {
        $aModuleFilesToCheck = $this->getConfig()->getConfigParam('aModuleFiles');
        $aModuleFilesToCheck = is_null($aModuleFilesToCheck) ? array() : $aModuleFilesToCheck;

        return $aExpectedFiles == $aModuleFilesToCheck;
    }

    /**
     * Asserts that module controllers match expected files
     *
     * @param array $expectedControllers
     *
     * @return bool
     */
    public function checkControllers($expectedControllers)
    {
        $moduleControllersToCheck = $this->getConfig()->getConfigParam('aModuleControllers');
        $moduleControllersToCheck = is_null($moduleControllersToCheck) ? array() : $moduleControllersToCheck;

        return $expectedControllers == $moduleControllersToCheck;
    }

    /**
     * Asserts that module configs match expected configs
     *
     * @param array $aExpectedConfigs
     *
     * @return bool
     */
    public function checkConfigAmount($aExpectedConfigs)
    {
        $oDb = oxDb::getDb();
        $sQuery = "select c.oxvarname
                   from  oxconfig c inner join oxconfigdisplay d
                   on c.oxvarname = d.oxcfgvarname  and c.oxmodule = d.oxcfgmodule
                   where oxmodule like 'module:%' and c.oxshopid = {$this->getConfig()->getShopId()}";
        $aConfigsToCheck = $oDb->getAll($sQuery);

        return count($aExpectedConfigs) == count($aConfigsToCheck);
    }

    /**
     * Asserts that module configs match expected values.
     *
     * @param array $aExpectedConfigs configs to check
     *
     * @return bool
     */
    public function checkConfigValues($aExpectedConfigs)
    {
        $oConfig = $this->getConfig();
        foreach ($aExpectedConfigs as $aExpectedConfig) {
            $oConfigValueInShop = $oConfig->getConfigParam($aExpectedConfig['name']);
            if ($oConfigValueInShop != $aExpectedConfig['value']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Asserts that module version match expected version
     *
     * @param array $aExpectedVersions
     *
     * @return bool
     */
    public function checkVersions($aExpectedVersions)
    {
        $aModuleVersionsToCheck = $this->getConfig()->getConfigParam('aModuleVersions');
        $aModuleVersionsToCheck = is_null($aModuleVersionsToCheck) ? array() : $aModuleVersionsToCheck;

        return $aExpectedVersions == $aModuleVersionsToCheck;
    }

    /**
     * Asserts that module version match expected version
     *
     * @param array $aExpectedEvents
     *
     * @return bool
     */
    public function checkEvents($aExpectedEvents)
    {
        $aModuleEventsToCheck = $this->getConfig()->getConfigParam('aModuleEvents');
        $aModuleEventsToCheck = is_null($aModuleEventsToCheck) ? array() : $aModuleEventsToCheck;

        return $aExpectedEvents == $aModuleEventsToCheck;
    }

    /**
     * @param array $aExpectedBlocks
     * @param array $aBlocksToCheck
     *
     * @return bool
     */
    protected function _checkBlockValues($aExpectedBlocks, $aBlocksToCheck)
    {
        foreach ($aExpectedBlocks as $aValues) {
            if (!$this->_matchingBlockExists($aValues, $aBlocksToCheck)) {
                return false;
            };
        }

        return true;
    }

    /**
     * @param array $aBlockValues
     * @param array $aBlocks
     *
     * @return bool
     */
    protected function _matchingBlockExists($aBlockValues, $aBlocks)
    {
        foreach ($aBlocks as $aBlock) {
            if (!$this->_matchBlocks($aBlockValues, $aBlock)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param array $aBlockValues
     * @param array $aBlock
     *
     * @return bool
     */
    protected function _matchBlocks($aBlockValues, $aBlock)
    {
        foreach ($aBlockValues as $sValue) {
            if (!in_array($sValue, $aBlock)) {
                return false;
            }
        }

        return true;
    }
}
