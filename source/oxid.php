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
 * @package   main
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

/**
 * Search engine URL parser
 */

// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
if ( isset( $_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]) {
    $sParams = $_SERVER["REQUEST_URI"];
} else {
    // try something else
    $sParams = $_SERVER["SCRIPT_URI"];
}

/**
 * Get requested URL
 *
 * @param string $sParams     URL paremeters
 * @param bool   $blReturnUrl Return just request URL
 *
 * @return string
 */
function getRequestUrl( $sParams = '', $blReturnUrl = false )
{
    static $sProcUrl = null;

    if ( $blReturnUrl ) {
        return 'index.php?' . trim( $sProcUrl, '&amp;' );
    } else {
        $sProcUrl = '';
    }

    $sScriptFile = basename(__FILE__);
    $iPos = strpos( $sParams, $sScriptFile);
    $sParams = substr( $sParams, $iPos + strlen($sScriptFile));
    $sParams = str_replace("_&2f", "%2f", $sParams);  // #1123A

    //searchengine url safe replace
    $aReplaceArray = array( "/?" => "/",
                        "?" => "/",
                        "/&" => "/",
                        "&" => "/",
                        "=/" => "/-/",
                        "=" => "/");
    $sParams = strtr($sParams, $aReplaceArray);


    $aArg = explode( "/", $sParams);


    $sSizeofArg = count($aArg);
    for ($iCtr = 0; $iCtr < $sSizeofArg; $iCtr++) {
        $sParam = $aArg[$iCtr];
        if ( !$sParam || strstr( $sParam, $sScriptFile)) {
            continue;
        }

        // sets value "-" (or other) to empty, according to change
        // in index.php line 512 (described)
        $sEmptyVar = "-";
        if ( !isset($aArg[$iCtr+1]) || $aArg[$iCtr+1] == $sEmptyVar) {
            $aArg[$iCtr+1] = "";
        }

        if ( strpos(rawurldecode($aArg[$iCtr]), "[") !== false && preg_match( "/.*\[.*\]/", rawurldecode($aArg[$iCtr]))) {
            $sVar = rawurldecode($aArg[$iCtr]);
            $sName = preg_replace( "/\[.*\]/", "", $sVar);
            $sKey  = preg_replace( array( "/.*\[/", "/\]/"), "", $sVar);
            $aArray[$sKey] = $aArg[$iCtr+1];
            @$_GET[$sName] = $aArray;
            @$_REQUEST[$sName] = $aArray;

            $sProcUrl .= "{$sName}[{$sKey}]=".$aArg[$iCtr+1]."&amp;";
        } else {
            @$_GET[$aArg[$iCtr]] = rawurldecode($aArg[$iCtr+1]);
            @$_REQUEST[$aArg[$iCtr]] = rawurldecode($aArg[$iCtr+1]);

            // skipping session id
            if ( $aArg[$iCtr] != 'sid' && $aArg[$iCtr+1] ) {
                $sProcUrl .= $aArg[$iCtr]."=".$aArg[$iCtr+1]."&amp;";
            }
        }
        $iCtr++;
    }
}

if ( isset( $sParams ) && $sParams && $_SERVER["REQUEST_METHOD"] != "POST" ) {
    getRequestUrl( $sParams );
}

/**
 * Includes index.php file
 */
require "index.php";
