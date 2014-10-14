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

error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
ini_set('display_errors', true);

chdir( dirname(__FILE__) );

require_once "test_config.php";

if ( file_exists( "test_config.local.php" ) ) {
    include_once "test_config.local.php";
}

define( 'oxPATH', getenv('oxPATH')? getenv('oxPATH') : $sShopPath );
define ( 'OXID_VERSION', getenv('OXID_VERSION')? getenv('OXID_VERSION') : $sShopEdition );

define ( 'INSTALLSHOP', getenv('oxINSTALLSHOP')? getenv('oxINSTALLSHOP') : $blInstallShop );
define ( 'SKIPSHOPSETUP', getenv('oxSKIPSHOPSETUP')? getenv('oxSKIPSHOPSETUP') : $blSkipShopSetup );
define ( 'SKIPSHOPRESTORE', getenv('oxSKIPSHOPRESTORE')? getenv('oxSKIPSHOPRESTORE') : $blSkipShopRestore );

define ('OXID_TEST_UTF8', getenv('OXID_TEST_UTF8')? getenv('OXID_TEST_UTF8') : $blUtf8);
define ('OXID_VARNISH', getenv('OXID_VARNISH')? getenv('OXID_VARNISH') : $blVarnish);
define ('PREG_FILTER', getenv('PREG_FILTER'));
define ('TEST_DIRS', getenv('TEST_DIRS'));

    switch ( OXID_VERSION ) {
        case 'EE':
            define('OXID_VERSION_EE', true );
            define('OXID_VERSION_PE', false);
            define('OXID_VERSION_PE_PE', false );
            define('OXID_VERSION_PE_CE', false );
            break;
        case 'PE':
            define('OXID_VERSION_EE',    false);
            define('OXID_VERSION_PE',    true );
            define('OXID_VERSION_PE_PE', true );
            define('OXID_VERSION_PE_CE', false );
            break;
        case 'CE':
            define('OXID_VERSION_EE',    false);
            define('OXID_VERSION_PE',    true );
            define('OXID_VERSION_PE_PE', false );
            define('OXID_VERSION_PE_CE', true );
            break;

        default:
            die('bad version--- : '."'".getenv('OXID_VERSION')."'");
            break;
    }


if (!defined('oxPATH')) {
        die('oxPATH is not defined');
}

define ('oxCCTempDir', oxPATH.'/oxCCTempDir/');
if (!is_dir(oxCCTempDir)) {
    mkdir(oxCCTempDir, 0777, 1);
} else {
    array_map('unlink', glob(oxCCTempDir."/*"));
}


require_once 'unit/test_config.inc.php';
require_once "unit/OxidTestCase.php";

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
if (getenv('oxADMIN_PASSWD')) {
    define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD'));
} else {
    define('oxADMIN_PASSWD', 'admin');
}
