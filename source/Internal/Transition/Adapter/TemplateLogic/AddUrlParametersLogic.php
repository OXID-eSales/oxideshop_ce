<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Str;

class AddUrlParametersLogic
{
    /**
     * Add additional parameters to SEO url.
     *
     * @param string $sUrl       Url
     * @param string $sDynParams Dynamic URL parameters
     */
    public function addUrlParameters(string $sUrl, string $sDynParams): string
    {
        // removing empty parameters
        $sDynParams = $sDynParams ? Str::getStr()->preg_replace(['/^\?/', '/^\&(amp;)?$/'], '', $sDynParams) : false;
        if ($sDynParams) {
            $sUrl .= ((false !== strpos($sUrl, '?')) ? '&amp;' : '?') . $sDynParams;
        }

        return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processSeoUrl($sUrl);
    }
}
