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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: Output multilang string
 * add [{oxmultilang ident="..." args=...}] where you want to display content
 * ident - language constant
 * args - array of argument that can be parsed to language constant threw %s
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
*/
function smarty_function_oxmultilang( $params, &$smarty )
{
    startProfile("smarty_function_oxmultilang");

    $oLang = oxRegistry::getLang();
    $oConfig = oxRegistry::getConfig();
    $oShop = $oConfig->getActiveShop();
    $blAdmin = $oLang->isAdmin();

    $sIdent  = isset( $params['ident'] ) ? $params['ident'] : 'IDENT MISSING';
    $aArgs = isset( $params['args'] ) ? $params['args'] : false;
    $sSuffix = isset( $params['suffix'] ) ? $params['suffix'] : 'NO_SUFFIX';
    $blShowError = isset( $params['noerror']) ? !$params['noerror'] : true ;

    $iLang = $oLang->getTplLanguage();

    if( !$blAdmin && $oShop->isProductiveMode() ) {
        $blShowError = false;
    }

    try {
        $sTranslation = $oLang->translateString( $sIdent, $iLang, $blAdmin );
        $blTranslationNotFound = !$oLang->isTranslated();
        if ( 'NO_SUFFIX' != $sSuffix ) {
            $sSuffixTranslation = $oLang->translateString( $sSuffix, $iLang, $blAdmin );
        }
    } catch (\OxidEsales\EshopCommunity\Core\Exception\LanguageException $oEx ) {
        // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
    }

    if( $blTranslationNotFound && isset( $params['alternative'] ) ) {
        $sTranslation = $params['alternative'];
        $blTranslationNotFound = false;
    }

    if ( !$blTranslationNotFound ) {
        if ( $aArgs !== false ) {
            if ( is_array( $aArgs ) ) {
                $sTranslation = vsprintf( $sTranslation, $aArgs );
            } else {
                $sTranslation = sprintf( $sTranslation, $aArgs );
            }
        }

        if ( 'NO_SUFFIX' != $sSuffix ) {
            $sTranslation .= $sSuffixTranslation;
        }

    } elseif( $blShowError ) {
        $sTranslation = 'ERROR: Translation for '.$sIdent.' not found!';
    }

    stopProfile("smarty_function_oxmultilang");

    return $sTranslation;
}
