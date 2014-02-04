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
 * This method replaces existing Smarty function for truncating strings
 * (check Smarty documentation for details). When truncating strings
 * additionally we need to convert &#039;/&quot; entities to '/"
 * and after truncating convert them back.
 *
 * -------------------------------------------------------------
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 *  -------------------------------------------------------------
 *
 * @param string  $sString      String to truncate
 * @param integer $iLength      To length
 * @param string  $sSufix       Truncation mark
 * @param bool    $blBreakWords break words
 * @param bool    $middle       middle ?
 *
 * @return string
 */
function smarty_modifier_oxtruncate($sString, $iLength = 80, $sSufix = '...', $blBreakWords = false, $middle = false)
{
    if ($iLength == 0) {
        return '';
    } elseif ( $iLength > 0 && getStr()->strlen( $sString ) > $iLength ) {
        $iLength -= getStr()->strlen( $sSufix );

        $sString = str_replace( array('&#039;', '&quot;'), array( "'",'"' ), $sString );

        if (!$blBreakWords ) {
            $sString = getStr()->preg_replace( '/\s+?(\S+)?$/', '', getStr()->substr( $sString, 0, $iLength + 1 ) );
        }

        $sString = getStr()->substr( $sString, 0, $iLength ).$sSufix;

        return str_replace( array( "'",'"' ), array('&#039;', '&quot;'), $sString );
    }

    return $sString;
}

/* vim: set expandtab: */

?>
