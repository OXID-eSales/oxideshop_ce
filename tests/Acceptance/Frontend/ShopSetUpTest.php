<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Setup\Utilities;
use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;
use OxidEsales\TestingLibrary\ServiceCaller;
use OxidEsales\TestingLibrary\TestConfig;

/** Selenium tests for frontend navigation. */
class ShopSetUpTest extends FrontendTestCase
{
    const WELCOME_STEP = 'step0Submit';
    const LICENSE_STEP = 'step1Submit';
    const DATABASE_INFO_STEP = 'step2Submit';
    const DIRECTORY_LOGIN_STEP = 'step3Submit';
    const FINISH_CE_STEP = 'step4Submit';
    const FINISH_PE_EE_STEP = 'step5Submit';

    const CLICK_AND_WAIT_TIMEOUT = 2;

    const SYNTAX_ERROR_STRING = "SYNTAX ERROR!";
    const INVALID_LICENSE_SERIAL_NUMBER = 'INVALID_LICENSE_NUMBER';

    const DEMODATA_SQL_FILENAME = 'demodata.sql';
    const DATABASE_SCHEMA_SQL_FILENAME = 'database_schema.sql';
    const INITIAL_DATA_SQL_FILENAME = 'initial_data.sql';
    const HTACCESS_FILENAME = '.htaccess';
    const PACKAGE_INDICATOR_FILENAME = 'pkg.info';
    const DB_MIGRATE_SCRIPT_FILENAME = 'oe-eshop-doctrine_migration';
    const DB_VIEWS_REGENERATE_SCRIPT_FILENAME = 'oe-eshop-db_views_generate';

    const SETUP_DIRECTORY = 'Setup';
    const SOURCE_DIRECTORY = 'source';
    const DEMODATA_SOURCE_DIRECTORY = 'src';
    const SETUP_SQL_DIRECTORY = 'Sql';
    const INVALID_MIGRATION_FILENAME = 'Version20170101.php';
    const MIGRATION_DIRECTORY = 'migration';
    const MIGRATION_DATA_DIRECTORY = 'data';
    const VENDOR_DIRECTORY = 'vendor';
    const VENDOR_BIN_DIRECTORY = 'bin';
    const OXID_ESALES_VENDOR_DIRECTORY = 'oxid-esales';
    const SHOP_DIRECTORY_FROM_COMPOSER_VENDOR_PATH = 'oxideshop-%s';
    const DEMODATA_DIRECTORY_FROM_COMPOSER_VENDOR_PATH = 'oxideshop-demodata-%s';

    const CE_EDITION_ID = 'CE';

    /** @var int How much more time wait for these tests. */
    protected $_iWaitTimeMultiplier = 7;

    protected function setUp()
    {
        $this->preventModuleVersionNotify = false;
        parent::setUp();

        $this->restoreModifiedFiles();
    }

    protected function tearDown()
    {
        $this->restoreModifiedFiles();

        $this->resetShop();
        parent::tearDown();

        $this->cleanupDemodataPackageImitation();

        $oServiceCaller = new ServiceCaller($this->getTestConfig());
        $oServiceCaller->callService('ViewsGenerator', 1);
    }

    /**
     * Tests installation of new shop version (setup).
     * Test chooses demo data if possible test data otherwise.
     * In CI:
     * - nightlies run with demo data
     * - dailies run without demo data
     *
     * @group main
     */
    public function testInstallShop()
    {
        $this->clearDatabase();

        $this->goToSetup();

        // Step 1
        $this->assertTextPresent("Welcome to OXID eShop installation wizard");
        $this->assertElementPresent("setup_lang");
        $this->assertEquals("English Deutsch", trim(preg_replace("/[ \r\n]*[\r\n][ \r\n]*/", ' ', $this->getText("setup_lang"))));
        $this->select("setup_lang", "English");
        $this->assertEquals("English", $this->getSelectedLabel("setup_lang"));
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        // Step 2
        $this->assertTextPresent("Welcome to OXID eShop installation wizard");
        $this->assertElementPresent("check_for_updates_ckbox");
        $this->assertEquals("off", $this->getValue("check_for_updates_ckbox"));

        $this->check("check_for_updates_ckbox");

        if (getenv('OXID_LOCALE') == 'international') {
            $this->assertElementPresent("sShopLang");
            $this->select("sShopLang", "English");
        } else {
            $this->assertElementPresent("sShopLang");
            $this->select("sShopLang", "Deutsch");
        }

        $this->assertElementPresent("country_lang");
        $this->select("country_lang", "Germany");
        $this->checkForErrors();

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            //there is no such checkbox for EE or utf mode
            $this->assertElementPresent("send_technical_information_to_oxid_checkbox");
            $this->assertElementVisible("send_technical_information_to_oxid_checkbox");
            $this->assertEquals("off", $this->getValue("send_technical_information_to_oxid_checkbox"));
            $this->check("send_technical_information_to_oxid_checkbox");
            $this->assertEquals("on", $this->getValue("send_technical_information_to_oxid_checkbox"));
            $this->checkForErrors();
        } else {
            $this->assertElementNotPresent("send_technical_information_to_oxid_checkbox");
        }

        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        // Step 3
        $this->assertElementPresent("iEula");
        $this->check("iEula");
        $this->checkForErrors();
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        // Step 4
        $this->assertEquals("off", $this->getValue("sDbPassCheckbox"));
        $this->assertTrue($this->isEditable("sDbPass"), "Element not editable: sDbPass");
        $this->assertFalse($this->isEditable("sDbPassPlain"), "Hidden element is visible: sDbPassPlain");

