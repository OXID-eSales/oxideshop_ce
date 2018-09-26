<?php

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;

/**
 * Class OxaddparamsLogic
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class OxaddparamsLogic
{
    /**
     * Add additional parameters to SEO url
     *
     * @param string $sUrl          Url
     * @param string $sDynParams    Dynamic URL parameters
     *
     * @return string
     */
    public function oxaddparams($sUrl, $sDynParams)
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