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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

if (!defined('ESHOP_CONFIG_FILE')) {
    define('ESHOP_CONFIG_FILE', 'config.inc.php');
}

if (!function_exists('showErrorIfConfigIsMissing')) {
    function showErrorIfConfigIsMissing()
    {
        $configFileName = __DIR__ . DIRECTORY_SEPARATOR . ESHOP_CONFIG_FILE;

        if (file_exists($configFileName)) {
            return;
        }

        $message = printf(
            "Config file '%s' could not be found! Please use '%s.dist' to make a copy.",
            ESHOP_CONFIG_FILE,
            ESHOP_CONFIG_FILE
        );

        die($message);
    }
}

if (!function_exists('redirectIfShopNotConfigured')) {
    function redirectIfShopNotConfigured()
    {
        $configFileName = __DIR__ . DIRECTORY_SEPARATOR . ESHOP_CONFIG_FILE;

        if (file_exists($configFileName) && strpos(file_get_contents($configFileName), '<dbHost') === false) {
            return;
        }

        header("HTTP/1.1 302 Found");
        header("Location: setup/index.php");
        header("Connection: close");
        exit(0);
    }
}

if (!function_exists('showErrorIfAutoloaderIsMissing')) {
    function showErrorIfAutoloaderIsMissing($fileName)
    {
        if (file_exists($fileName)) {
            return;
        }

        $message = printf(
            "Autoloader file '%s' was not found! Please run 'composer install' to generate it.",
            $fileName
        );

        die($message);
    }
}

if (!function_exists('registerComposerAutoload')) {
    /**
     * Registers auto-loader for shop namespaced classes.
     */
    function registerComposerAutoload()
    {
        class AutoloadConfigFile {
            public function __construct()
            {
                showErrorIfConfigIsMissing();
                include ESHOP_CONFIG_FILE;
            }
        }
        $configFile = new AutoloadConfigFile();
        $autoloaderFileName = $configFile->vendorDirectory . '/autoload.php';

        showErrorIfAutoloaderIsMissing($autoloaderFileName);
        require_once $autoloaderFileName;
    }
}

if (!function_exists('registerVirtualNamespaceAutoLoad')) {
    /**
     * Registers auto-loader for classes of the virtual namespace
     */
    function registerVirtualNamespaceAutoLoad()
    {
        $classMapProvider = new \OxidEsales\Eshop\Core\ClassMapProvider(new \OxidEsales\Eshop\Core\Edition\EditionSelector());
        $classMap = $classMapProvider->getVirtualNamespaceClassMap();
        $virtualNamespaceAutoLoader = new \OxidEsales\EshopCommunity\Core\Autoload\VirtualNamespaceClassAutoload($classMap->getOverridableMap());

        spl_autoload_register(array($virtualNamespaceAutoLoader, 'autoload'));
    }
}

if (!function_exists('registerShopAutoLoad')) {
    /**
     * Registers auto-loader for shop legacy (non-namespaced) classes.
     */
    function registerShopAutoLoad()
    {
        $classMapProvider = new \OxidEsales\Eshop\Core\ClassMapProvider(new \OxidEsales\Eshop\Core\Edition\EditionSelector());
        $notOverridableClassAutoloader = new \OxidEsales\Eshop\Core\Autoload\NotOverridableClassAutoload($classMapProvider->getNotOverridableClassMap());
        spl_autoload_register(array($notOverridableClassAutoloader, 'autoload'));

        $shopAutoloader = new \OxidEsales\Eshop\Core\Autoload\ShopAutoload();
        spl_autoload_register(array($shopAutoloader, 'autoload'));
    }
}

