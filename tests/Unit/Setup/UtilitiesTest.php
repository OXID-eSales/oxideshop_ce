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
namespace Unit\Setup;

use OxidEsales\EshopCommunity\Setup\Utilities;
use \Exception;

require_once getShopBasePath() . '/Setup/functions.php';

/**
 * Utilities tests
 */
class UtilitiesTest extends \OxidTestCase
{
    protected $_sPathTranslated = null;
    protected $_sScriptFilename = null;
    protected $_sHttpReferer = null;
    protected $_sHttpHost = null;
    protected $_sScriptName = null;

    /**
     * @var path to test config directory
     */
    protected $configTestPath = null;

    /**
     * Test setup
     */
    protected function setUp()
    {
        // backup..
        $this->_sPathTranslated = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : null;
        $this->_sScriptFilename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : null;
        $this->_sHttpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $this->_sHttpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        $this->_sScriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null;

        parent::setUp();

        $this->configTestPath = __DIR__ .'/../testData/Setup';
        $this->removeTestFile();
    }

    /**
     * Test teardown
     *
     * @return null
     */
    protected function tearDown()
    {
        if (isset($_POST["testPostVarName"])) {
            unset($_POST["testPostVarName"]);
        }

        if (isset($_GET["testGetVarName"])) {
            unset($_GET["testGetVarName"]);
        }

        // restore
        $_SERVER['PATH_TRANSLATED'] = $this->_sPathTranslated;
        $_SERVER['SCRIPT_FILENAME'] = $this->_sScriptFilename;
        $_SERVER['HTTP_REFERER'] = $this->_sHttpReferer;
        $_SERVER['HTTP_HOST'] = $this->_sHttpHost;
        $_SERVER['SCRIPT_NAME'] = $this->_sScriptName;

        //clean up
        $this->removeTestFile();

        parent::tearDown();
    }

    /**
     * Testing Utilities::getFileContents()
     *
     * @return null
     */
    public function testGetFileContents()
    {
        $sLicenseFile = "lizenz.txt";

        $sFilePath = getShopBasePath() . "Setup/En/{$sLicenseFile}";

        $oUtils = new Utilities();
        $this->assertEquals(file_get_contents($sFilePath), $oUtils->getFileContents($sFilePath));
    }

    /**
     * Testing Utilities::getDefaultPathParams()
     *
     * @return null
     */
    public function testGetDefaultPathParams()
    {
        $sTmp = "tmp/";

        $_SERVER['PATH_TRANSLATED'] = null;
        $_SERVER['HTTP_REFERER'] = null;
        $_SERVER['SCRIPT_FILENAME'] = "/var/www/ee440setup/setup/index.php";
        $_SERVER['SCRIPT_NAME'] = "/ee440setup/setup/index.php";
        $_SERVER['HTTP_HOST'] = "127.0.0.1:1001";

        // paths
        $aParams['sShopDir'] = "/var/www/ee440setup/";
        $aParams['sCompileDir'] = $aParams['sShopDir'] . $sTmp;
        $aParams['sShopURL'] = "http://127.0.0.1:1001/ee440setup/";

        $oUtils = new Utilities();
        $this->assertEquals($aParams, $oUtils->getDefaultPathParams());
    }

    /**
     * test for bug #0002043: System requirements check for "Files/folders access rights" always fails
     *
     * @return null
     */
    public function testGetDefaultPathParamsIfPathTranslatedIsEmpty()
    {
        $sTmp = "tmp/";

        $_SERVER['PATH_TRANSLATED'] = '';
        $_SERVER['HTTP_REFERER'] = null;
        $_SERVER['SCRIPT_FILENAME'] = "/var/www/ee440setup/setup/index.php";
        $_SERVER['SCRIPT_NAME'] = "/ee440setup/setup/index.php";
        $_SERVER['HTTP_HOST'] = "127.0.0.1:1001";

        // paths
        $aParams['sShopDir'] = "/var/www/ee440setup/";
        $aParams['sCompileDir'] = $aParams['sShopDir'] . $sTmp;
        $aParams['sShopURL'] = "http://127.0.0.1:1001/ee440setup/";

        $oUtils = new Utilities();
        $this->assertEquals($aParams, $oUtils->getDefaultPathParams());
    }

