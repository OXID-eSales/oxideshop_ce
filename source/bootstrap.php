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


if (defined('E_DEPRECATED')) {
    //E_DEPRECATED is disabled particularly for PHP 5.3 as some 3rd party modules still uses deprecated functionality
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
} else {
    error_reporting(E_ALL ^ E_NOTICE);
}


if (!defined('OX_BASE_PATH')) {
    define('OX_BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

// custom functions file
require_once OX_BASE_PATH . 'modules/functions.php';

// Generic utility method file including autoloading definition
require_once OX_BASE_PATH . 'core/oxfunctions.php';

//sets default PHP ini params
setPhpIniParams();

//init config.inc.php file reader
$oConfigFile = new oxConfigFile(OX_BASE_PATH . "config.inc.php");

oxRegistry::set("oxConfigFile", $oConfigFile);
