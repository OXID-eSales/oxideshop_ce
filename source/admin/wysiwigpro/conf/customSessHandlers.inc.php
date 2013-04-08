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

    // shop path for includes
    if ( !function_exists( 'getShopBasePath' ) ) {
        /**
         * Returns shop base path.
         *
         * @return string
         */
        function getShopBasePath()
        {
            return dirname(__FILE__).'/../../../';
        }
    }

    //to load the session correctly
    if ( !function_exists( 'isAdmin' ) ) {
        /**
         * Method used to override default isAdmin. It simulates admin mode. Returns true
         *
         * @return bool
         */
        function isAdmin()
        {
            return true;
        }
    }

    $sBasePath = getShopBasePath();


    // Setting error reporting mode
    error_reporting( E_ALL ^ E_NOTICE);

    //setting basic configuration parameters
    ini_set('session.name', 'sid' );
    ini_set('session.use_cookies', 0 );
    ini_set('session.use_trans_sid', 0);
    ini_set('url_rewriter.tags', '');
    ini_set('magic_quotes_runtime', 0);

    // custom functions file
    require $sBasePath . 'modules/functions.php';

    // Generic utility method file.
    require_once $sBasePath . 'core/oxfunctions.php';
    require_once $sBasePath . 'core/adodblite/adodb.inc.php';
    require_once $sBasePath . 'core/oxconfig.php';
    require_once $sBasePath . 'core/oxsupercfg.php';

    // TODO: check this some day before release :)
    require_once $sBasePath . "core/oxutils.php";

    $myConfig = oxConfig::getInstance();

    //TODO change this
    // Includes Utility module.
    $sUtilModule = $myConfig->getConfigParam( 'sUtilModule' );
    if ( $sUtilModule && file_exists( getShopBasePath()."modules/".$sUtilModule ) )
        include_once getShopBasePath()."modules/".$sUtilModule;

    $myConfig->setConfigParam( 'blAdmin', true );
    $myConfig->setConfigParam( 'blTemplateCaching', false );

    // authorization
    if ( !count(oxUtilsServer::getInstance()->getOxCookie()) || !oxUtils::getInstance()->checkAccessRights()) {
        oxUtils::getInstance()->redirect( $myConfig->getShopURL().'admin/index.php', true, 302 );
    }

}