    /**
     * Testing Utilities::getEnvVar()
     *
     * @return null
     */
    public function testGetEnvVar()
    {
        // ENV is not always filled in..
        if (count($_ENV)) {
            $sValue = current($_ENV);
            $sName = key($_ENV);

            $oUtils = new Utilities();
            $this->assertEquals($sValue, $oUtils->getEnvVar($sName));
        }
    }

    /**
     * Testing Utilities::getRequestVar()
     *
     * @return null
     */
    public function testGetRequestVar()
    {
        $_POST["testPostVarName"] = "testPostVarValue";
        $_GET["testGetVarName"] = "testGetVarValue";

        $oUtils = new Utilities();
        $this->assertEquals("testPostVarValue", $oUtils->getRequestVar("testPostVarName"));
        $this->assertEquals("testGetVarValue", $oUtils->getRequestVar("testGetVarName"));
    }

    /**
     * Testing Utilities::preparePath()
     *
     * @return null
     */
    public function testPreparePath()
    {
        $oUtils = new Utilities();
        $this->assertEquals("c:/www/oxid", $oUtils->preparePath('c:\\www\\oxid\\'));
        $this->assertEquals("/htdocs/eshop", $oUtils->preparePath('/htdocs/eshop/'));
        $this->assertEquals("/o/x/i/d", $oUtils->preparePath('/o/x/i/d/'));
    }

    /**
     * Testing Utilities::extractBasePath()
     *
     * @return null
     */
    public function testExtractBasePath()
    {
        $oUtils = new Utilities();
        $this->assertEquals("nothing", $oUtils->extractRewriteBase("nothing"));
        $this->assertEquals("/folder", $oUtils->extractRewriteBase("http://www.shop.com/folder/"));
        $this->assertEquals("www.shop.com/folder", $oUtils->extractRewriteBase("www.shop.com/folder/"));
        $this->assertEquals("/folder", $oUtils->extractRewriteBase("http://www.shop.com/folder"));
        $this->assertEquals("/folder", $oUtils->extractRewriteBase("http://shop.com/folder"));
    }

    /**
     * Testing Utilities::isValidEmail()
     *
     * @return null
     */
    public function testIsValidEmail()
    {
        $oUtils = new Utilities();
        $this->assertFalse($oUtils->isValidEmail("admin"));
        $this->assertTrue($oUtils->isValidEmail("shop@admin.com"));
    }

    /**
     * Verify that Utilities::updateConfigFile stores the given variables correctly.
     *
     * @throws \Exception
     */
    public function testUpdateConfigFileForPassword()
    {
        //preparation
        $this->assertTrue(function_exists('getDefaultFileMode'), 'missing function getDefaultFileMode');
        $this->assertTrue(function_exists('getDefaultConfigFileMode'), 'missing function getDefaultConfigFileMode');

        $utilities = new Utilities();
        $password = 'l3$z4f#buzhdcv5745XC$lic';
        $url = 'http://test.myoxidshop.com';

        $originalFile = $this->configTestPath . '/config.inc.php.dist';
        $destination = $this->configTestPath . '/config.inc.php';

        file_put_contents($destination, file_get_contents($originalFile));
        $this->assertNotContains($password, $destination);

        $parameters = ['sShopDir' => $this->configTestPath,
                       'testPassword' => $password,
                       'testUrl' => $url];

        //check
        try {
            $utilities->updateConfigFile($parameters);
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        $content = file_get_contents($this->configTestPath . '/config.inc.php');
        $this->assertContains($url, $content);
        $this->assertContains($password, $content);
    }

    /**
     * Test helper for cleaning up files.
     */
    private function removeTestFile()
    {
        $file = $this->configTestPath . '/config.inc.php';
        if (file_exists($file)) {
            chmod($file, 0777);
            unlink($file);
        }
    }
}
