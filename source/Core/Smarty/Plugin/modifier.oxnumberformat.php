<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty modifier
 * -------------------------------------------------------------
 * Name:     smarty_modifier_oxnumberformat<br>
 * Purpose:  Formats number for chosen locale
 * Example:  $object = "EUR@ 1.00@ ,@ .@ EUR@ 2"{$object|oxnumberformat:2000.123}
 * -------------------------------------------------------------
 *
 * @param string $sFormat Number formatting rules (use default currency formatting rules defined in Admin)
 * @param string $sValue  Number to format
 *
 * @return string
 */
function smarty_modifier_oxnumberformat($sFormat = "EUR@ 1.00@ ,@ .@ EUR@ 2", $sValue = 0)
{
    // logic copied from \OxidEsales\Eshop\Core\Config::getCurrencyArray()
    $sCur = explode("@", $sFormat);
    $oCur           = new stdClass();
    $oCur->id       = 0;
    $oCur->name     = @trim($sCur[0]);
    $oCur->rate     = @trim($sCur[1]);
    $oCur->dec      = @trim($sCur[2]);
    $oCur->thousand = @trim($sCur[3]);
    $oCur->sign     = @trim($sCur[4]);
    $oCur->decimal  = @trim($sCur[5]);

    // change for US version
    if (isset($sCur[6])) {
        $oCur->side = @trim($sCur[6]);
    }

    return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($sValue, $oCur);
}
