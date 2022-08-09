<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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

        return $blParamsCountMatch && $this->checkBlockValues($aExpectedBlocks, $aBlocksToCheck);
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
     * @param array $aExpectedBlocks
     * @param array $aBlocksToCheck
     *
     * @return bool
     */
    protected function checkBlockValues($aExpectedBlocks, $aBlocksToCheck)
    {
        foreach ($aExpectedBlocks as $aValues) {
            if (!$this->matchingBlockExists($aValues, $aBlocksToCheck)) {
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
    protected function matchingBlockExists($aBlockValues, $aBlocks)
    {
        foreach ($aBlocks as $aBlock) {
            if (!$this->matchBlocks($aBlockValues, $aBlock)) {
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
    protected function matchBlocks($aBlockValues, $aBlock)
    {
        foreach ($aBlockValues as $sValue) {
            if (!in_array($sValue, $aBlock)) {
                return false;
            }
        }

        return true;
    }
}
