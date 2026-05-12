<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use Psr\Log\LoggerInterface;

if (!function_exists('getShopBasePath')) {
    /**
     * Returns framework base path.
     *
     * @return string
     */
    function getShopBasePath()
    {
        return OX_BASE_PATH;
    }
}

if (!function_exists('error_404_handler')) {
    /**
     * error_404_handler handler for 404 (page not found) error
     *
     * @param string $sUrl url wich was given, can be not specified in some cases
     */
    function error_404_handler($sUrl = '')
    {
        Registry::getUtils()->handlePageNotFoundError($sUrl);
    }
}

if (!function_exists('isSearchEngineUrl')) {

    /**
     * Returns search engine url status
     *
     * @return bool
     */
    function isSearchEngineUrl()
    {
        return false;
    }
}

if (!function_exists('startProfile')) {
    /**
     * Start profiling
     *
     * @param string $sProfileName name of profile
     */
    function startProfile($sProfileName)
    {
        global $aStartTimes;
        global $executionCounts;
        if (!isset($executionCounts[$sProfileName])) {
            $executionCounts[$sProfileName] = 0;
        }
        if (!isset($aStartTimes[$sProfileName])) {
            $aStartTimes[$sProfileName] = 0;
        }
        $executionCounts[$sProfileName]++;
        $aStartTimes[$sProfileName] = microtime(true);
    }
}

if (!function_exists('stopProfile')) {
    /**
     * Stop profiling
     *
     * @param string $sProfileName name of profile
     */
    function stopProfile($sProfileName)
    {
        global $aProfileTimes;
        global $aStartTimes;
        if (!isset($aProfileTimes[$sProfileName])) {
            $aProfileTimes[$sProfileName] = 0;
        }
        $aProfileTimes[$sProfileName] += microtime(true) - $aStartTimes[$sProfileName];
    }
}

if (!function_exists('getLangTableIdx')) {

    /**
     * Returns language table index
     *
     * @param int $iLangId language id
     *
     * @return string
     */
    function getLangTableIdx($iLangId)
    {
        $iLangPerTable = Registry::getConfig()->getConfigParam("iLangPerTable");
        //#0002718 min language count per table 2
        $iLangPerTable = ($iLangPerTable > 1) ? $iLangPerTable : 8;

        return (int) ($iLangId / $iLangPerTable);
    }
}

if (!function_exists('getLangTableName')) {

    /**
     * Returns language table name
     *
     * @param string $sTable  table name
     * @param int    $iLangId language id
     *
     * @return string
     */
    function getLangTableName($sTable, $iLangId)
    {
        $iTableIdx = getLangTableIdx($iLangId);
        if ($iTableIdx && in_array($sTable, Registry::getLang()->getMultiLangTables(), true)) {
            $sLangTableSuffix = Registry::getConfig()->getConfigParam('sLangTableSuffix');
            $sLangTableSuffix = $sLangTableSuffix ?: '_set';

            $sTable .= $sLangTableSuffix . $iTableIdx;
        }

        return $sTable;
    }
}

if (!function_exists('getLogger')) {
    /**
     * Returns the Logger
     *
     * @return LoggerInterface
     */
    function getLogger()
    {
        return ContainerFacade::get(LoggerInterface::class);
    }
}
