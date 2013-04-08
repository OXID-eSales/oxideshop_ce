<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

print("start"."\r\n");

// Bootstrap starts

//Know exactly where in the code the event occurred.
//Zend platform only.
if (function_exists('monitor_set_aggregation_hint') && isset($_REQUEST['cl'])) {
    $sAgregationHint = htmlentities($_REQUEST['cl'], ENT_QUOTES, 'UTF-8') . '/';
    if (isset($_REQUEST['fnc']))
        $sAgregationHint .= htmlentities($_REQUEST['fnc'], ENT_QUOTES, 'UTF-8');
    monitor_set_aggregation_hint($sAgregationHint);
}

// Local this file exists. Cant add to deploy tags as deploy do not parse this file.
if (file_exists('_version_define.php')) {
    include_once('_version_define.php');
}

//setting basic configuration parameters
ini_set('session.name', 'sid' );
ini_set('session.use_cookies', 0 );
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');
ini_set('magic_quotes_runtime', 0);

/**
 * Returns shop base path.
 *
 * @return string
 */
function getShopBasePath()
{
    return dirname(__FILE__).'/';
}


if ( !function_exists( 'isAdmin' )) {
    /**
     * Returns false.
     *
     * @return bool
     */
    function isAdmin()
    {
        return false;
    }
}

// custom functions file
require getShopBasePath() . 'modules/functions.php';

// Generic utility method file
require_once getShopBasePath() . 'core/oxfunctions.php';


// set the exception handler already here to catch everything, also uncaught exceptions from the config or utils

// initializes singleton config class
$myConfig = oxConfig::getInstance();

//strips magics quote if any
oxUtils::getInstance()->stripGpcMagicQuotes();

// reset it so it is done with oxnew
$iDebug = $myConfig->getConfigParam('iDebug');
set_exception_handler(array(oxNew('oxexceptionhandler', $iDebug), 'handleUncaughtException'));
// Admin handling
if ( isAdmin() ) {
    $myConfig->setConfigParam( 'blAdmin', true );
    $myConfig->setConfigParam( 'blTemplateCaching', false );
    if ($sAdminDir)
        $myConfig->setConfigParam( 'sAdminDir', $sAdminDir );
    else
        $myConfig->setConfigParam( 'sAdminDir', "admin" );
}

// Bootstrap ends

$oxConfig = oxConfig::getInstance();

// Get active shop as it might be different if subshops are active.
// It is impossible to change data in different shops while they are not active.
$sActiveShopId = $oxConfig->getRequestParameter('shp');
if ($sActiveShopId ) {
    $oxConfig->setShopId($sActiveShopId);
}

$sClassName    = $oxConfig->getParameter("cl");
$sFunctionName = $oxConfig->getParameter('fnc');
$sOxid         = $oxConfig->getParameter("oxid");
$sClassParams  = $oxConfig->getParameter("classparams");

// Class and function name is must have for every action: create object, save object, delete object.
if (!$sClassName || !$sFunctionName) {
    echo "No \$sClassName or no \$sFunctionName";
    return;
}

switch (strtolower($sClassName)) {
    case "oxconfig":
        callFunctionOnConfig($sClassName, $sFunctionName, $sClassParams);
        break;

    default:
        callFunctionOnObject($sClassName, $sFunctionName, $sOxid, $sClassParams);
        break;
}

/**
 * Calls oxconfig method with passed parameters.
 * For now it is prepared for 'saveShopConfVar' method only.
 *
 * @param string $sClassName Name of class
 * @param string $sFunctionName Name of method
 * @param string $sClassParams
 *
 * @return null
 */
function callFunctionOnConfig($sClassName, $sFunctionName, $sClassParams = null)
{
    $oConfig = oxConfig::getInstance();
    if ($sClassParams) {
        foreach ($sClassParams as $sParamKey => $aParams) {
            if ($aParams) {
                $sType = null;
                $sValue = null;
                $sModule = null;
                foreach ($aParams as $sSubParamKey => $sSubParamValue) {
                    switch ($sSubParamKey) {
                        case "type":
                            $sType = $sSubParamValue;
                            break;
                        case "value":
                            $sValue = $sSubParamValue;
                            break;
                        case "module":
                            $sModule = $sSubParamValue;
                            break;
                    }
                }
                if (isset($sType) && isset($sValue)) {
                    if ($sType == "arr") {
                        $sValue = unserialize(htmlspecialchars_decode($sValue));
                    }
                    call_user_func(array($oConfig, $sFunctionName), $sType, $sParamKey, $sValue, null, $sModule);
                }
            }
        }
    }
}

/**
 * Calls object method with passed parameters.
 *
 * @param string $sClassName Name of class
 * @param string $sFunctionName Name of method
 * @param string $sOxid Oxid value
 * @param string $sClassParams
 *
 * @return null
 */
function callFunctionOnObject($sClassName, $sFunctionName, $sOxid = null, $sClassParams = null)
{
    $oObject = oxNew($sClassName);
    if (!empty($sOxid)) {
        $oObject->load($sOxid);
    }

    $sTableName = getTableNameFromClassName($sClassName);
    if ($sClassParams) {
        foreach ($sClassParams as $sParamKey => $sParamValue) {
            $sDBFieldName = $sTableName .'__'. $sParamKey;
            $oObject->$sDBFieldName = new oxField($sParamValue);
        }
    }
    call_user_func(array($oObject, $sFunctionName));
}

/**
 * Return table name from class name.
 * @example $sClassName = oxArticle; return oxarticles;
 * @example $sClassName = oxRole; return oxroles;
 *
 * @param string $sClassName Name of class.
 *
 * @return string
 */

function getTableNameFromClassName($sClassName)
{
    $aClassNameWithoutS   = array("oxarticle", "oxrole");
    $aClassNameWithoutIes = array("oxcategory");

    $sTableName = strtolower($sClassName);
    if (in_array(strtolower($sClassName), $aClassNameWithoutS)) {
        $sTableName = strtolower($sClassName) ."s";
    } elseif (in_array(strtolower($sClassName), $aClassNameWithoutIes)) {
        $sTableName = substr(strtolower($sClassName), 0, -1) ."ies";
    }
    return $sTableName;
}

print("end"."\r\n");