if (!function_exists('registerModuleAutoload')) {
    /**
     * Registers auto-loader for module files and extensions.
     */
    function registerModuleAutoload()
    {
        $moduleAutoloader = new \OxidEsales\Eshop\Core\Autoload\ModuleAutoload();
        spl_autoload_register(array($moduleAutoloader, 'autoload'));
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

/**
 * Returns true in case framework is called from shop administrator environment.
 *
 * @return bool
 */
function isAdmin()
{
    return defined('OX_IS_ADMIN') ? OX_IS_ADMIN : false;
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

/**
 * Displays 'nice' HTML formatted user error.
 * Later this method is hooked as error handler by calling set_error_handler('warningHandler', E_USER_WARNING);
 * #T2008-07-22
 * Not used yet
 *
 * @param int    $iErrorNr   error number
 * @param string $sErrorText error message
 */
function warningHandler($iErrorNr, $sErrorText)
{
    echo "<div class='error_box'>" . Registry::getLang()->translateString('userError') . "<code>[$iErrorNr] $sErrorText</code></div>";
}

/**
 * Dumps $mVar information to vardump.txt file. Used in debugging.
 *
 * @param mixed $mVar     variable
 * @param bool  $blToFile marker to write log info to file (must be true to log)
 */
function dumpVar($mVar, $blToFile = false)
{
    $myConfig = Registry::getConfig();
    if ($blToFile) {
        $out = var_export($mVar, true);
        $f = fopen($myConfig->getConfigParam('sCompileDir') . "/vardump.txt", "a");
        fwrite($f, $out);
        fclose($f);
    } else {
        echo '<pre>';
        var_export($mVar);
        echo '</pre>';
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

/**
 * prints anything given into a file, for debugging
 *
 * @param mixed $mVar variable to debug
 */
function debug($mVar)
{
    $f = fopen('out.txt', 'a');
    $sString = var_export($mVar, true);
    fputs($f, $sString . "\n---------------------------------------------\n");
    fclose($f);
}

/**
 * Sorting for crossselling
 *
 * @param object $a first compare item
 * @param object $b second compre item
 *
 * @deprecated since v6.0.0 (2016-05-16); Moved as anonymous function to Article class.
 *
 * @return integer
 */
function cmpart($a, $b)
{
    if ($a->cnt == $b->cnt) {
        return 0;
    }
    return ($a->cnt < $b->cnt) ? -1 : 1;
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

/**
 * Creates and returns new object. If creation is not available, dies and outputs
 * error message.
 *
 * @param string $className Name of class
 * @param mixed ...$args constructor arguments
 * @throws oxSystemComponentException in case that class does not exists
 *
 * @return object
 */
function oxNew($className)
{
    startProfile('oxNew');
    $arguments = func_get_args();
    $object = call_user_func_array(array(oxUtilsObject::getInstance(), "oxNew"), $arguments);
    stopProfile('oxNew');

    return $object;
}

/**
 * Returns current DB handler
 *
 * @param bool $blAssoc data fetch mode
 *
 * @deprecated since v6.0.0 (2016-05-16); Use oxDb::getDb().
 *
 * @return oxDb
 */
function getDb($blAssoc = true)
{
    return oxDb::getDb($blAssoc);
}

/**
 * Returns string handler
 *
 * @deprecated since v6.0.0 (2016-05-16); Use oxStr::getStr().
 *
 * @return oxStrRegular|oxStrMb
 */
function getStr()
{
    return oxStr::getStr();
}

/**
 * Sets template content from cache. In demoshop enables security mode.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName    name of template
 * @param string &$sTplSource Template source
 * @param object $oSmarty     not used here
 *
 * @return bool
 */
function ox_get_template($sTplName, &$sTplSource, $oSmarty)
{
    $sTplSource = $oSmarty->oxidcache->value;
    if (Registry::getConfig()->isDemoShop()) {
        $oSmarty->security = true;
    }

    return true;
}

/**
 * Sets time for smarty templates recompilation. If oxidtimecache is set, smarty will cache templates for this period.
 * Otherwise templates will always be compiled.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName       name of template
 * @param string &$iTplTimestamp template timestamp referense
 * @param object $oSmarty        not used here
 *
 * @return bool
 */
function ox_get_timestamp($sTplName, &$iTplTimestamp, $oSmarty)
{
    $iTplTimestamp = isset($oSmarty->oxidtimecache->value) ? $oSmarty->oxidtimecache->value : time();
    return true;
}

/**
 * Dummy function, required for smarty plugin registration.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 *
 * @return bool
 */
function ox_get_secure($sTplName, $oSmarty)
{
    return true;
}

/**
 * Dummy function, required for smarty plugin registration.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 */
function ox_get_trusted($sTplName, $oSmarty)
{
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
