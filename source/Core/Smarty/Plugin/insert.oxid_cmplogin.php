<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_cmpbasket.php
 * Type: string, html
 * Name: oxid_cmplogin
 * Purpose: Inserts OXID eShop Login without caching
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_insert_oxid_cmplogin($params, &$smarty)
{
    $smarty->caching = false;

    $sOutput = $smarty->fetch($params['tpl']);

    $smarty->caching = false;

    return $sOutput;
}
