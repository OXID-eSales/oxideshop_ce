<?php

if (!defined('IN_WPRO')) exit();
if (WPRO_SESSION_ENGINE=='PHP'&&!isset($_SESSION)) {

    /*
    * WysiwygPro custom session handler setup file.
    * If your application uses custom session handlers (http://www.php.net/manual/en/function.session-set-save-handler.php)
    * then include your session handler functions into this file.
    *
    * Or if your session requires a specific name you will need to set it here.
    *
    * If you want to add your application's user authentication routine to WysiwygPro then it should be added to this file.
    *
    * SIMPLIFIED EXAMPLE:

    // include custom session handler functions:
    include_once('mySessionHandlers.php');
    session_set_save_handler("myOpen", "myClose", "myRead", "myWrite", "myDestroy", "myGC");

    // start the session with a specific name if required:
    session_name('SessionName');
    session_start();

    */


    define( 'OX_IS_ADMIN', true );
    include_once dirname( __FILE__ ) . "/../../../bootstrap.php";

    $myConfig = oxRegistry::getConfig();

    //TODO change this
    // Includes Utility module.
    $sUtilModule = $myConfig->getConfigParam( 'sUtilModule' );
    if ( $sUtilModule && file_exists( getShopBasePath()."modules/".$sUtilModule ) )
        include_once getShopBasePath()."modules/".$sUtilModule;

    $myConfig->setConfigParam( 'blAdmin', true );

    // authorization
    if ( !count(oxRegistry::get("oxUtilsServer")->getOxCookie()) || !oxRegistry::getUtils()->checkAccessRights()) {
        oxRegistry::getUtils()->redirect( $myConfig->getShopURL().'admin/index.php', true, 302 );
    }

}
