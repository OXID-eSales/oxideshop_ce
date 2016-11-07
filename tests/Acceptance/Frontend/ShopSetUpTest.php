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

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use Exception;
use oxConnectionException;
use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;
use OxidEsales\TestingLibrary\ServiceCaller;
use oxRegistry;

/** Selenium tests for frontend navigation. */
class ShopSetUpTest extends FrontendTestCase
{
    /** @var int How much more time wait for these tests. */
    protected $_iWaitTimeMultiplier = 7;

    /**
     * Regenerate views after test.
     */
    protected function tearDown()
    {
        parent::tearDown();

        $oServiceCaller = new ServiceCaller($this->getTestConfig());
        $oServiceCaller->callService('ViewsGenerator', 1);
    }

    /**
     * Tests installation of new shop version (setup)
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
        $this->select("setup_lang", "label=English");
        $this->assertEquals("English", $this->getSelectedLabel("setup_lang"));
        $this->clickAndWait("step0Submit", 2);

        // Step 2
        $this->assertTextPresent("Welcome to OXID eShop installation wizard");
        $this->assertElementPresent("location_lang");
        $this->assertEquals("Please choose Germany, Austria, SwitzerlandAny other", trim(preg_replace("/[ \r\n]*[\r\n][ \r\n]*/", ' ', $this->getText("location_lang"))));
        $this->assertElementPresent("check_for_updates_ckbox");
        $this->assertEquals("off", $this->getValue("check_for_updates_ckbox"));

        $this->check("check_for_updates_ckbox");

        if (getenv('OXID_LOCALE') == 'international') {
            $this->select("location_lang", "Any other");
            $this->assertEquals("Any other", $this->getSelectedLabel("location_lang"));
            $this->assertElementPresent("sShopLang");
            $this->select("sShopLang", "label=English");
        } else {
            $this->select("location_lang", "Germany, Austria, Switzerland");
            $this->assertEquals("Germany, Austria, Switzerland", $this->getSelectedLabel("location_lang"));
            $this->assertElementPresent("sShopLang");
            $this->select("sShopLang", "label=Deutsch");
        }

        $this->assertElementPresent("country_lang");
        $this->select("country_lang", "label=Germany");
        $this->checkForErrors();

        if ($this->getTestConfig()->getShopEdition() === 'PE' && getenv('OXID_LOCALE') == 'germany') {
            //there is no such checkbox for EE or utf mode
            $this->assertElementPresent("use_dynamic_pages_ckbox");
            $this->assertElementVisible("use_dynamic_pages_ckbox");
            $this->assertEquals("off", $this->getValue("use_dynamic_pages_ckbox"));
            $this->check("use_dynamic_pages_ckbox");
            $this->assertEquals("on", $this->getValue("use_dynamic_pages_ckbox"));
            $this->checkForErrors();
        }
        $this->clickAndWait("step1Submit", 2);

        // Step 3
        $this->assertElementPresent("iEula");
        $this->check("iEula");
        $this->checkForErrors();
        $this->clickAndWait("step2Submit", 2);

        // Step 4
        $this->assertEquals("off", $this->getValue("sDbPassCheckbox"));
        $this->assertTrue($this->isEditable("sDbPass"), "Element not editable: sDbPass");
        $this->assertFalse($this->isEditable("sDbPassPlain"), "Hidden element is visible: sDbPassPlain");

        $this->click("sDbPassCheckbox");

        $this->assertEquals("on", $this->getValue("sDbPassCheckbox"));
        $this->assertFalse($this->isEditable("sDbPass"), "Hidden element is visible: sDbPass");
        $this->assertTrue($this->isEditable("sDbPassPlain"), "Element not editable: sDbPassPlain");

        $dbName = oxRegistry::getConfig()->getConfigParam('dbName');
        $dbUser = oxRegistry::getConfig()->getConfigParam('dbUser');
        $dbPwd =  oxRegistry::getConfig()->getConfigParam('dbPwd');
        $dbHost = oxRegistry::getConfig()->getConfigParam('dbHost');

        $this->type("aDB[dbUser]", $dbUser);
        $this->type("sDbPassPlain", $dbPwd);
        $this->type("aDB[dbName]", $dbName);
        $this->assertEquals("localhost", $this->getValue("aDB[dbHost]"));
        $this->type("aDB[dbHost]", $dbHost);
        $this->assertEquals(1, $this->getValue("aDB[dbiDemoData]"));
        $this->check("aDB[dbiDemoData]");
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

        if (getenv('OXID_LOCALE') == 'international') {
            $this->assertTextPresent("Just arrived");
            $this->assertTextNotPresent("Frisch eingetroffen");
        } else {
            $this->assertTextPresent("Frisch eingetroffen");
            $this->assertTextNotPresent("Just arrived");
        }

        //checking admin
        $this->openNewWindow($this->getTestConfig()->getShopUrl()."admin", false);
        $this->type("user", "admin@myoxideshop.com");
        $this->type("pwd", "admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']");
        $this->frame("navigation");
        $this->frame("basefrm");
        $this->waitForText("Home");
        $this->assertTextPresent("Welcome to the OXID eShop Admin.", "Missing text: Welcome to the OXID eShop Admin.");
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
        $sSearchPattern = '/(.*\$this-\>(dbHost|dbName|dbUser|dbPwd)\s*=).*/';
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

    /**
     * @throws Exception
     */
    private function clearDatabase()
    {
        $dbName = oxRegistry::getConfig()->getConfigParam('dbName');
        $dbUser = oxRegistry::getConfig()->getConfigParam('dbUser');
        $dbPwd =  oxRegistry::getConfig()->getConfigParam('dbPwd');
        $dbHost = oxRegistry::getConfig()->getConfigParam('dbHost');

        $command = "mysql -h'$dbHost' -u'$dbUser' -p'$dbPwd' -e 'DROP DATABASE IF EXISTS `$dbName`' ; "
                 . "mysql -h'$dbHost' -u'$dbUser' -p'$dbPwd' -e 'CREATE DATABASE `$dbName`'; ";

        exec($command, $response, $returnCode);

        if ($returnCode > 0) {
            throw new Exception("Error when creating database for testing: " . implode("\n", $response));
        }
    }

    /**
     * @return bool
     */
    protected function isPackage()
    {
        return file_exists($this->getTestConfig()->getShopPath() . '/pkg.info');
    }
}
