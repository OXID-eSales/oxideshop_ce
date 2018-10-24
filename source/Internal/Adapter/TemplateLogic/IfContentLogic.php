<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;

/**
 * Class IfContentLogic
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IfContentLogic
{

    /**
     * @param string $sIdent
     * @param string $sOxid
     *
     * @return array
     */
    public function getContent($sIdent, $sOxid)
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
