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
 * File: insert.oxid_nocache.php
 * Type: string, html
 * Name: oxid_nocache
 * Purpose: Inserts Items not cached
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_insert_oxid_nocache($params, &$smarty)
{   $myConfig = oxRegistry::getConfig();

    $smarty->caching = false;

   /* if( isset( $smarty->oxobject->oProduct))
        $smarty->assign_by_ref( "product", $smarty->oxobject->oProduct);*/

    // #1184M - specialchar search
    $sSearchParamForHTML = oxConfig::getParameter("searchparam");
    $sSearchParamForLink = rawurlencode( oxConfig::getParameter( "searchparam", true ) );
    if ( $sSearchParamForHTML ) {
        $smarty->assign_by_ref( "searchparamforhtml", $sSearchParamForHTML );
        $smarty->assign_by_ref( "searchparam", $sSearchParamForLink );
    }

    $sSearchCat = oxConfig::getParameter("searchcnid");
    if( $sSearchCat )
        $smarty->assign_by_ref( "searchcnid", rawurldecode( $sSearchCat ) );

    foreach (array_keys( $params) as $key) {
        $viewData = & $params[$key];
        $smarty->assign_by_ref($key, $viewData);
    }

    $sOutput = $smarty->fetch( $params['tpl']);

    $smarty->caching = false;

    return $sOutput;
}