        $this->click("sDbPassCheckbox");

        $this->assertEquals("on", $this->getValue("sDbPassCheckbox"));
        $this->assertFalse($this->isEditable("sDbPass"), "Hidden element is visible: sDbPass");
        $this->assertTrue($this->isEditable("sDbPassPlain"), "Element not editable: sDbPassPlain");

        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->type("aDB[dbUser]", $user);
        $this->type("sDbPassPlain", $password);
        $this->type("aDB[dbName]", $name);
        $this->assertEquals("localhost", $this->getValue("aDB[dbHost]"));
        $this->type("aDB[dbHost]", $host);
        $this->assertEquals("3306", $this->getValue("aDB[dbPort]"));
        $this->type("aDB[dbPort]", $port);
        $this->selectDemoDataIfExist();
        $this->checkForErrors();

        $this->assertElementPresent("step3Submit");
        $this->click("step3Submit");
        $aMessages = array(
            0 => "Seems there is already OXID eShop installed in database",
            1 => "Please provide necessary data for running OXID eShop"
        );
        $this->waitForText($aMessages, false, 120);
        $this->checkForErrors();

        if ($this->isTextPresent($aMessages[0])) {
            $this->assertElementPresent("step3Continue");
            $this->click("step3Continue");
            $this->waitForText($aMessages[1], false, 120);
            $this->checkForErrors();
        }

        // Step 5
        $this->assertEquals($this->getTestConfig()->getShopUrl(), $this->getValue("aPath[sShopURL]"));
        $this->assertNotEquals("", $this->getValue("aPath[sShopDir]"));
        $this->assertNotEquals("", $this->getValue("aPath[sCompileDir]"));

        $this->type("aAdminData[sLoginName]", "admin@myoxideshop.com");
        $this->type("aAdminData[sPassword]", "admin0303");
        $this->type("aAdminData[sPasswordConfirm]", "admin0303");
        $this->getElement("aSetupConfig[blDelSetupDir]")->setValue(0);
        $this->click("step4Submit");
        $this->waitForText("Check and writing data successful.");
        $this->waitForPageToLoad();
        $this->checkForErrors();

