<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Core\Registry;

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: Output price string
 * add [{oxprice price="..." currency="..."}] where you want to display content
 * price - decimal number: 13; 12.45; 13.01;
 * currency - currency abbreviation: EUR, USD, LTL etc.
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxprice($params, &$smarty)
{
    $sOutput = '';
    $iDecimals = 2;
    $sDecimalsSeparator = ',';
    $sThousandSeparator = '.';
    $sCurrencySign = '';
    $sSide = '';
    $mPrice = $params['price'];

    if (!is_null($mPrice)) {
        $oConfig = Registry::getConfig();

        $sPrice = ($mPrice instanceof \OxidEsales\Eshop\Core\Price) ? $mPrice->getPrice() : floatval($mPrice);
        $oCurrency = isset($params['currency']) ? $params['currency'] : $oConfig->getActShopCurrencyObject();

        if (!is_null($oCurrency)) {
            $sDecimalsSeparator = isset($oCurrency->dec) ? $oCurrency->dec : $sDecimalsSeparator;
            $sThousandSeparator = isset($oCurrency->thousand) ? $oCurrency->thousand : $sThousandSeparator;
            $sCurrencySign = isset($oCurrency->sign) ? $oCurrency->sign : $sCurrencySign;
            $sSide = isset($oCurrency->side) ? $oCurrency->side : $sSide;
            $iDecimals = isset($oCurrency->decimal) ? (int) $oCurrency->decimal : $iDecimals;
        }

        if (is_numeric($sPrice)) {
            if ((float) $sPrice > 0 || $sCurrencySign) {
                $sPrice = number_format($sPrice, $iDecimals, $sDecimalsSeparator, $sThousandSeparator);
                $sOutput = (isset($sSide) && $sSide == 'Front') ? $sCurrencySign . $sPrice : $sPrice . ' ' . $sCurrencySign;
            }

            $sOutput = trim($sOutput);
        }
    }

    return $sOutput;
}
