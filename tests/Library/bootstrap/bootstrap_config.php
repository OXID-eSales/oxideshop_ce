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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: $
 */

require_once TESTS_DIRECTORY . "test_config.php";
if (file_exists(TESTS_DIRECTORY . "test_config.local.php")) {
    include_once TESTS_DIRECTORY . "test_config.local.php";
}

define('OXID_PHP_UNIT', true);

$sShopPath = getenv('oxPATH') ? getenv('oxPATH') : $sShopPath;
define('oxPATH', rtrim($sShopPath, '/') . '/');
define('REMOTE_DIR', getenv('REMOTE_DIR')? getenv('REMOTE_DIR') : $sRemoteDir);

define('INSTALLSHOP', getenv('oxINSTALLSHOP') !== false ? (bool) getenv('oxINSTALLSHOP') : $blInstallShop);
define('ADD_TEST_DATA', getenv('oxSKIPSHOPSETUP') !== false ? (bool) !getenv('oxSKIPSHOPSETUP') : $blAddTestData);
define('RESTORE_SHOP_AFTER_TEST_SUITE', getenv('oxSKIPSHOPRESTORE') !== false ? (bool) !getenv('oxSKIPSHOPRESTORE') : $blRestoreShopAfterTestSuite);
define('RESTORE_SHOP_AFTER_TEST', getenv('oxSKIPSHOPRESTORE') !== false ? (bool) !getenv('oxSKIPSHOPRESTORE') : $blRestoreShopAfterTest);

define('SHOP_SETUP_PATH', getenv('SHOP_SETUP_PATH') ? getenv('SHOP_SETUP_PATH') : $sShopSetupPath);
define('MODULES_PATH', getenv('MODULES_PATH') ? getenv('MODULES_PATH') : $sModulesPath);

define('SHOPRESTORATIONCLASS', getenv('SHOPRESTORATIONCLASS') ? getenv('SHOPRESTORATIONCLASS') : $sDataBaseRestore);
define('COPY_SERVICES_TO_SHOP', getenv('COPY_SERVICES_TO_SHOP') !== false ? (bool) getenv('COPY_SERVICES_TO_SHOP') : $blCopyServicesToShop);

define('OXID_VERSION', getenv('OXID_VERSION')); // only used for deploy test. If not set - package version is not checked.
define('TEST_SHOP_SERIAL', getenv('TEST_SHOP_SERIAL') ? getenv('TEST_SHOP_SERIAL') : $sShopSerial);
define('OXID_VARNISH', getenv('OXID_VARNISH') !== false ? (bool) getenv('OXID_VARNISH') : $blVarnish);

    define('OXID_VERSION_SUFIX', '');

if (!defined('oxPATH')) {
    die('Path to tested shop (oxPATH) is not defined');
}

$sShopId = "oxbaseshop";
define('oxSHOPID', $sShopId);

$sShopUrl = getenv('SELENIUM_TARGET')? getenv('SELENIUM_TARGET') : $sShopUrl;
if (!$sShopUrl) {
    include_once oxPATH.'core/oxconfigfile.php';
    $oConfigFile = new oxConfigFile(oxPATH . "config.inc.php");
    $sShopUrl = $sShopUrl ? $sShopUrl : $oConfigFile->sShopURL;
}
define('shopURL', rtrim($sShopUrl, '/').'/');

$blIsSubShop = false;
define('isSUBSHOP', $blIsSubShop);

define('oxCCTempDir', oxPATH . '/oxCCTempDir/');