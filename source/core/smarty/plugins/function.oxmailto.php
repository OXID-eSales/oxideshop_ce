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
 * Smarty {mailto} function plugin extension, fixes character encoding problem
 *
 * @param array  $aParams  parameters
 * @param Smarty &$oSmarty smarty object
 *
 * @return string
 */
function smarty_function_oxmailto( $aParams, &$oSmarty )
{
    if ( isset( $aParams['encode'] ) && $aParams['encode'] == 'javascript' ) {

        $sAddress = isset( $aParams['address'] ) ? $aParams['address'] : '';
        $sText = $sAddress;

        $aMailParms = array();
        foreach ( $aParams as $sVarName => $sValue ) {
            switch ( $sVarName ) {
                case 'cc':
                case 'bcc':
                case 'followupto':
                    if ( $sValue ) {
                        $aMailParms[] = $sVarName . '=' . str_replace( array( '%40', '%2C' ), array( '@', ',' ), rawurlencode( $sValue ) );
                    }
                    break;
                case 'subject':
                case 'newsgroups':
                    $aMailParms[] = $sVarName . '=' . rawurlencode( $sValue );
                    break;
                case 'extra':
                case 'text':
                    $sName  = "s".ucfirst( $sVarName );
                    $$sName = $sValue;
                default:
            }
        }

        for ( $iCtr = 0; $iCtr < count( $aMailParms ); $iCtr++ ) {
            $sAddress .= ( $iCtr == 0 ) ? '?' : '&';
            $sAddress .= $aMailParms[$iCtr];
        }

        $sString = 'document.write(\'<a href="mailto:'.$sAddress.'" '.$sExtra.'>'.$sText.'</a>\');';
        $sEncodedString = "%".wordwrap( current( unpack( "H*", $sString ) ), 2, "%", true );
        return '<script type="text/javascript">eval(decodeURIComponent(\''.$sEncodedString.'\'))</script>';
    } else {
        include_once $oSmarty->_get_plugin_filepath( 'function', 'mailto' );
        return smarty_function_mailto($aParams, $oSmarty );
    }
}