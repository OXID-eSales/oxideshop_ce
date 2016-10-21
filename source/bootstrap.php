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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;

if (defined('E_DEPRECATED')) {
    //E_DEPRECATED is disabled particularly for PHP 5.3 as some 3rd party modules still uses deprecated functionality
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
} else {
    error_reporting(E_ALL ^ E_NOTICE);
}

if (!defined('OX_BASE_PATH')) {
    define('OX_BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

// custom functions file
if (file_exists(OX_BASE_PATH . 'modules/functions.php')) {
    include_once OX_BASE_PATH . 'modules/functions.php';
}

// Generic utility method file including autoloading definition
require_once OX_BASE_PATH . 'oxfunctions.php';

// Make actions if there are eShop configuration problems
showErrorIfConfigIsMissing();
redirectIfShopNotConfigured();

// Composer autoloader.
registerComposerAutoload();

//init config.inc.php file reader
$oConfigFile = new ConfigFile(OX_BASE_PATH . "config.inc.php");
Registry::set("oxConfigFile", $oConfigFile);

registerVirtualNamespaceAutoLoad();
registerShopAutoLoad();
registerModuleAutoload();

//sets default PHP ini params
ini_set('session.name', 'sid');
ini_set('session.use_cookies', 0);
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');
