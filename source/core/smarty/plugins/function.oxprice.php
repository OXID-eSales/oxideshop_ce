<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   smarty_plugins
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: Output price string
 * add [{ oxprice price="..." currency="..." }] where you want to display content
 * price - decimal number: 13; 12.45; 13.01;
 * currency - currency abbreviation: EUR, USD, LTL etc.
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
*/
function smarty_function_oxprice( $params, &$smarty )
{
    $iDecimals = 2;
    $sDecimalsSeparator = ',';
    $sThousandSeparator = '.';
    $sCurrencySign = '';
    $sSide = '';

    $mPrice  = isset( $params['price'] ) ? $params['price'] : '';
    $sPrice = ( $mPrice instanceof oxPrice ) ? $mPrice->getPrice() : $mPrice;

    $oCurrency = isset( $params['currency'] ) ? $params['currency'] : null;

    if ( !is_null( $oCurrency ) ) {
        $sDecimalsSeparator = ( $oCurrency->dec ) ? $oCurrency->dec : $sDecimalsSeparator;
        $sThousandSeparator = ( $oCurrency->thousand ) ? $oCurrency->thousand : $sThousandSeparator;
        $sCurrencySign = ( $oCurrency->sign ) ? $oCurrency->sign : $sCurrencySign;
        $sSide = ( $oCurrency->side ) ? $oCurrency->side : $sSide;
        $iDecimals = ( $oCurrency->decimal ) ? (int) $oCurrency->decimal : $iDecimals;
    }

    $sPrice = number_format( $sPrice, $iDecimals, $sDecimalsSeparator, $sThousandSeparator );

    if ($sCurrencySign) {
        $sOutput = ( isset($sSide) && $sSide == 'Front' ) ? $sCurrencySign . ' ' . $sPrice : $sPrice . ' ' . $sCurrencySign;
    } else {
        $sOutput = $sPrice;
    }

    return $sOutput;
}
