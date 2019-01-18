<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Setup;

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
        $password = 'l3$z4f#bu\'xyz\\\'zh"ad\\"dc$1\1\\1\2v5745XC$lic';
        $url = 'http://test.myoxidshop.com';

        /** @var  $originalFile take the real config.inc.php.dist for testing as this is the blueprint for config.inc.php */
        $originalFile = OX_BASE_PATH . 'config.inc.php.dist';
        if (!realpath($originalFile)) {
            $originalFile = VENDOR_PATH .
                            'oxid-esales' . DIRECTORY_SEPARATOR .
                            'oxideshop-ce' . DIRECTORY_SEPARATOR .
                            'source' . DIRECTORY_SEPARATOR .
                            'config.inc.php.dist';
        }
        if (!realpath($originalFile)) {
            throw new Exception('Configuration file template \'config.inc.php.dist\' not found');
        }
        $destinationDirectory = realpath($this->configTestPath);
        if (!is_writable(realpath($destinationDirectory))) {
            throw new Exception($destinationDirectory . ' is not writable');
        }

        $destinationFile = $destinationDirectory . '/config.inc.php';
        file_put_contents($destinationFile, file_get_contents($originalFile));
        $this->assertNotContains($password, $destinationFile);

        $configParameters = [
            'sShopDir' => $destinationDirectory,
            'dbPwd'    => $password,
            'sShopURL' => $url
        ];

        //check
        try {
            $utilities->updateConfigFile($configParameters);
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        /**
         * Test if the _values_ are assigned correctly:
         * - file can be parsed without problems
         * - the properties are set to the correct values
         */
        include $destinationFile;
        foreach ($configParameters as $key => $value) {
            $this->assertEquals($value, $this->{$key}, "The value for the parameter $key was not updated as expected");
        }
    }

    /**
     * @param string $testInput
     * @param string $expectedValue
     * @param string $explanationOnWhatIsChecked
     *
     * @dataProvider stripAnsiControlCodesDataProvider
     */
    public function testStripAnsiControlCodes($testInput, $expectedValue, $explanationOnWhatIsChecked)
    {
        $actualValue = Utilities::stripAnsiControlCodes($testInput);

        $this->assertSame($expectedValue, $actualValue, "Test case which failed: $explanationOnWhatIsChecked");
    }

    public function stripAnsiControlCodesDataProvider()
    {
        return [
            [
                "Regular text with no ANSI controls",
                "Regular text with no ANSI controls",
                "No ANSI codes used",
            ],
            [
                "Test of \e[1;31mcolored\e[0m text",
                "Test of colored text",
                "Red foreground color",
            ],
            [
                "Test of \e[44mbackground\e[0m text",
                "Test of background text",
                "Blue background color",
            ],
            [
                "Test of \e[1;31m\e[44mcolored background\e[0m text",
                "Test of colored background text",
                "Red foreground with blue background color",
            ],
            [
                "\e[0m\e[0m\e[0m\e[0m",
                "",
                "ANSI control codes only, empty text",
            ],
            [
                "\e[0ma\e[0m\n\e[0mb\e[0m",
                "a\nb",
                "ANSI control codes combined with new lines and simple text",
            ],
            [
                "",
                "",
                "Empty string",
            ],
            [
                null,
                "",
                "Null converted to empty string",
            ]
        ];
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
