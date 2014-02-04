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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Includes $sClass class file
 *
 * @param string $sClass classname
 *
 * @return null
 */
function oxAutoload( $sClass )
{
    startProfile("oxAutoload");
    $sClass = basename( $sClass );
    $sClass = strtolower($sClass);

    static $sBasePath  = null;
    static $aClassDirs = null;

    // preventing infinite loop
    static $aTriedClasses = array();

    //loading very base classes. We can do this as we know they exists,
    //moreover even further method code could not execute without them
    $sBaseClassLocation = null;
    $aBaseClasses = array("oxutils", "oxsupercfg", "oxutilsobject");
    if (in_array($sClass, $aBaseClasses)) {
        $sFilename = getShopBasePath() ."core/" . $sClass . ".php" ;
        include $sFilename;
        return;
    }

    static $aClassPaths;

    if (isset($aClassPaths[$sClass])) {
        stopProfile("oxAutoload");
        include $aClassPaths[$sClass];
        return;
    }

   $sBasePath = getShopBasePath();


    // initializing paths
    if ( $aClassDirs == null ) {
        $aClassDirs = getClassDirs ( $sBasePath );
    }

    foreach ( $aClassDirs as $sDir ) {
        $sFilename = $sDir .  $sClass . '.php';
        if ( file_exists( $sFilename ) ) {
            if (!isset($aClassPaths[$sClass])) {
                $aClassPaths[$sClass] = $sFilename;
                //oxRegistry::getUtils()->toPhpFileCache("class_file_paths", $aClassPaths);
            }
            stopProfile("oxAutoload");
            include $sFilename;
            return;
        }
    }



    // Files registered by modules
    //$aModuleFiles = oxRegistry::getConfig()->getConfigParam( 'aModuleFiles' );
    $aModuleFiles = oxUtilsObject::getInstance()->getModuleVar( 'aModuleFiles' );
    if ( is_array( $aModuleFiles ) ) {
        $sBasePath   = getShopBasePath();
        $oModulelist = oxNew('oxmodulelist');
        $aActiveModuleInfo = $oModulelist->getActiveModuleInfo();
        if (is_array($aActiveModuleInfo)) {
            foreach ($aModuleFiles as $sModuleId => $aModules) {
                if (isset($aModules[$sClass]) && isset($aActiveModuleInfo[$sModuleId])) {
                    $sPath = $aModules[$sClass];
                    $sFilename = $sBasePath. 'modules/'.  $sPath;
                    if ( file_exists( $sFilename ) ) {
                        if (!isset($aClassPaths[$sClass])) {
                            $aClassPaths[$sClass] = $sFilename;
                            oxRegistry::getUtils()->toPhpFileCache("class_file_paths", $aClassPaths);
                        }
                        stopProfile("oxAutoload");
                        include $sFilename;
                        return;
                    }
                }
            }
        }
    }

    // in case module parent class (*_parent) is required
    $sClass = preg_replace( '/_parent$/i', '', $sClass );

    // special case
    if ( !in_array( $sClass, $aTriedClasses ) && is_array( $aModules = oxUtilsObject::getInstance()->getModuleVar( 'aModules' ) ) ) {

        $myUtilsObject = oxUtilsObject::getInstance();
        foreach ( $aModules as $sParentName => $sModuleName ) {
            // looking for module parent class
            if (  preg_match('/\b'.$sClass.'($|\&)/i', $sModuleName )  ) {
                $myUtilsObject->getClassName( $sParentName );
                break;
            }
            $aTriedClasses[] = $sClass;
        }
    }

    stopProfile("oxAutoload");
}

/**
 * Return array with classes paths.
 *
 * @param string $sBasePath path to shop base ddirectory.
 *
 * @return array
 */
function getClassDirs($sBasePath)
{
    $aClassDirs = array( $sBasePath . 'core/',
                         $sBasePath . 'application/components/widgets/',
                         $sBasePath . 'application/components/',
                         $sBasePath . 'application/models/',
                         $sBasePath . 'application/controllers/',
                         $sBasePath . 'application/controllers/admin/',
                         $sBasePath . 'application/controllers/admin/reports/',
                         $sBasePath . 'views/',
                         $sBasePath . 'core/exception/',
                         $sBasePath . 'core/interface/',
                         $sBasePath . 'core/cache/',
                         $sBasePath . 'core/cache/connectors/',
                         $sBasePath . 'core/wysiwigpro/',
                         $sBasePath . 'admin/reports/',
                         $sBasePath . 'admin/',
                         $sBasePath . 'modules/',
                         $sBasePath
                        );
    return $aClassDirs;
}


if ( !function_exists( 'getShopBasePath' ) ) {
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
    if (defined('OX_IS_ADMIN')) {
        return OX_IS_ADMIN;
    }

    return false;
}

/**
 * Sets default PHP parameters.
 *
 * @return null;
 */
function setPhpIniParams()
{
    //setting required PHP configuration parameters
    ini_set('session.name', 'sid');
    ini_set('session.use_cookies', 0);
    ini_set('session.use_trans_sid', 0);
    ini_set('url_rewriter.tags', '');
    ini_set('magic_quotes_runtime', 0);
}

