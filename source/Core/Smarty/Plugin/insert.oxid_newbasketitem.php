<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_newbasketitem.php
 * Type: string, html
 * Name: newbasketitem
 * Purpose: Used for tracking in econda, etracker etc.
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_insert_oxid_newbasketitem($params, &$smarty)
{
    $myConfig  = \OxidEsales\Eshop\Core\Registry::getConfig();

    $aTypes = ['0' => 'none','1' => 'message', '2' =>'popup', '3' =>'basket'];
    $iType  = $myConfig->getConfigParam('iNewBasketItemMessage');

    // If corect type of message is expected
    if ($iType && $params['type'] && ($params['type'] != $aTypes[$iType])) {
        return '';
    }

    //name of template file where is stored message text
    $sTemplate = $params['tpl']?$params['tpl']:'inc_newbasketitem.snippet.tpl';

    //allways render for ajaxstyle popup
    $blRender = $params['ajax'] && ($iType == 2);

    //fetching article data
    $oNewItem = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem');

    if ($oNewItem) {
        // loading article object here because on some system passing article by session couses problems
        $oNewItem->oArticle = oxNew('oxarticle');
        $oNewItem->oArticle->Load($oNewItem->sId);

        // passing variable to template with unique name
        $smarty->assign('_newitem', $oNewItem);

        // deleting article object data
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable('_newitem');

        $blRender = true;
    }

    // returning generated message content
    if ($blRender) {
        return $smarty->fetch($sTemplate);
    }
}
