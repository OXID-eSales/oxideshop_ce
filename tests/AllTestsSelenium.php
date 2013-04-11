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
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

switch (getenv('OXID_VERSION')) {
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

// browser name which will be used for testing. Possible values: *iexplore, *iehta, *firefox, *chrome, *piiexplore, *pifirefox, *safari, *opera
// make sure that path to browser executable is known for the system
define('browserName', '*firefox' );

// URL to testible eShop
define('shopURL', getenv('SELENIUM_TARGET'));
define('hostUrl', getenv('SELENIUM_SERVER'));



define ('oxCCTempDir', oxPATH.'/tmp/');

// if running on NON-parsed source - change shopPrefix value to '_ee'
define('shopPrefix', '');
define('isSUBSHOP', OXID_VERSION_EE && (oxSHOPID > 1));

if (getenv('OXID_LOCALE') == 'international') {
    define ('oxTESTSUITEDIR', 'acceptanceInternational');
} elseif (getenv('OXID_TEST_EFIRE')) {
    define ('oxTESTSUITEDIR', 'acceptanceEfire');
} else {
    define ('oxTESTSUITEDIR', 'acceptance');
}

if (getenv('MODULE_PKG_DIR')) {
    define ('MODULE_PKG_DIR', getenv('MODULE_PKG_DIR'));
}

if (getenv('SHOP_REMOTE')) {
    define ('SHOP_REMOTE', getenv('SHOP_REMOTE'));
}

if (getenv('oxSKIPSHOPSETUP') == 1) {
    define ('SKIPSHOPSETUP', true);
} else {
    define ('SKIPSHOPSETUP', false);
}

if (getenv('TEST_DATA_DIR')) {
    define('oxTESTDATADIR', oxTESTSUITEDIR.'/'.getenv('TEST_DATA_DIR'));
} else {
    define('oxTESTDATADIR', oxTESTSUITEDIR);
}

    define('demoData', oxTESTDATADIR.'/demodata_PE.sql');

require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * PHPUnit_Framework_TestCase implementation for adding and testing all selenium tests from this dir
 */
class AllTestsSelenium extends PHPUnit_Framework_TestCase
{
    /**
     * Test suite
     *
     * @return object
     */
    static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');

        require_once 'acceptance/oxidAdditionalSeleniumFunctions.php';
        $oAdditionalFunctions = new oxidAdditionalSeleniumFunctions();

        // dumping original database
        try {
            $oAdditionalFunctions->dumpDB('orig_db_dump');
        } catch (Exception $e) {
            $oAdditionalFunctions->stopTesting("Failed dumping original db");
        }

        if (!SKIPSHOPSETUP) {
            //installing shop versions using selenium
            include_once 'acceptance/shopSetUp.php';
            $suite->addTestSuite('shopSetUp');

            // add seleniums demodata
            $sFileName = "acceptance/demodata_PE.sql";
            $oAdditionalFunctions->addDemoData($sFileName);

        }

        // dumping database for selenium tests
        try {
            $oAdditionalFunctions->dumpDB();
        } catch (Exception $e) {
            $oAdditionalFunctions->stopTesting("Failed dumping db");
        }

        //adding ACCEPTANCE Tests
        if (!($sFilter = getenv('TEST_FILE_FILTER'))) {
            $sFilter = '*';
        }
        $sGlob = oxTESTSUITEDIR."/{$sFilter}Test.php";

        foreach ( glob($sGlob) as $sFilename) {
            include_once $sFilename;
            $sFilename = oxTESTSUITEDIR.'_'.str_replace("/", "_", str_replace( array( ".php", oxTESTSUITEDIR.'/'), "", $sFilename));
            $suite->addTestSuite( $sFilename);
        }

        //killing firefox windows after all selenium tests are ran
        include_once 'acceptance/shopTearDown.php';
        $suite->addTestSuite('shopTearDown');

        return $suite;
    }
}
