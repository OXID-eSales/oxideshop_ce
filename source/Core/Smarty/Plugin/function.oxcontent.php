<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_content.php
 * Type: string, html
 * Name: oxid_content
 * Purpose: Output content snippet
 * add [{insert name="oxid_content" ident="..."}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxcontent($params, &$smarty)
{
    $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
    $sText = $myConfig->getActiveShop()->oxshops__oxproductive->value ? null : "<b>content not found ! check ident(".$params['ident'].") !</b>";
    $smarty->oxidcache = new \OxidEsales\Eshop\Core\Field($sText, \OxidEsales\Eshop\Core\Field::T_RAW);

    $sIdent = isset($params['ident'])?$params['ident']:null;
    $sOxid  = isset($params['oxid'])?$params['oxid']:null;

    if ($sIdent || $sOxid) {
        $oContent = oxNew("oxcontent");
        if ($sOxid) {
            $blLoaded = $oContent->load($sOxid);
        } else {
            $blLoaded = $oContent->loadbyIdent($sIdent);
        }

        if ($blLoaded && $oContent->oxcontents__oxactive->value) {
            // set value
            $sField = "oxcontent";
            if (isset($params['field'])) {
                $sField = $params['field'];
            }
            // set value
            $sProp = 'oxcontents__'.$sField;
            $smarty->oxidcache = clone $oContent->$sProp;
            $smarty->compile_check  = true;
            $sCacheId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage() . $myConfig->getShopId();
            $sText = $smarty->fetch("ox:".(string)$sIdent.(string)$sOxid.$sField.$sCacheId);
            $smarty->compile_check  = $myConfig->getConfigParam('blCheckTemplates');
        }
    }

    // if we write '[{oxcontent ident="oxemailfooterplain" assign="fs_text"}]' the content wont be outputted.
    // instead of this the content will be assigned to variable.
    if (isset($params['assign']) && $params['assign']) {
        $smarty->assign($params['assign'], $sText);
    } else {
        return $sText;
    }
}