        // Step 6
        // License is only for PE and EE versions. CE is license free
        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            // There is a need to wait 3 seconds. _header.php file has meta tag with page refresh functionality.
            sleep(4);
            $this->assertNotEquals("", $this->getValue("sLicence"));
            $serial = $this->getTestConfig()->getShopSerial();
            if ($serial) {
                $this->type("sLicence", $serial);
            }
            $this->click("step5Submit");
            $this->waitForText("License key successfully saved");
        } else {
            $this->assertTextNotPresent("6. License", "License tab visible in CE");
        }

        // Step 7
        if ($this->isTextPresent("Not Found")) {
            $this->fail("Bug #1538 -> SETUP DIR WAS DELETED BEFORE SETUP FULLY COMPLETED.");
        }
        $this->waitForText("Your OXID eShop has been installed successfully");

        $this->waitForElement("linkToShop");
        $this->assertEquals("To Shop", $this->getText("linkToShop"));
        $this->assertEquals("To admin interface", $this->getText("linkToAdmin"));

        // checking frontend
        $this->openNewWindow($this->getTestConfig()->getShopUrl(), false);
        $this->assertElementNotPresent("link=subshop", "Element should not exist: link=subshop");

        $this->assertHomePageDisplaysCorrectData();

        //checking admin
        $this->openNewWindow($this->getTestConfig()->getShopUrl()."admin", false);
        $this->type("user", "admin@myoxideshop.com");
        $this->type("pwd", "admin0303");
        $this->select("chlanguage", "English");
        $this->select("profile", "Standard");
        $this->clickAndWait("//input[@type='submit']");
        $this->frame("navigation");
        $this->frame("basefrm");
        $this->waitForText("Home");
        $this->assertTextPresent("Welcome to the OXID eShop Admin.", "Missing text: Welcome to the OXID eShop Admin.");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToWelcomeScreenInCaseLicenseIsNotCheckedAsAgreed()
    {
        $this->clearDatabase();
        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(false);
        $this->click(self::DATABASE_INFO_STEP);

        $this->waitForText("Setup has been cancelled because you didn't accept the license conditions.");
        $this->waitForText("Welcome to installation wizard of OXID eShop");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDatabaseEntryPageWhenNotAllFieldsAreFilled()
    {
        $this->clearDatabase();
        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->click(self::DIRECTORY_LOGIN_STEP);

        $this->waitForText("ERROR: Please fill in all needed fields!");
        $this->waitForText("Database is going to be created and needed tables are written. Please provide some information:");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDatabaseEntryPageWhenDatabaseUserDoesNotHaveAccess()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, 'test', 'test', 'test');
        $this->click(self::DIRECTORY_LOGIN_STEP);

        $this->waitForText("ERROR: No database connection possible!");
        $this->assertTextPresent("Access denied for user");

        $this->waitForText("Database is going to be created and needed tables are written. Please provide some information:");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDatabaseEntryPageWhenDatabaseUserIsValidButCantCreateDatabase()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        if ($user === 'root') {
            $this->markTestSkipped('Unable to reuse this test with root user as it can create any database.');
        }

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, 'test', $user, $password);
        $this->click(self::DIRECTORY_LOGIN_STEP);

        $this->assertTextPresent("ERROR: Database not available and also cannot be created! - ERROR: Issue while inserting this SQL statements: ( CREATE DATABASE `test` CHARACTER SET utf8 COLLATE utf8_general_ci; ): SQLSTATE[42000]: Syntax error or access violation: 1044 Access denied for user '$user'@'$host' to database 'test'");
        $this->assertTextPresent("Database is going to be created and needed tables are written. Please provide some information:");
    }

    /**
     * @group setup
     */
    public function testUserIsNotifiedIfAValidDatabaseAlreadyExistsBeforeTryingToOverwriteIt()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->createEmptyValidOxidEshopDatabase();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->click(self::DIRECTORY_LOGIN_STEP);

        $this->waitForText("ERROR: Seems there is already OXID eShop installed in database $name. Please delete it prior continuing!");
        $this->assertTextPresent("If you want to overwrite all existing data and install anyway click here");
        $this->click("//a[@id='step3Continue']");
        $this->waitForText("Please provide necessary data for running OXID eShop:");
    }

    /**
     * @group setup
     */
    public function testSetupHaveDisabledDemodataCheckboxIfDemodataPackageNotInstalled()
    {
        $this->skipOnInstalledDemodata();

        $this->clearDatabase();
        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->waitForText("Demo data package not installed.");

        $this->assertElementPresent(
            "//input[@type='radio' and @name='aDB[dbiDemoData]' and @value='1' and @disabled]",
            "Install demodata radio button is not disabled, but it should"
        );

        $this->assertElementPresent(
            "//input[@type='radio' and @name='aDB[dbiDemoData]' and @value='0' and @checked]",
            "Do not Install demodata radio button should be checked"
        );
    }

    /**
     * @group setup
     */
    public function testSetupHaveEnabledDemodataCheckboxIfDemodataPackageInstalled()
    {
        $this->clearDatabase();
        $this->ensureDemodataPackage();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->waitForText("Demodata");
        $this->assertElementNotPresent(
            "//input[@type='radio' and @name='aDB[dbiDemoData]' and @value='1' and @disabled]",
            "Install demodata radio button is disabled, but it should not be"
        );

        $this->assertElementPresent(
            "//input[@type='radio' and @name='aDB[dbiDemoData]' and @value='1' and @checked]",
            "Do not Install demodata radio button is checked, but another should be"
        );

        $this->assertTextNotPresent("Demo data package not installed.");
    }

    /**
     * @param string $setupSqlFile
     *
     * @dataProvider setupSqlFilesProvider
     * @group setup
     *
     * @return boolean|null Used only for early return
     */
    public function testSetupRedirectsToDatabaseEntryPageWhenSetupSqlFileIsMissing($setupSqlFile)
    {
        $this->skipInitialDataSqlCaseIfDemodataPackageIsInUse($setupSqlFile);

        $this->hideSetupSqlFile($setupSqlFile);

        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('test@test.com', '123456');
        $this->click(self::FINISH_CE_STEP);

        if ($setupSqlFile !== self::DATABASE_SCHEMA_SQL_FILENAME) {
            $this->waitForText("ERROR: Issue while inserting this SQL statements:");
        }

        $this->waitForText("ERROR: Cannot open SQL file");
        $this->assertTextPresent("$setupSqlFile!");
    }

    /**
     * @param string $setupSqlFile
     *
     * @dataProvider setupSqlFilesProvider
     * @group setup
     */
    public function testSetupRedirectsToDatabaseEntryPageWhenSetupSqlFileHasSyntaxError($setupSqlFile)
    {
        $this->skipInitialDataSqlCaseIfDemodataPackageIsInUse($setupSqlFile);

        $this->includeSyntaxErrorToSetupSqlFile($setupSqlFile);

        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('test@test.com', '123456');
        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("ERROR: Issue while inserting this SQL statements:");
        $this->assertTextPresent("SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax;");
    }

    public function setupSqlFilesProvider()
    {
        return [
            [self::DATABASE_SCHEMA_SQL_FILENAME],
            [self::INITIAL_DATA_SQL_FILENAME],
        ];
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDirInfoEntryPageWhenNotAllFieldsAreFilled()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("ERROR: Please fill in all needed fields!");
        $this->waitForText("Please provide necessary data for running OXID eShop:");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDirInfoEntryPageWhenPasswordIsTooShort()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('test@test.com', '12345');
        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("Password is too short!");
        $this->waitForText("Please provide necessary data for running OXID eShop:");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDirInfoEntryPageWhenPasswordDoesNotMatch()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('test@test.com', '123456', '123457');
        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("Passwords do not match!");
        $this->waitForText("Please provide necessary data for running OXID eShop:");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDirInfoEntryPageWhenInvalidEmailUsed()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('invalid_email', '123456', '123456');
        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("Please enter a valid e-mail address!");
        $this->waitForText("Please provide necessary data for running OXID eShop:");
    }

    /**
     * @group setup
     */
    public function testSetupRedirectsToDirInfoEntryPageWhenSetupCantFindConfigFile()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopDirectoryParameters(null, '/test/');
        $this->provideEshopLoginParameters('test@test.com', '123456', '123456');
        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("Could not open /test/config.inc.php for reading! Please consult our FAQ, forum or contact OXID Support staff!");
        $this->waitForText("Please provide necessary data for running OXID eShop:");
    }

    /**
     * @group setup
     */
    public function testSetupShowsErrorMessageWhenMigrationFileContainsSyntaxErrors()
    {
        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->createInvalidMigration();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('test@test.com', '123456', '123456');
        $this->click(self::FINISH_CE_STEP);

        $this->waitForText("Error while executing command");
        $this->assertTextPresent("Return code: '0'");
        $this->assertTextPresent("INVALID_SQL_SYNTAX");

        $this->deleteInvalidMigration();
    }

    /**
     * @group setup
     */
    public function testSetupShowsErrorMessageWhenAnInvalidLicenseIsEnteredAndRedirectsToPreviousTab()
    {
        if ($this->getTestConfig()->getShopEdition() === self::CE_EDITION_ID) {
            $this->markTestSkipped('This test is for Professional and Enterprise editions only.');
        }

        $this->clearDatabase();
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->goToSetup();

        $this->selectSetupLanguage();
        $this->clickContinueAndProceedTo(self::WELCOME_STEP);

        $this->selectEshopLanguage();
        $this->clickContinueAndProceedTo(self::LICENSE_STEP);

        $this->selectAgreeWithLicense(true);
        $this->clickContinueAndProceedTo(self::DATABASE_INFO_STEP);

        $this->provideDatabaseParameters($host, $port, $name, $user, $password);
        $this->clickContinueAndProceedTo(self::DIRECTORY_LOGIN_STEP);

        $this->provideEshopLoginParameters('test@test.com', '123456', '123456');
        $this->clickContinueAndProceedTo(self::FINISH_CE_STEP);

        $this->provideLicenseNumber(self::INVALID_LICENSE_SERIAL_NUMBER);
        $this->click(self::FINISH_PE_EE_STEP);

        $this->waitForText("ERROR: Wrong license key!");
        $this->waitForText("Please confirm license key:", self::CLICK_AND_WAIT_TIMEOUT);
    }

    /**
     * Test if System Requirements Page is displayed correctly when all the requirements are met.
     *
     * @group setup
     */
    public function testSystemRequirementsPageCanContinueWithSetup()
    {
        $this->goToSetup();
        $this->assertTextNotPresent(
            "Your system does not fit system requirements",
            "Setup should be able to continue, but system requirements page shows that it can't."
        );
        $this->assertElementPresent(
            "//input[@type='submit' and @id='step0Submit']",
            "Proceed with setup button is not available, but it should."
        );
    }

    /**
     * Test if System Requirements Page has requirement module names translated.
     *
     * @group setup
     */
    public function testSystemRequirementsPageShowsTranslatedModuleNames()
    {
        $this->goToSetup();

        $this->assertSame("Apache mod_rewrite module", $this->getText("//li[@id='mod_rewrite']"));
        $this->assertSame("UTF-8 support", $this->getText("//li[@id='unicode_support']"));
        $this->assertSame("GDlib v2 incl. JPEG support", $this->getText("//li[@id='gd_info']"));
    }

    /**
     * Test if System Requirements Page has requirement module group names translated.
     *
     * @group setup
     */
    public function testSystemRequirementsPageShowsTranslatedModuleGroupNames()
    {
        $this->goToSetup();

        $this->assertContains("Server configuration", $this->getText("//li[@class='group'][1]"));
        $this->assertContains("PHP configuration", $this->getText("//li[@class='group'][2]"));
        $this->assertContains("PHP extensions", $this->getText("//li[@class='group'][3]"));
    }

    /**
     * Test if System Requirements Page has requirement module state html class names correctly converted.
     *
     * @group setup
     */
    public function testSystemRequirementsContainsProperModuleStateHtmlClassNames()
    {
        $this->hideHtaccessFile();

        $this->goToSetup();

        $this->assertElementPresent("//li[@id='unicode_support' and @class='pass']");
        $this->assertElementPresent("//li[@id='mod_rewrite' and @class='fail']");
    }

    /**
     * Test htaccess exceptional case for system requirements in setup page
     *
     * @group setup
     */
    public function testInstallShopCantContinueDueToHtaccessProblem()
    {
        $this->goToSetup();
        $this->assertTextNotPresent(
            "Your system does not fit system requirements",
            "Setup should be able to continue, but system requirements page shows that it can't."
        );
        $this->assertElementPresent(
            "//li[@id='mod_rewrite' and @class='pass']",
            "Mod rewrite check does not have 'pass' class attribute, but it should."
        );

        $this->hideHtaccessFile();
        $this->goToSetup();
        $this->assertTextPresent(
            "Your system does not fit system requirements",
            "Setup should not be able to continue, but system requirements page shows that it can."
        );
        $this->assertElementPresent(
            "//li[@id='mod_rewrite' and @class='fail']",
            "Mod rewrite check does not have 'fail' class attribute, but it should."
        );
    }

    /**
     * Check if shop automatically redirects to setup when you're trying to set it up for the first time
     */
    public function goToSetup()
    {
        if (!$this->isPackage()) {
            $sUrl = $this->getTestConfig()->getShopUrl() . 'Setup/index.php?istep=100';
            $this->openNewWindow($sUrl, false);
            return;
        }

        if (!file_exists($this->getTestConfig()->getShopPath() . '/Setup/index.php')) {
            $this->fail('Setup directory was already most likely deleted thus making this test invalid');
        }
        $sPath = $this->getTestConfig()->getShopPath() . "/config.inc.php";
        if (!is_writable($sPath)) {
            $this->fail("$sPath has to have writing permissions in order for this test to work");
        }

        $sOldConfigFile = file_get_contents($sPath);
        $sSearchPattern = '/(.*\$this-\>(dbHost|dbPort|dbName|dbUser|dbPwd)\s*=).*/';
        $sReplacePattern = "\\1 '<\\2>';";
        $sConfigFile = preg_replace($sSearchPattern, $sReplacePattern, $sOldConfigFile);
        file_put_contents($sPath, $sConfigFile);

        try {
            $this->openNewWindow($this->getTestConfig()->getShopUrl(), false);
            file_put_contents($sPath, $sOldConfigFile);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ConnectionException $e) {
            // restoring config file no matter what
            file_put_contents($sPath, $sOldConfigFile);
            $this->fail("shop threw exception: " . $e->getTraceAsString());
        }
    }

    private function createEmptyValidOxidEshopDatabase()
    {
        $this->executeDatabaseSqlQuery('CREATE TABLE `oxconfig` (`test` int NOT NULL)');
    }

    private function clearDatabase()
    {
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $this->executeDatabaseSqlQuery("DROP DATABASE IF EXISTS `$name`", false);
        $this->executeDatabaseSqlQuery("CREATE DATABASE `$name`", false);
    }

    private function executeDatabaseSqlQuery($query, $useDatabase = true)
    {
        list($host, $port, $name, $user, $password) = $this->getDatabaseParameters();

        $dsn = "mysql:host=$host";
        if ($useDatabase) {
            $dsn .= ";dbname=$name";
        }

        $pdo = new \PDO($dsn, $user, $password);

        $pdo->exec($query);
    }

    /**
     * @return bool
     */
    protected function isPackage()
    {
        $fileName = $this->getPathFromArray(
            [
                $this->getTestConfig()->getShopPath(),
                self::PACKAGE_INDICATOR_FILENAME
            ]
        );

        return file_exists($fileName);
    }

    private function getPathFromArray($pathElements)
    {
        return implode(DIRECTORY_SEPARATOR, $pathElements);
    }

    private function getShopPath()
    {
        return $this->getTestConfig()->getShopPath();
    }

    private function getShopSetupPath()
    {
        return $this->getPathFromArray([$this->getShopPath(), self::SETUP_DIRECTORY]);
    }

    private function getShopSetupPathBasedOnEdition()
    {
        return $this->getPathFromArray(
            [
                $this->getComposerVendorPath(),
                self::OXID_ESALES_VENDOR_DIRECTORY,
                $this->getShopDirectoryFromComposerVendorPath(),
                $this->getTestConfig()->getShopEdition() === self::CE_EDITION_ID ? self::SOURCE_DIRECTORY : null,
                self::SETUP_DIRECTORY,
            ]
        );
    }

    private function getShopDirectoryFromComposerVendorPath()
    {
        $edition = $this->getTestConfig()->getShopEdition();

        return sprintf(self::SHOP_DIRECTORY_FROM_COMPOSER_VENDOR_PATH, strtolower($edition));
    }

    private function getComposerVendorPath()
    {
        return $this->getPathFromArray([$this->getShopPath(), '..', self::VENDOR_DIRECTORY]);
    }

    private function resetShop()
    {
        $testConfig = new TestConfig();
        $serviceCaller = new ServiceCaller($testConfig);
        $serviceCaller->setParameter('serial', $testConfig->getShopSerial());
        $serviceCaller->setParameter('addDemoData', 1);
        $serviceCaller->setParameter('turnOnVarnish', $testConfig->shouldEnableVarnish());
        $serviceCaller->setParameter('setupPath', $testConfig->getShopSetupPath());
        $serviceCaller->callService('ShopInstaller');
    }

    private function getDatabaseMigrationExecutableFilePath()
    {
        return $this->getComposerVendorBinaryFile(self::DB_MIGRATE_SCRIPT_FILENAME);
    }

    private function getDatabaseViewRegenerationExecutableFilePath()
    {
        return $this->getComposerVendorBinaryFile(self::DB_VIEWS_REGENERATE_SCRIPT_FILENAME);
    }

    private function getComposerVendorBinaryFile($filename)
    {
        return $this->getPathFromArray([$this->getComposerVendorPath(), self::VENDOR_BIN_DIRECTORY, $filename]);
    }

    private function hideDatabaseMigrationExecutableFile()
    {
        $this->hideFile($this->getDatabaseMigrationExecutableFilePath());
    }

    private function showDatabaseMigrationExecutableFile()
    {
        $this->showFile($this->getDatabaseMigrationExecutableFilePath());
    }

    private function hideDatabaseViewRegenerationExecutableFile()
    {
        $this->hideFile($this->getDatabaseViewRegenerationExecutableFilePath());
    }

    private function showDatabaseViewRegenerationExecutableFile()
    {
        $this->showFile($this->getDatabaseViewRegenerationExecutableFilePath());
    }

    /**
     * @return string
     */
    private function getHtaccessFilePath()
    {
        return $this->getPathFromArray([$this->getShopPath(), self::HTACCESS_FILENAME]);
    }

    private function hideHtaccessFile()
    {
        $this->hideFile($this->getHtaccessFilePath());
    }

    private function showHtaccessFile()
    {
        $this->showFile($this->getHtaccessFilePath());
    }

    private function getSetupSqlFilePathBasedOnSourceDirectory($sqlFileName)
    {
        return $this->getPathFromArray(
            [$this->getShopSetupPath(), self::SETUP_SQL_DIRECTORY, $sqlFileName]
        );
    }

    private function getSetupSqlFilePathBasedOnVendorDirectory($sqlFileName)
    {
        return $this->getPathFromArray(
            [$this->getShopSetupPathBasedOnEdition(), self::SETUP_SQL_DIRECTORY, $sqlFileName]
        );
    }

    private function getSetupSqlFilePathBasedOnDemodataPackageDirectory($sqlFileName)
    {
        $edition = $this->getTestConfig()->getShopEdition();

        return $this->getPathFromArray(
            [
                $this->getComposerVendorPath(),
                self::OXID_ESALES_VENDOR_DIRECTORY,
                sprintf(self::DEMODATA_DIRECTORY_FROM_COMPOSER_VENDOR_PATH, strtolower($edition)),
                self::DEMODATA_SOURCE_DIRECTORY,
                $sqlFileName
            ]
        );
    }

    private function hideSetupSqlFile($sqlFileName)
    {
        foreach ($this->iterateThroughAllSetupSqlFilePaths($sqlFileName) as $sqlFileNameWithPath) {
            $this->hideFile($sqlFileNameWithPath);
        }
    }

    private function showSetupSqlFile($sqlFileName)
    {
        foreach ($this->iterateThroughAllSetupSqlFilePaths($sqlFileName) as $sqlFileNameWithPath) {
            $this->showFile($sqlFileNameWithPath);
        }
    }

    private function isDemodataSqlFileFromDemodataPackageAvailable()
    {
        return file_exists(
            $this->getSetupSqlFilePathBasedOnDemodataPackageDirectory(self::DEMODATA_SQL_FILENAME)
        );
    }

    private function getHiddenFilePath($filePath)
    {
        return $filePath . '_';
    }

    private function includeSyntaxErrorToSetupSqlFile($sqlFileName)
    {
        foreach ($this->iterateThroughAllSetupSqlFilePaths($sqlFileName) as $sqlFileNameWithPath) {
            $this->includeSyntaxErrorToFile($sqlFileNameWithPath);
        }
    }

    private function excludeSyntaxErrorFromSetupSqlFile($sqlFileName)
    {
        foreach ($this->iterateThroughAllSetupSqlFilePaths($sqlFileName) as $sqlFileNameWithPath) {
            $this->excludeSyntaxErrorFromFile($sqlFileNameWithPath);
        }
    }

    private function iterateThroughAllSetupSqlFilePaths($sqlFileName)
    {
        yield $this->getSetupSqlFilePathBasedOnSourceDirectory($sqlFileName);
        yield $this->getSetupSqlFilePathBasedOnVendorDirectory($sqlFileName);

        if ($sqlFileName === self::DEMODATA_SQL_FILENAME) {
            yield $this->getSetupSqlFilePathBasedOnDemodataPackageDirectory($sqlFileName);
        }
    }

    private function includeSyntaxErrorToFile($filePath)
    {
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);

            if (strpos($contents, self::SYNTAX_ERROR_STRING) !== 0) {
                $contents = self::SYNTAX_ERROR_STRING . $contents;

                file_put_contents($filePath, $contents);
            }
        }
    }

    private function excludeSyntaxErrorFromFile($filePath)
    {
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);

            if (strpos($contents, self::SYNTAX_ERROR_STRING) === 0) {
                $contents = substr($contents, strlen(self::SYNTAX_ERROR_STRING));

                file_put_contents($filePath, $contents);
            }
        }
    }

    private function hideFile($filePath)
    {
        $hiddenFilePath = $this->getHiddenFilePath($filePath);

        if (file_exists($filePath)) {
            rename($filePath, $hiddenFilePath);
        }
    }

    private function showFile($filePath)
    {
        $hiddenFilePath = $this->getHiddenFilePath($filePath);

        if (file_exists($hiddenFilePath)) {
            rename($hiddenFilePath, $filePath);
        }
    }

    private function restoreModifiedFiles()
    {
        $this->showHtaccessFile();
        $this->showDatabaseMigrationExecutableFile();
        $this->showDatabaseViewRegenerationExecutableFile();

        $sqlFiles = $this->setupSqlFilesProvider();
        foreach ($sqlFiles as $sqlFilesArgumentList) {
            $sqlFileName = $sqlFilesArgumentList[0];
            $this->showSetupSqlFile($sqlFileName);
            $this->excludeSyntaxErrorFromSetupSqlFile($sqlFileName);
        }

        $this->deleteInvalidMigration();
        $this->restoreViewRegenerationBinaryFile();
    }

    private function getDatabaseParameters()
    {
        $config = Registry::getConfig();

        $host = $config->getConfigParam('dbHost');
        $port = $config->getConfigParam('dbPort');
        $name = $config->getConfigParam('dbName');
        $user = $config->getConfigParam('dbUser');
        $password = $config->getConfigParam('dbPwd');

        return [$host, $port, $name, $user, $password];
    }

    private function provideDatabaseParameters($host, $port, $name, $user, $password)
    {
        $this->type("//input[@name='aDB[dbHost]']", $host);
        $this->type("//input[@name='aDB[dbPort]']", $port);
        $this->type("//input[@name='aDB[dbName]']", $name);
        $this->type("//input[@name='aDB[dbUser]']", $user);
        $this->type("//input[@name='aDB[dbPwd]']", $password);
    }

    private function clickContinueAndProceedTo($stepId)
    {
        $this->clickAndWait($stepId, self::CLICK_AND_WAIT_TIMEOUT);
    }

    private function selectSetupLanguage()
    {
        $this->select("setup_lang", "English");
    }

    private function selectEshopLanguage()
    {
        $this->select("sShopLang", "English");
        $this->select("country_lang", "Germany");
    }

    private function selectAgreeWithLicense($isAgreed)
    {
        $optionValue = (int)$isAgreed;
        $this->click("//input[@name='iEula' and @value='$optionValue']");
    }

    private function provideEshopLoginParameters($adminEmail, $adminPassword, $passwordConfirmation = null)
    {
        $this->type("//input[@name='aAdminData[sLoginName]']", $adminEmail);
        $this->type("//input[@name='aAdminData[sPassword]']", $adminPassword);
        $this->type(
            "//input[@name='aAdminData[sPasswordConfirm]']",
            $passwordConfirmation ? $passwordConfirmation : $adminPassword
        );
    }

    private function provideEshopDirectoryParameters($shopUrl = null, $sourcePath = null, $temporaryPath = null)
    {
        if ($shopUrl) {
            $this->type("//input[@name='aPath[sShopURL]']", $sourcePath);
        }
        if ($sourcePath) {
            $this->type("//input[@name='aPath[sShopDir]']", $sourcePath);
        }
        if ($temporaryPath) {
            $this->type("//input[@name='aPath[sCompileDir]']", $temporaryPath);
        }
    }

    private function provideLicenseNumber($licenseNumber)
    {
        $this->waitForElement("//input[@name='sLicence']");
        $this->type("//input[@name='sLicence']", $licenseNumber);
    }

    private function createInvalidMigration()
    {
        $contents = <<<'EOL'
<?php
namespace OxidEsales\EshopCommunity\Migrations;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20170101 extends AbstractMigration {
public function up(Schema $schema) {$this->addSql('INVALID_SQL_SYNTAX');}
public function down(Schema $schema) {}
}
EOL;
        file_put_contents($this->getInvalidMigrationFilePath(), $contents);
    }

    private function deleteInvalidMigration()
    {
        $fileNamePath = $this->getInvalidMigrationFilePath();
        if (file_exists($fileNamePath)) {
            unlink($fileNamePath);
        }
    }

    private function getInvalidMigrationFilePath()
    {
        return $this->getPathFromArray(
            [
                $this->getShopPath(),
                self::MIGRATION_DIRECTORY,
                self::MIGRATION_DATA_DIRECTORY,
                self::INVALID_MIGRATION_FILENAME,
            ]
        );
    }

    private function modifyViewRegenerationToReturnBadReturnCode()
    {
        $viewRegenerationBinaryFilePath = $this->getDatabaseViewRegenerationExecutableFilePath();

        $fakeViewRegenerationBinaryContents = $this->getFakeViewRegenerationBinary();

        if (file_get_contents($viewRegenerationBinaryFilePath) !== $fakeViewRegenerationBinaryContents) {
            $this->hideFile($viewRegenerationBinaryFilePath);
            file_put_contents($viewRegenerationBinaryFilePath, $fakeViewRegenerationBinaryContents);
            chmod($viewRegenerationBinaryFilePath, 0755);
        }
    }

    private function restoreViewRegenerationBinaryFile()
    {
        $viewRegenerationBinaryFilePath = $this->getDatabaseViewRegenerationExecutableFilePath();

        if (file_get_contents($viewRegenerationBinaryFilePath) === $this->getFakeViewRegenerationBinary()) {
            unlink($viewRegenerationBinaryFilePath);
            $this->showFile($viewRegenerationBinaryFilePath);
        }
    }

    private function getFakeViewRegenerationBinary()
    {
        $content = <<<SCRIPT
#!/bin/bash

false
SCRIPT;

        return $content;
    }

    private function skipInitialDataSqlCaseIfDemodataPackageIsInUse($setupSqlFile)
    {
        if (($setupSqlFile === self::INITIAL_DATA_SQL_FILENAME) && ($this->isDemodataSqlFileFromDemodataPackageAvailable())) {
            $this->markTestSkipped(
                self::INITIAL_DATA_SQL_FILENAME . " file is not available nor used if 'demodata' package is present."
            );
        }
    }

    private function skipOnInstalledDemodata()
    {
        if ($this->checkDemodataPackageExists()) {
            $this->markTestSkipped("The test checks the workflow with no demodata package");
        }
    }

    private function checkDemodataPackageExists()
    {
        $utilities = new Utilities();
        return is_dir($utilities->getActiveEditionDemodataPackagePath());
    }

    /**
     * If demodata is not installed, creates imitation of demodata package (without composer.json file)
     */
    private function ensureDemodataPackage()
    {
        $packageExists = $this->checkDemodataPackageExists();
        if (!$packageExists) {
            $this->createDemodataPackageImitation();
        }
    }

    /**
     * Creates very basic structure of demodata package with empty files
     */
    private function createDemodataPackageImitation()
    {
        $packagePaths = $this->getDemodataPackageImitationPaths();

        mkdir($packagePaths['package'], 0777, true);
        mkdir($packagePaths['source'], 0777, true);
        file_put_contents($packagePaths['demodata'], "");
    }

    /**
     * Removes the imitation of demodata package created by method createDemodataPackageImitation
     */
    private function cleanupDemodataPackageImitation()
    {
        $packagePaths = $this->getDemodataPackageImitationPaths();

        if (is_dir($packagePaths['package']) && !is_file($packagePaths['composer'])) {
            unlink($packagePaths['demodata']);
            rmdir($packagePaths['source']);
            rmdir($packagePaths['package']);
        }
    }

    /**
     * Returns paths of virtual demodata package
     *
     * @return array ['package', 'composer', 'demodata', 'source']
     */
    private function getDemodataPackageImitationPaths()
    {
        $utilities = new Utilities();
        $directory = $utilities->getActiveEditionDemodataPackagePath();

        return [
            'package' => $directory,
            'composer' => $directory . DIRECTORY_SEPARATOR . 'composer.json',
            'demodata' => implode(DIRECTORY_SEPARATOR, [
                $directory,
                $utilities::DEMODATA_PACKAGE_SOURCE_DIRECTORY,
                $utilities::DEMODATA_SQL_FILENAME
            ]),
            'source' => $directory . DIRECTORY_SEPARATOR . $utilities::DEMODATA_PACKAGE_SOURCE_DIRECTORY
        ];
    }

    /**
     * Demo data choice is only available when the file exists.
     * Choose demo data if possible, use test data otherwise.
     */
    private function selectDemoDataIfExist()
    {
        if ($this->checkDemodataPackageExists()) {
            $this->assertEquals(1, $this->getValue("aDB[dbiDemoData]"));
            $this->check("aDB[dbiDemoData]");
        } else {
            $this->assertEquals(0, $this->getValue("aDB[dbiDemoData]"));
            $this->assertTextPresent('Demo data package not installed');
        }
    }

    /**
     * Home page will display different data dependent on testing environment:
     * - International will use English language by default.
     * - Not international will use German language by default.
     * - Azure Theme is active by default wit test data.
     * - Flow Theme is active by default wit test data.
     */
    private function assertHomePageDisplaysCorrectData()
    {
        if ($this->checkDemodataPackageExists()) {
            $this->assertHomePageDisplaysFlowTheme();
        } else {
            $this->assertHomePageDisplaysAzureTheme();
        }
    }

    private function assertHomePageDisplaysFlowTheme()
    {
        if (getenv('OXID_LOCALE') == 'international') {
            $this->assertTextPresent("Just arrived");
            $this->assertTextNotPresent("Frisch eingetroffen");
        } else {
            $this->assertTextPresent("Frisch eingetroffen");
            $this->assertTextNotPresent("Just arrived");
        }
    }

    private function assertHomePageDisplaysAzureTheme()
    {
        if (getenv('OXID_LOCALE') == 'international') {
            $this->assertTextPresent("Home");
            $this->assertTextNotPresent("Startseite");
        } else {
            $this->assertTextPresent("Startseite");
            $this->assertTextNotPresent("Home");
        }
    }
}
