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
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_tracker.php
 * Type: string, html
 * Name: oxid_tracker
 * Purpose: Output etracker code or Econda Code
 * add [{ insert name="oxid_tracker" title="..." }] after Body Tag in Templates
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_insert_oxid_tracker( $params, &$smarty )
{
    $myConfig = oxRegistry::getConfig();
    // econda is on ?
    if ( $myConfig->getConfigParam( 'blEcondaActive' ) ) {
        include_once $myConfig->getConfigParam( 'sCoreDir' ).'smarty/plugins/oxemosadapter.php';

        $sOutput = oxRegistry::get("oxEmosAdapter")->getCode( $params, $smarty );

        // returning JS code to output
        if ( strlen( trim( $sOutput ) ) ) {
            return "<div style=\"display:none;\">{$sOutput}</div>";
        }
    }
}
