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

error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
ini_set('display_errors', true);

chdir(dirname(__FILE__));

require_once "bootstrap_config.php";

if (!defined('oxPATH')) {
    die('Path to tested shop (oxPATH) is not defined');
}

define ('OXID_PHP_UNIT', true);

define ('oxCCTempDir', oxPATH . '/oxCCTempDir/');
if (!is_dir(oxCCTempDir)) {
    mkdir(oxCCTempDir, 0777, 1);
} else {
    array_map('unlink', glob(oxCCTempDir . "/*"));
}


require_once 'unit/test_config.inc.php';

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
define ('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD') ? getenv('oxADMIN_PASSWD') : 'admin');
