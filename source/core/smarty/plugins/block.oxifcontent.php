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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: block.oxid_content.php
 * Type: string, html
 * Name: block_oxifcontent
 * Purpose: Output content snippet if content exists
 * add [{oxifcontent ident="..." }][{/oxifcontent}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param string $content rendered content
 * @param Smarty &$smarty clever simulation of a method
 * @param bool   &$repeat repeat
 *
 * @return string
 */
function smarty_block_oxifcontent( $params, $content, &$smarty, &$repeat)
{
    $myConfig = oxRegistry::getConfig();

    $sIdent  = isset( $params['ident'] )?$params['ident']:null;
    $sOxid   = isset( $params['oxid'] )?$params['oxid']:null;
    $sAssign = isset( $params['assign'])?$params['assign']:null;
    $sObject = isset( $params['object'])?$params['object']:'oCont';

    if ($repeat) {
        if ( $sIdent || $sOxid ) {

            static $aContentCache = array();

            if ( ( $sIdent && isset( $aContentCache[$sIdent] ) ) ||
                 ( $sOxid && isset( $aContentCache[$sOxid] ) ) ) {
                $oContent = $sOxid ? $aContentCache[$sOxid] : $aContentCache[$sIdent];
            } else {
                $oContent = oxNew( "oxContent" );
                $blLoaded = $sOxid ? $oContent->load( $sOxid ) : ( $oContent->loadbyIdent( $sIdent ) );
                if ( $blLoaded && $oContent->isActive() ) {
                    $aContentCache[$oContent->getId()] = $aContentCache[$oContent->getLoadId()] = $oContent;
                } else {
                    $oContent = false;
                    if ( $sOxid ) {
                        $aContentCache[$sOxid] = $oContent;
                    } else {
                        $aContentCache[$sIdent] = $oContent;
                    }
                }
            }

            $blLoaded = false;
            if ( $oContent ) {
                $smarty->assign($sObject, $oContent);
                $blLoaded = true;
            }
        } else {
            $blLoaded = false;
        }
        $repeat = $blLoaded;
    } else {
        $oStr = getStr();
        $blHasSmarty = $oStr->strstr( $content, '[{' );
        if ( $blHasSmarty  ) {
            $content = oxRegistry::get("oxUtilsView")->parseThroughSmarty( $content, $sIdent.md5($content), $myConfig->getActiveView() );
        }

        if ($sAssign) {
            $smarty->assign($sAssign, $content);
        } else {
            return $content;
        }
    }

}
