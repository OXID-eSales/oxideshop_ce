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
 * Smarty modifier
 * -------------------------------------------------------------
 * Name:     smarty_modifier_oxformdate<br>
 * Purpose:  Conterts date/timestamp/datetime type value to user defined format
 * Example:  {$object|oxformdate:"foo"}
 * -------------------------------------------------------------
 *
 * @param object $oConvObject   oxField object
 * @param string $sFieldType    additional type if field (this may help to force formatting)
 * @param bool   $blPassedValue bool if true, will simulate object as sometimes we need to apply formatting to some regulat values
 *
 * @return string
 */
function smarty_modifier_oxformdate( $oConvObject, $sFieldType = null, $blPassedValue = false)
{   // creating fake bject
    if ( $blPassedValue || is_string($oConvObject) ) {
        $sValue = $oConvObject;
        $oConvObject = new oxField();
        $oConvObject->fldmax_length = "0";
        $oConvObject->fldtype = $sFieldType;
        $oConvObject->setValue($sValue);
    }

    $myConfig = oxRegistry::getConfig();

    // if such format applies to this type of field - sets formatted value to passed object
    if ( !$myConfig->getConfigParam( 'blSkipFormatConversion' ) ) {
        if ( $oConvObject->fldtype == "datetime" || $sFieldType == "datetime")
            oxRegistry::get('oxUtilsDate')->convertDBDateTime( $oConvObject );
        elseif ( $oConvObject->fldtype == "timestamp" || $sFieldType == "timestamp")
            oxRegistry::get('oxUtilsDate')->convertDBTimestamp( $oConvObject );
        elseif ( $oConvObject->fldtype == "date" || $sFieldType == "date")
            oxRegistry::get('oxUtilsDate')->convertDBDate( $oConvObject );
    }

    return $oConvObject->value;
}

/* vim: set expandtab: */
