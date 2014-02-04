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
 * Purpose: Modifies provided language constant with it's translation
 * usage: [{ $val|oxmultilangassign}]
 * -------------------------------------------------------------
 *
 * @param string $sIdent language constant ident
 * @param mixed  $args   for constants using %s notations
 *
 * @return string
 */
function smarty_modifier_oxmultilangassign( $sIdent, $args = null )
{
    if ( !isset( $sIdent ) ) {
        $sIdent = 'IDENT MISSING';
    }

    $oLang = oxRegistry::getLang();
    $iLang = $oLang->getTplLanguage();

    if ( !isset( $iLang ) ) {
        $iLang = $oLang->getBaseLanguage();
        if ( !isset( $iLang ) ) {
            $iLang = 0;
        }
    }

    try {
        $sTranslation = $oLang->translateString( $sIdent, $iLang, $oLang->isAdmin() );
    } catch ( oxLanguageException $oEx ) {
        // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
    }

    if ( $sTranslation == $sIdent ) {
        $sTranslation = '<b>ERROR : Translation for '.$sIdent.' not found!</b>';
    }
    if ( $args ) {
        if ( is_array( $args ) ) {
            $sTranslation = vsprintf( $sTranslation, $args );
        } else {
            $sTranslation = sprintf( $sTranslation, $args );
        }
    }
    return $sTranslation;
}
