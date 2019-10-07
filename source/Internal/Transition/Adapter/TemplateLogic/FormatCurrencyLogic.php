<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use stdClass;

class FormatCurrencyLogic
{

    /**
     * @param string     $sFormat
     * @param string|int $sValue
     *
     * @return string
     */
    public function numberFormat($sFormat = "EUR@ 1.00@ ,@ .@ EUR@ 2", $sValue = 0)
    {
        // logic copied from \OxidEsales\Eshop\Core\Config::getCurrencyArray()
        $sCur = explode("@", $sFormat);
        $oCur = new stdClass();
        $oCur->id = 0;
        $oCur->name = @trim($sCur[0]);
        $oCur->rate = @trim($sCur[1]);
        $oCur->dec = @trim($sCur[2]);
        $oCur->thousand = @trim($sCur[3]);
        $oCur->sign = @trim($sCur[4]);
        $oCur->decimal = @trim($sCur[5]);

        // change for US version
        if (isset($sCur[6])) {
            $oCur->side = @trim($sCur[6]);
        }

        return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($sValue, $oCur);
    }
}
