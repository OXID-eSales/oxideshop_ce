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

if ( !defined( 'OX_IS_ADMIN' ) ) {
    define( 'OX_IS_ADMIN', true );
}

if ( !defined( 'OX_ADMIN_DIR' ) ) {
    define( 'OX_ADMIN_DIR', dirname(__FILE__) );
}

require_once dirname(__FILE__) . "/../bootstrap.php";

// processing ..
$blAjaxCall = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
if ( $blAjaxCall ) {


    // Setting error reporting mode
    error_reporting( E_ALL ^ E_NOTICE);

    $myConfig = oxRegistry::getConfig();

    // Includes Utility module.
    $sUtilModule = $myConfig->getConfigParam( 'sUtilModule' );
    if ( $sUtilModule && file_exists( getShopBasePath()."modules/".$sUtilModule ) )
        include_once getShopBasePath()."modules/".$sUtilModule;

    $myConfig->setConfigParam( 'blAdmin', true );

    // authorization
    if ( !(oxRegistry::getSession()->checkSessionChallenge() && count(oxRegistry::get("oxUtilsServer")->getOxCookie()) && oxRegistry::getUtils()->checkAccessRights())) {
        header( "location:index.php");
        oxRegistry::getUtils()->showMessageAndExit( "" );
    }

    if ( $sContainer = oxConfig::getParameter( 'container' ) ) {

        $sContainer = trim(strtolower( basename( $sContainer ) ));

        try{
            $oAjaxComponent = oxNew($sContainer.'_ajax');
        }
        catch (oxSystemComponentException $oCe ){
            $sFile = 'inc/'.$sContainer.'.inc.php';
            if ( file_exists( $sFile ) ) {
                $aColumns = array();
                include_once $sFile;
                $oAjaxComponent = new ajaxcomponent( $aColumns );
                $oAjaxComponent->init( $aColumns );
            } else {
                $oEx = oxNew ( 'oxFileException' );
                $oEx->setMessage( 'EXCEPTION_FILENOTFOUND' );
                $oEx->setFileName( $sFile );
                $oEx->debugOut();
                throw $oEx;
            }
        }

        $oAjaxComponent->setName( $sContainer );
        $oAjaxComponent->processRequest( oxConfig::getParameter( 'fnc' ) );

    } else {

    }

    $myConfig->pageClose();

    // closing session handlers
    // session_write_close();
    return;
}