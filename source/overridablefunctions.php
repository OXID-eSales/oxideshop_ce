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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

if (!defined('ESHOP_CONFIG_FILE')) {
    define('ESHOP_CONFIG_FILE', 'config.inc.php');
}

if (!function_exists('redirectIfShopNotConfigured')) {
    function redirectIfShopNotConfigured()
    {
        $configFileName = __DIR__ . DIRECTORY_SEPARATOR . ESHOP_CONFIG_FILE;

        if (file_exists($configFileName) && strpos(file_get_contents($configFileName), '<dbHost') === false) {
            return;
        }

        $message = sprintf(
            "Config file '%s' is not updated! Please navigate to '/Setup' or update '%s' manually.",
            ESHOP_CONFIG_FILE,
            ESHOP_CONFIG_FILE
        );

        header("HTTP/1.1 302 Found");
        header("Location: Setup/index.php");
        header("Connection: close");

        die($message);
    }
}

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
     *
     * @return void
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
        global $aExecutionCounts;
        if (!isset($aExecutionCounts[$sProfileName])) {
            $aExecutionCounts[$sProfileName] = 0;
        }
        if (!isset($aStartTimes[$sProfileName])) {
            $aStartTimes[$sProfileName] = 0;
        }
        $aExecutionCounts[$sProfileName]++;
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

        $iTableIdx = (int) ($iLangId / $iLangPerTable);

        return $iTableIdx;
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
        if ($iTableIdx && in_array($sTable, Registry::getLang()->getMultiLangTables())) {
            $sLangTableSuffix = Registry::getConfig()->getConfigParam("sLangTableSuffix");
            $sLangTableSuffix = $sLangTableSuffix ? $sLangTableSuffix : "_set";

            $sTable .= $sLangTableSuffix . $iTableIdx;
        }

        return $sTable;
    }
}

if (!function_exists('getViewName')) {

    /**
     * Return the view name of the given table if a view exists, otherwise the table name itself
     *
     * @param string $table      table name
     * @param int    $languageId language id [optional]
     * @param string $shopId     shop id, otherwise config->myshopid is used [optional]
     *
     * @deprecated since v6.0.0 (2016-05-16); Use oxTableViewNameGenerator::getViewName().
     *
     * @return string
     */
    function getViewName($table, $languageId = null, $shopId = null)
    {
        $viewNameGenerator = Registry::get('oxTableViewNameGenerator');

        return $viewNameGenerator->getViewName($table, $languageId, $shopId);
    }
}

if (!function_exists('getRequestUrl')) {
    /**
     * Returns request url, which was executed to render current page view
     *
     * @param string $sParams     Parameters to object
     * @param bool   $blReturnUrl If return url
     *
     * @deprecated since v6.0.0 (2016-05-16); Use OxidEsales\Eshop\Core\Request::getRequestUrl().
     *
     * @return string
     */
    function getRequestUrl($sParams = '', $blReturnUrl = false)
    {
        return Registry::get(Request::class)->getRequestUrl($sParams, $blReturnUrl);
    }
}
