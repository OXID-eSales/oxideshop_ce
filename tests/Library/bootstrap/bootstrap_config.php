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

define('INSTALLSHOP', getenv('oxINSTALLSHOP') ? getenv('oxINSTALLSHOP') : $blInstallShop);
define('ADD_TEST_DATA', getenv('oxSKIPSHOPSETUP') ? !getenv('oxSKIPSHOPSETUP') : $blAddTestData);
define('RESTORE_SHOP_AFTER_TEST_SUITE', getenv('oxSKIPSHOPRESTORE') ? !getenv('oxSKIPSHOPRESTORE') : $blRestoreShopAfterTestSuite);
define('RESTORE_SHOP_AFTER_TEST', getenv('oxSKIPSHOPRESTORE') ? !getenv('oxSKIPSHOPRESTORE') : $blRestoreShopAfterTest);

define('SHOPRESTORATIONCLASS', getenv('SHOPRESTORATIONCLASS') ? getenv('SHOPRESTORATIONCLASS') : $sDataBaseRestore);
define('COPYSERVICESTOSHOP', getenv('COPYSERVICESTOSHOP') ? getenv('SHOPRESTORATIONCLASS') : $blCopyServicesToShop);

define('OXID_VERSION', getenv('OXID_VERSION') ? getenv('OXID_VERSION') : $sShopEdition);
define('TEST_SHOP_SERIAL', getenv('TEST_SHOP_SERIAL') ? getenv('TEST_SHOP_SERIAL') : $sShopSerial);
define('OXID_VARNISH', getenv('OXID_VARNISH') ? getenv('OXID_VARNISH') : $blVarnish);

    switch (OXID_VERSION) {
        case 'EE':
            define('OXID_VERSION_EE', true);
            define('OXID_VERSION_PE', false);
            define('OXID_VERSION_PE_PE', false);
            define('OXID_VERSION_PE_CE', false);
            break;
        case 'PE':
            define('OXID_VERSION_EE', false);
            define('OXID_VERSION_PE', true);
            define('OXID_VERSION_PE_PE', true);
            define('OXID_VERSION_PE_CE', false);
            break;
        case 'CE':
            define('OXID_VERSION_EE', false);
            define('OXID_VERSION_PE', true);
            define('OXID_VERSION_PE_PE', false);
            define('OXID_VERSION_PE_CE', true);
            break;

        default:
            die('bad version--- : ' . "'" . getenv('OXID_VERSION') . "'");
            break;
    }
    $sSuffix = '';
    define('OXID_VERSION_SUFIX', $sSuffix);

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

define('isSUBSHOP', OXID_VERSION_EE && (oxSHOPID > 1));

define('oxCCTempDir', oxPATH . '/oxCCTempDir/');