/**
 * Strips magic quotes
 *
 * @return null
 */
function stripGpcMagicQuotes()
{
    if (!get_magic_quotes_gpc()) {
        return;
    }
    $_REQUEST = _stripMagicQuotes($_REQUEST);
    $_POST = _stripMagicQuotes($_POST);
    $_GET = _stripMagicQuotes($_GET);
    $_COOKIE = _stripMagicQuotes($_COOKIE);
}

/**
 * Recursively removes slashes from arrays
 *
 * @param mixed $mInput the input from which the slashes should be removed
 *
 * @return mixed
 */
function _stripMagicQuotes($mInput)
{
    return is_array($mInput) ? array_map( '_stripMagicQuotes', $mInput ) : stripslashes( $mInput );
}

if ( !function_exists( 'error_404_handler' ) ) {
    /**
     * error_404_handler handler for 404 (page not found) error
     *
     * @param string $sUrl url wich was given, can be not specified in some cases
     *
     * @return void
     */
    function error_404_handler($sUrl = '')
    {
        oxRegistry::getUtils()->handlePageNotFoundError($sUrl);
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
 *
 * @return null
 */
function warningHandler($iErrorNr, $sErrorText)
{
    echo "<div class='error_box'>".oxRegistry::getLang()->translateString('userError')."<code>[$iErrorNr] $sErrorText</code></div>";
}

/**
 * Dumps $mVar information to vardump.txt file. Used in debugging.
 *
 * @param mixed $mVar     variable
 * @param bool  $blToFile marker to write log info to file (must be true to log)
 *
 * @return null
 */
function dumpVar( $mVar, $blToFile = false )
{
    $myConfig = oxRegistry::getConfig();
    if ( $blToFile ) {
        $out = var_export( $mVar, true );
        $f = fopen( $myConfig->getConfigParam( 'sCompileDir' )."/vardump.txt", "a" );
        fwrite( $f, $out );
        fclose( $f );
    } else {
        echo '<pre>';
        var_export( $mVar );
        echo '</pre>';
    }
}

if ( !function_exists( 'isSearchEngineUrl' ) ) {

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
 *
 * @return null
 */
function debug( $mVar )
{
    $f = fopen( 'out.txt', 'a' );
    $sString = var_export( $mVar, true );
    fputs( $f, $sString."\n---------------------------------------------\n" );
    fclose( $f );
}

/**
 * Sorting for crossselling
 *
 * @param object $a first compare item
 * @param object $b second compre item
 *
 * @return integer
 */
function cmpart( $a, $b )
{
    // sorting for crossselling
    if ( $a->cnt == $b->cnt )
        return 0;
    return ( $a->cnt < $b->cnt ) ? -1 : 1;
}

if ( !function_exists( 'startProfile' ) ) {
    /**
     * Start profiling
     *
     * @param string $sProfileName name of profile
     *
     * @return null
     */
    function startProfile( $sProfileName )
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

if ( !function_exists( 'stopProfile' ) ) {
    /**
     * Stop profiling
     *
     * @param string $sProfileName name of profile
     *
     * @return null
     */
    function stopProfile( $sProfileName )
    {
        global $aProfileTimes;
        global $aStartTimes;
        if (!isset($aProfileTimes[$sProfileName])) {
            $aProfileTimes[$sProfileName] = 0;
        }
        $aProfileTimes[$sProfileName] += microtime( true ) - $aStartTimes[$sProfileName];
    }
}

/**
 * Creates and returns new object. If creation is not available, dies and outputs
 * error message.
 *
 * @param string $sClassName Name of class
 *
 * @throws oxSystemComponentException in case that class does not exists
 *
 * @return object
 */
function oxNew( $sClassName )
{
    startProfile( 'oxNew' );
    $aArgs = func_get_args();
    $oRes = call_user_func_array( array( oxUtilsObject::getInstance(), "oxNew" ), $aArgs );
    stopProfile( 'oxNew' );
    return $oRes;
}

/**
 * Creates, loads returns oxarticle object
 *
 * @param string $sArtId product id
 *
 * @deprecated since v4.7.5-5.0.5 (2013-03-29); use oxNew
 *
 * @return oxarticle
 */
function oxNewArticle( $sArtId )
{
    return oxUtilsObject::getInstance()->oxNewArticle( $sArtId );
}

/**
 * Returns current DB handler
 *
 * @param bool $blAssoc data fetch mode
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
 * @return oxStr
 */
function getStr()
{
    return oxStr::getStr();
}

/**
 * Sets template name to passed reference, returns true.
 *
 * @param string $sTplName    name of template
 * @param string &$sTplSource Template source
 * @param object $oSmarty     not used here
 *
 * @return bool
 */
function ox_get_template( $sTplName, &$sTplSource, $oSmarty )
{
    $sTplSource = $oSmarty->oxidcache->value;
    if ( oxRegistry::getConfig()->isDemoShop() ) {
        $oSmarty->security = true;
    }

    return true;
}

/**
 * Sets timestamt to passed timestamp object, returns true.
 *
 * @param string $sTplName       name of template
 * @param string &$iTplTimestamp template timestamp referense
 * @param object $oSmarty        not used here
 *
 * @return bool
 */
function ox_get_timestamp( $sTplName, &$iTplTimestamp, $oSmarty )
{
    if ( isset( $oSmarty->oxidtimecache->value ) ) {
        // use stored timestamp
        $iTplTimestamp = $oSmarty->oxidtimecache->value;
    } else {
        // always compile
        $iTplTimestamp = time();
    }

    return true;
}

/**
 * Assumes all templates are secure, returns true.
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 *
 * @return bool
 */
function ox_get_secure( $sTplName, $oSmarty )
{
    // assume all templates are secure
    return true;
}

/**
 * Does nothing.
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 *
 * @return null
 */
function ox_get_trusted( $sTplName, $oSmarty )
{
    // not used for templates
}


if ( !function_exists( 'getLangTableIdx' ) ) {

    /**
     * Returns language table index
     *
     * @param int $iLangId language id
     *
     * @return string
     */
    function getLangTableIdx( $iLangId )
    {
        $iLangPerTable = oxRegistry::getConfig()->getConfigParam( "iLangPerTable" );
        //#0002718 min language count per table 2
        $iLangPerTable = ( $iLangPerTable > 1 ) ? $iLangPerTable : 8;

        $iTableIdx = (int) ( $iLangId / $iLangPerTable );
        return $iTableIdx;
    }
}

if ( !function_exists( 'getLangTableName' ) ) {

    /**
     * Returns language table name
     *
     * @param string $sTable  table name
     * @param int    $iLangId language id
     *
     * @return string
     */
    function getLangTableName( $sTable, $iLangId )
    {
        $iTableIdx = getLangTableIdx( $iLangId );
        if ( $iTableIdx && in_array($sTable, oxRegistry::getLang()->getMultiLangTables())) {
            $sLangTableSuffix = oxRegistry::getConfig()->getConfigParam( "sLangTableSuffix" );
            $sLangTableSuffix = $sLangTableSuffix ? $sLangTableSuffix : "_set";

            $sTable .= $sLangTableSuffix . $iTableIdx;
        }

        return $sTable;
    }
}

if ( !function_exists( 'getViewName' ) ) {

    /**
     * Return the view name of the given table if a view exists, otherwise the table name itself
     *
     * @param string $sTable  table name
     * @param int    $iLangId language id [optional]
     * @param string $sShopId shop id, otherwise config->myshopid is used [optional]
     *
     * @return string
     */
    function getViewName( $sTable, $iLangId = null, $sShopId = null )
    {
        $myConfig = oxRegistry::getConfig();

        //This config option should only be used in emergency case.
        //Originally it was planned for the case when admin area is not reached due to the broken views.
        if ( !$myConfig->getConfigParam( 'blSkipViewUsage' ) ) {
            $sViewSfx = '';


            $blIsMultiLang = in_array( $sTable, oxRegistry::getLang()->getMultiLangTables() );
            if ( $iLangId != -1 && $blIsMultiLang ) {
                $oLang = oxRegistry::getLang();
                $iLangId = $iLangId !== null ? $iLangId : oxRegistry::getLang()->getBaseLanguage();
                $sAbbr = $oLang->getLanguageAbbr( $iLangId );
                $sViewSfx .= "_{$sAbbr}";
            }

            if ( $sViewSfx || (($iLangId == -1 || $sShopId == -1 ) && $blIsMultiLang)) {
                return "oxv_{$sTable}{$sViewSfx}";
            }

        }

        return $sTable;
    }
}

if ( !function_exists( 'getRequestUrl' ) ) {
    /**
     * Returns request url, which was executed to render current page view
     *
     * @param string $sParams     Parameters to object
     * @param bool   $blReturnUrl If return url
     *
     * @return string
     */
    function getRequestUrl( $sParams = '', $blReturnUrl = false )
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST" ) {

            if ( isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] ) {
                $sRequest = $_SERVER['REQUEST_URI'];
            } else {
                // try something else
                $sRequest = $_SERVER['SCRIPT_URI'];
            }

            // trying to resolve controller file name
            if ( $sRequest && ( $iPos = stripos( $sRequest, '?' ) ) !== false ) {

                $oStr = getStr();
                // formatting request url
                $sRequest = 'index.php' . $oStr->substr( $sRequest, $iPos );

                // removing possible session id
                $sRequest = $oStr->preg_replace( '/(&|\?)(force_)?(admin_)?sid=[^&]*&?/', '$1', $sRequest );
                $sRequest = $oStr->preg_replace( '/(&|\?)stoken=[^&]*&?/', '$1', $sRequest );
                $sRequest = $oStr->preg_replace( '/&$/', '', $sRequest );
                return str_replace( '&', '&amp;', $sRequest );
            }
        }
    }
}

//registering oxAutoload() as autoload handler
spl_autoload_register("oxAutoload");
