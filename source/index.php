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

// Setting error reporting mode
    // some 3rd party libraries still use deprecated functions
    if ( defined( 'E_DEPRECATED' ) ) {
        error_reporting( E_ALL ^ E_NOTICE ^ E_DEPRECATED );
    } else {
        error_reporting( E_ALL ^ E_NOTICE );
    }

//Know exactly where in the code the event occurred.
//Zend platform only.
if (function_exists('monitor_set_aggregation_hint') && isset($_REQUEST['cl'])) {
    $sAgregationHint = htmlentities($_REQUEST['cl'], ENT_QUOTES, 'UTF-8') . '/';
    if (isset($_REQUEST['fnc']))
        $sAgregationHint .= htmlentities($_REQUEST['fnc'], ENT_QUOTES, 'UTF-8');
    monitor_set_aggregation_hint($sAgregationHint);
}


//setting basic configuration parameters
ini_set('session.name', 'sid' );
ini_set('session.use_cookies', 0 );
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');
ini_set('magic_quotes_runtime', 0);

/**
 * Returns shop base path.
 *
 * @return string
 */
function getShopBasePath()
{
    return dirname(__FILE__).'/';
}


if ( !function_exists( 'isAdmin' )) {
    /**
     * Returns false.
     *
     * @return bool
     */
    function isAdmin()
    {
        return false;
    }
}

// custom functions file
require getShopBasePath() . 'modules/functions.php';

// Generic utility method file
require_once getShopBasePath() . 'core/oxfunctions.php';


// set the exception handler already here to catch everything, also uncaught exceptions from the config or utils

// initializes singleton config class
$myConfig = oxConfig::getInstance();

//strips magics quote if any
oxUtils::getInstance()->stripGpcMagicQuotes();

// reset it so it is done with oxnew
$iDebug = $myConfig->getConfigParam('iDebug');
set_exception_handler(array(oxNew('oxexceptionhandler', $iDebug), 'handleUncaughtException'));
// Admin handling
if ( isAdmin() ) {
    $myConfig->setConfigParam( 'blAdmin', true );
    $myConfig->setConfigParam( 'blTemplateCaching', false );
    if ($sAdminDir)
        $myConfig->setConfigParam( 'sAdminDir', $sAdminDir );
    else
        $myConfig->setConfigParam( 'sAdminDir', "admin" );
}

//Starting the shop
$oShopControl = oxNew('oxShopControl');
$oShopControl->start();
