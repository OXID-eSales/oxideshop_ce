<?php

print("start"."\r\n");

require_once dirname(__FILE__) . "/bootstrap.php";

error_reporting(E_ALL);
ini_set('display_errors', '1');

$oxConfig = oxRegistry::getConfig();

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
    $oConfig = oxRegistry::getConfig();
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
                    //flush cache if needed
                    $oCache = oxRegistry::get( 'oxReverseProxyBackend' );
                    if ( $oCache->isActive() ) {
                        $oCache->execute();
                    }
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
    //flush cache if needed
    $oCache = oxRegistry::get( 'oxReverseProxyBackend' );
    if ( $oCache->isActive() ) {
        $oCache->execute();
    }
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
    $aClassNameWithoutS   = array("oxarticle", "oxrole", "oxrating", "oxreview","oxrecommlist", "oxmanufacturer", "oxvoucherserie");
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