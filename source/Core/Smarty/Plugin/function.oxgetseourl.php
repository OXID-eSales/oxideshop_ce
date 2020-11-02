<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: output SEO style url
 * add [{oxgetseourl ident="..."}] where you want to display content
 * -------------------------------------------------------------.
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxgetseourl($params, &$smarty)
{
    $sOxid = $params['oxid'] ?? null;
    $sType = $params['type'] ?? null;
    $sUrl = $sIdent = $params['ident'] ?? null;

    // requesting specified object SEO url
    if ($sType) {
        $oObject = oxNew($sType);

        // special case for content type object when ident is provided
        if ('oxcontent' === $sType && $sIdent && $oObject->loadByIdent($sIdent)) {
            $sUrl = $oObject->getLink();
        } elseif ($sOxid) {
            //minimising aricle object loading
            if ('oxarticle' === strtolower($sType)) {
                $oObject->disablePriceLoad();
                $oObject->setNoVariantLoading(true);
            }

            if ($oObject->load($sOxid)) {
                $sUrl = $oObject->getLink();
            }
        }
    } elseif ($sUrl && \OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive()) {
        // if SEO is on ..
        $oEncoder = \OxidEsales\Eshop\Core\Registry::getSeoEncoder();
        if (($sStaticUrl = $oEncoder->getStaticUrl($sUrl))) {
            $sUrl = $sStaticUrl;
        } else {
            // in case language parameter is not added to url
            $sUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($sUrl);
        }
    }

    $sDynParams = $params['params'] ?? false;
    if ($sDynParams) {
        include_once $smarty->_get_plugin_filepath('modifier', 'oxaddparams');
        $sUrl = smarty_modifier_oxaddparams($sUrl, $sDynParams);
    }

    return $sUrl;
}
