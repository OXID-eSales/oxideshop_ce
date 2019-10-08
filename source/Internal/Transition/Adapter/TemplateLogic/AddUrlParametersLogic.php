<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class AddUrlParametersLogic
{

    /**
     * Add additional parameters to SEO url
     *
     * @param string $sUrl       Url
     * @param string $sDynParams Dynamic URL parameters
     *
     * @return string
     */
    public function addUrlParameters(string $sUrl, string $sDynParams): string
    {
        $oStr = getStr();
        // removing empty parameters
        $sDynParams = $sDynParams ? $oStr->preg_replace(['/^\?/', '/^\&(amp;)?$/'], '', $sDynParams) : false;
        if ($sDynParams) {
            $sUrl .= ((strpos($sUrl, '?') !== false) ? "&amp;" : "?") . $sDynParams;
        }

        return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processSeoUrl($sUrl);
    }
}
