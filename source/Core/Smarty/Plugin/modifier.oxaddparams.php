<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: add additional parameters to SEO url
 * add |oxaddparams:"...." to link
 * -------------------------------------------------------------
 *
 * @param string $sUrl       Url
 * @param string $sDynParams Dynamic URL parameters
 *
 * @return string
 */
function smarty_modifier_oxaddparams($sUrl, $sDynParams)
{
    $oStr = getStr();
    // removing empty parameters
    $sDynParams = $sDynParams?$oStr->preg_replace([ '/^\?/', '/^\&(amp;)?$/' ], '', $sDynParams):false;
    if ($sDynParams) {
        $sUrl .= ((strpos($sUrl, '?') !== false) ? "&amp;":"?") . $sDynParams;
    }
    return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processSeoUrl($sUrl);
}
