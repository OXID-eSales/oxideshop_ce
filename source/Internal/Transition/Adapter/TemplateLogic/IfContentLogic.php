<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class IfContentLogic
{
    /**
     * @param string $sIdent
     * @param string $sOxid
     *
     * @return mixed
     */
    public function getContent(string $sIdent = null, string $sOxid = null)
    {
        static $aContentCache = [];

        if (($sIdent && isset($aContentCache[$sIdent])) ||
            ($sOxid && isset($aContentCache[$sOxid]))) {
            $oContent = $sOxid ? $aContentCache[$sOxid] : $aContentCache[$sIdent];
        } else {
            $oContent = oxNew("oxContent");
            $blLoaded = $sOxid ? $oContent->load($sOxid) : ($oContent->loadbyIdent($sIdent));
            if ($blLoaded && $oContent->isActive()) {
                $aContentCache[$oContent->getId()] = $aContentCache[$oContent->getLoadId()] = $oContent;
            } else {
                $oContent = false;
                if ($sOxid) {
                    $aContentCache[$sOxid] = $oContent;
                } else {
                    $aContentCache[$sIdent] = $oContent;
                }
            }
        }

        return $oContent;
    }
}
