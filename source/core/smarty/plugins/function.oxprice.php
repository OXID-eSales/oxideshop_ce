<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
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
    $sOutput = '';
    $iDecimals = 2;
    $sDecimalsSeparator = ',';
    $sThousandSeparator = '.';
    $sCurrencySign = '';
    $sSide = '';
    $mPrice = $params['price'];

    if ( !is_null( $mPrice ) ) {

        $sPrice = ( $mPrice instanceof oxPrice ) ? $mPrice->getPrice() : $mPrice;
        $oCurrency = isset( $params['currency'] ) ? $params['currency'] : null;

        if ( !is_null( $oCurrency ) ) {
            $sDecimalsSeparator = isset( $oCurrency->dec ) ? $oCurrency->dec : $sDecimalsSeparator;
            $sThousandSeparator = isset( $oCurrency->thousand ) ? $oCurrency->thousand : $sThousandSeparator;
            $sCurrencySign = isset( $oCurrency->sign ) ? $oCurrency->sign : $sCurrencySign;
            $sSide = isset( $oCurrency->side ) ? $oCurrency->side : $sSide;
            $iDecimals = isset( $oCurrency->decimal ) ? (int) $oCurrency->decimal : $iDecimals;
        }

        if ( is_numeric( $sPrice ) ) {
            if ( (float) $sPrice > 0 || $sCurrencySign  ) {
                $sPrice = number_format( $sPrice, $iDecimals, $sDecimalsSeparator, $sThousandSeparator );
                $sOutput = ( isset($sSide) && $sSide == 'Front' ) ? $sCurrencySign . ' ' . $sPrice : $sPrice . ' ' . $sCurrencySign;
            }

            $sOutput = trim($sOutput);
        }
    }

    return $sOutput;
}
