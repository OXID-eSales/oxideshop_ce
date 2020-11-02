<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class SeoUrlLogic
{
    /**
     * Output SEO style url.
     */
    public function seoUrl(array $params): string
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

        return $sUrl ?: '';
    }
}
