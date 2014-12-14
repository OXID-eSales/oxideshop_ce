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
 * File: insert.oxid_cssmanager.php
 * Type: string, html
 * Name: oxid_cmpbasket
 * Purpose: Includes css style file according to template file or sets default
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_insert_oxid_cssmanager($params, &$smarty)
{   $myConfig = oxRegistry::getConfig();

    $smarty->caching = false;

    // template file name
    $sTplName = $smarty->oxobject->getTemplateName();

    // css file extension
    $sCssExt  = "css";

    // sets name of alternative CSS file passed template parameters
    if ( isset($params["cssname"]) && $params["cssname"]) {
        $sAltCss = $params["cssname"];
    // possible CSS file for current template
    } else {
        $sAltCss = $sTplName . "." . $sCssExt;
    }

    // user defined alternative CSS files dir
    $sAltCssDir = "styles/";

    // URL to templates, there may be stored and css files
    if ( isset($params["cssurl"]) && $params["cssurl"]) {
        $sTplURL = $params["cssurl"];
    } else {
        $sTplURL =  $myConfig->getResourceUrl( $sAltCssDir, isAdmin() );
    }

    // direct path to templates, there may be stored and css files
    if ( isset($params["csspath"]) && $params["csspath"]) {
        $sTplPath = $params["csspath"];
    } else {
        $sTplPath = $myConfig->getResourcePath( $sAltCssDir, isAdmin() );
    }

    // full path to alternavive CSS file
    $sAltFullPath = $sTplPath . $sAltCss;

    $sOutput = "";
    // checking if alternative CSS file exists and returning URL to CSS file
    if ( $sTplName && file_exists( $sAltFullPath) && is_file( $sAltFullPath)) {
        $sOutput = '<link rel="stylesheet" href="'.$sTplURL . $sAltCss.'">';
    }

    $smarty->caching = false;

    return $sOutput;
}
