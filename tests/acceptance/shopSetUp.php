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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'oxidAdditionalSeleniumFunctions.php';


class shopSetUp extends oxidAdditionalSeleniumFunctions
{
    protected function setUp($skipDemoData=false)
    {
        parent::setUp(true);
    }

/*
 * -------------------------- Selenium tests for frontend navigation---- -----------------------
 */

    /**
     * installs new shop version (setup)
     * @group main
     */
    public function testInstallShop()
    {
        echo shopURL."   Installing fresh shop version: ";
        $dbName = oxConfig::getInstance()->getConfigParam( 'dbName' );
        $dbUser = oxConfig::getInstance()->getConfigParam( 'dbUser' );
        $dbPwd =  oxConfig::getInstance()->getConfigParam( 'dbPwd' );
        $dbHost = oxConfig::getInstance()->getConfigParam( 'dbHost' );
        $skipLastStep = false;

        $sCmd = "echo 'drop database if exists `$dbName`' | mysql -h'$dbHost' -u'$dbUser' -p'$dbPwd' ; "
              ."echo 'create database `$dbName`' | mysql -h'$dbHost' -u'$dbUser' -p'$dbPwd' ; ";
        exec($sCmd, $sOut, $ret);
        $sOut = implode("\n",$sOut);
        if ( $ret > 0 ) {
            throw new Exception( $sOut );
        }

        //installing shop
        //step 1
        try {
            $this->selectWindow(null);
            $this->windowMaximize(null);
            if ((getenv('OXID_TEST_EFIRE')) || (getenv('OXID_TEST_DO_SETUP')))  {
                $this->open(shopURL."setup/");
            } else {
                $this->open(shopURL."setup/index.php?istep=100");
                //$this->open(shopURL);
            }
            $this->checkForErrors();
            $this->assertTrue($this->isTextPresent("Checking if your system fits the requirements"), "Text not found: Checking if your system fits the requirements ");
            $this->assertTrue($this->isElementPresent("setup_lang"));
            $this->assertEquals("English Deutsch", trim(preg_replace("/[ \r\n]*[\r\n][ \r\n]*/", ' ', $this->getText("setup_lang"))));
            $this->select("setup_lang", "label=English");
            $this->assertEquals("English", $this->getSelectedLabel("setup_lang"));
            $this->clickAndWait("step0Submit");
        } catch (Exception $e) {
            $this->stopTesting("Failed to complete OXID eShop setup step 1! Reason: ".$e->getMessage(), $e);
        }
        //step 2
        try {
            $this->assertTrue($this->isTextPresent("Welcome to OXID eShop installation wizard"), "Text not found: Welcome to OXID eShop installation wizard ");
            $this->assertTrue($this->isElementPresent("location_lang"));
            $this->assertEquals("Please choose Germany, Austria, SwitzerlandAny other", trim(preg_replace("/[ \r\n]*[\r\n][ \r\n]*/", ' ', $this->getText("location_lang"))));
            $this->assertTrue($this->isElementPresent("check_for_updates_ckbox"));
            $this->assertEquals("off", $this->getValue("check_for_updates_ckbox"));
            $this->check("check_for_updates_ckbox");
            if (getenv('OXID_LOCALE') == 'international') {
                echo "locale - international   ";
                $this->select("location_lang", "Any other");
                $this->assertEquals("Any other", $this->getSelectedLabel("location_lang"));
                $this->assertTrue($this->isElementPresent("sShopLang"));
                $this->select("sShopLang", "label=English");
            } else {
                echo "locale - germany   ";
                $this->select("location_lang", "Germany, Austria, Switzerland");
                $this->assertEquals("Germany, Austria, Switzerland", $this->getSelectedLabel("location_lang"));
                $this->assertTrue($this->isElementPresent("sShopLang"));
                $this->select("sShopLang", "label=Deutsch");
            }
            $this->assertTrue($this->isElementPresent("country_lang"));
            $this->select("country_lang", "label=Germany");
            $this->checkForErrors();
            //there is no such checkbox for EE or utf mode
            if (getenv('OXID_LOCALE') == 'germany') {
                $this->assertTrue($this->isElementPresent("use_dynamic_pages_ckbox"));
                $this->assertTrue($this->isVisible("use_dynamic_pages_ckbox"), "Element not visible: use_dynamic_pages_ckbox");
                $this->assertEquals("off", $this->getValue("use_dynamic_pages_ckbox"));
                $this->check("use_dynamic_pages_ckbox");
                $this->assertEquals("on", $this->getValue("use_dynamic_pages_ckbox"));
                $this->checkForErrors();
            }
            $this->clickAndWait("step1Submit");
        } catch (Exception $e) {
            $this->stopTesting("Failed to complete OXID eShop setup step 2! Reason: ".$e->getMessage(), $e);
        }
        //step 3
        try {
            $this->assertTrue($this->isElementPresent("iEula"));
            $this->check("iEula");
            $this->checkForErrors();
            $this->clickAndWait("step2Submit");
        } catch (Exception $e) {
            $this->stopTesting("Failed to complete OXID eShop setup step 3! Reason: ".$e->getMessage(), $e);
        }
        //step 4
        try {
            $this->assertEquals("off", $this->getValue("sDbPassCheckbox"));
            $this->assertTrue($this->isEditable("sDbPass"), "Element not editable: sDbPass");
            $this->assertFalse($this->isEditable("sDbPassPlain"), "Hidden element is visible: sDbPassPlain");
            $this->click("sDbPassCheckbox");
            $this->assertEquals("on", $this->getValue("sDbPassCheckbox"));
            $this->assertFalse($this->isEditable("sDbPass"), "Hidden element is visible: sDbPass");
            $this->assertTrue($this->isEditable("sDbPassPlain"), "Element not editable: sDbPassPlain");
            $this->type("aDB[dbUser]", $dbUser);
            $this->type("sDbPassPlain", $dbPwd);
            $this->type("aDB[dbName]", $dbName);
            $this->assertEquals("localhost", $this->getValue("aDB[dbHost]"));
            $this->type("aDB[dbHost]", $dbHost);
            $this->assertEquals("on", $this->getValue("aDB[dbiDemoData]"));
            $this->check("aDB[dbiDemoData]");
            $this->check("aDB[iUtfMode]");
            echo " UTF-8 eShop. ";
            $this->checkForErrors();
            $aMessages = array( 0 => "Seems there is already OXID eShop installed in database",
                                1 => "Please provide neccesary data for running OXID eShop");
            $dStartTime = microtime( true );
            $this->assertTrue($this->isElementPresent("step3Submit"));
            $this->click("step3Submit");
            $this->waitForText($aMessages, false, 120);
            $this->checkForErrors();
            if ($this->isTextPresent($aMessages[0])) {
                echo " database $dbName already existed. Overwriting it. ";
                $this->assertTrue($this->isElementPresent("step3Continue"));
                $this->click("step3Continue");
                echo " waiting for text ".$aMessages[1].".";
                $this->waitForText($aMessages[1], false, 120);
                echo " checking page for errors. ";
                $this->checkForErrors();
                $dDiffTime = microtime( true ) - $dStartTime;
                echo " database created successfully (after $dDiffTime).";
            } else {
                echo " database $dbName did not existed. Creating new one. ";
            }
        } catch (Exception $e) {
            $dDiffTime = microtime( true ) - $dStartTime;
            $this->stopTesting("Failed to complete OXID eShop setup step 4 (after $dDiffTime)! Reason: ".$e->getMessage(), $e);
        }
        //step 5
        try {
            $this->assertEquals(shopURL, $this->getValue("aPath[sShopURL]"));
            $this->assertNotEquals("", $this->getValue("aPath[sShopDir]"));
            $this->assertNotEquals("", $this->getValue("aPath[sCompileDir]"));
            $this->type("aAdminData[sLoginName]", "admin@myoxideshop.com");
            $this->type("aAdminData[sPassword]", "admin0303");
            $this->type("aAdminData[sPasswordConfirm]", "admin0303");
            $this->click("step4Submit");
                $this->waitForPageToLoad("60000");
                $this->checkForErrors();
        } catch (Exception $e) {
            $this->stopTesting("Failed to complete OXID eShop setup step 5! Reason: ".$e->getMessage(), $e);
        }
        //step 6
        //license is only for PE and EE versions. CE is license free
            try {
                $this->assertFalse($this->isTextPresent("6. License"), "License tab visible in CE");
            } catch (Exception $e) {
                $this->stopTesting("Failed to complete OXID eShop setup! Reason: License tab is visible for CE");
            }
        //step 7
        try {
            $aMessages = array( 0 => "Your OXID eShop has been installed successfully",
                                1 => "Not Found");
            $this->waitForText($aMessages, true);
            if ($this->isTextPresent($aMessages[0])) {
                $this->checkForErrors();
            } else {
                $this->fail("Bug #1538 -> SETUP DIR WAS DELETED BEFORE SETUP FULLY COMPLETED.");
            }
        } catch (Exception $e) {
                $this->stopTesting("Failed to complete OXID eShop setup! Reason: ".$e->getMessage(), $e);
        }
        if (!$skipLastStep) {
            try {
                $this->waitForElement("linkToShop");
                $this->assertEquals("To Shop", $this->getText("linkToShop"));
                $this->assertEquals("To admin interface", $this->getText("linkToAdmin"));
            } catch (Exception $e) {
                $this->stopTesting("Failed to complete OXID eShop setup final step! Reason: ".$e->getMessage(), $e);
            }
        }

        // set both languages as active after setup
        try {
            //shop was installed in EN lang, so making German language active too
            if (getenv('OXID_LOCALE') == 'germany') {
                //making EN as default lang
                $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'sDefaultLang'");
                $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba832f744c5786a371d9df33778f956ef30fb1e8bb85d97b3b5de43e6bad688dfc6f63a8af34b33290cdd6fc889c8e77cfee0e8a17ade6b94130fda30d062d03e35d8d1bda1c2dc4dd5281fcb1c9538cf114050a3e7118e16151bfe94f5a0706d2eb3d9ff8b4a24f88963788f5dd1c33c573a1ebe3f5b06c072c6a373aaecb11755d907b50a79bbac613054871af686a7d3dbe0b6e1a3e292a109e2f5bc31bcd26ebbe42dac8c9cac3fa53c6fae3c8c7c3c113a4f1a8823d13c78c27dc WHERE `OXVARNAME` = 'aLanguageParams'");
            } else {
                $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba322c77e44ef7ced6aca1f357003cad231d1d78fe80070841979cd58fd7eca88459d4cb9ce3b72a2804d5 WHERE `OXVARNAME` = 'aHomeCountry'");
                $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dbace2972e14bf2cbd3a9a45157004422e928891572b281961cdebd1e0bbafe8b2444b15f2c7b1cfcbe6e5982d874c8c6b455e30cdf65e36448a167b6f12d8d395d21da37ed39df58cff9678dfe90c67404c9b47ba8a91b27a79911522cd97268e07d44c74e55c2000a14b4278f0980d50651b1a01ef3c8e20873c65c59a1df789156d73433c764bdabf718154b6171bdee4d673ed2f56579f1ae659784 WHERE `OXVARNAME` = 'aCurrencies'");
                $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba832f744c5786a371ca8c397d859f64f905bbe2b18fd3713157ee3461a76287f66569a2a53eb9389ac7dcf68296847dc5e404801da7ecb34b3af7a9070c2709e9578711d01627ced7588bf6bbc35986fb1e0f00347b12eb6b26a42b233f6c65fce7d0b39fd3abcfa3a10e7779cbe82026d9ac33e2df16f12df15bf4784793595cbe225432febd18d5555371a8818c95ec5b12bc4b31dffcf54acf93ed5a7d14080ff0d0bf67cc63eb18633c716561822c0ebb029771aca4fd9e8c27dc WHERE `OXVARNAME` = 'aLanguageParams'");
            }

            $this->open(shopURL."_cc.php");
            $this->checkForErrors();
            $this->assertFalse($this->isElementPresent("link=subshop"), "Element should not exist: link=subshop");
            $this->assertTrue($this->isTextPresent("Just arrived!"));
            $this->assertFalse($this->isTextPresent("Frisch eingetroffen!"));
        } catch (Exception $e) {
            $this->stopTesting("Failed to setup languages: ". $e->getMessage() );
        }

        try {
            //checking admin
            $this->selectWindow(null);
            $this->windowMaximize(null);
            $this->selectFrame("relative=top");
            $this->open(shopURL."_cc.php");
            $this->open(shopURL."admin");
            $this->checkForErrors();
            $this->type("user","admin@myoxideshop.com");
            $this->type("pwd","admin0303");
            $this->select("chlanguage", "label=English");
            $this->select("profile", "label=Standard");
            $this->click("//input[@type='submit']");
            $this->waitForElement("nav");
            $this->waitForElement("navigation");
            $this->waitForElement("basefrm");
        } catch (Exception $e) {
            $this->stopTesting("Failed login to admin interface. Frames are not fully loaded. ".$e->getMessage(), $e);
        }
        try {
            $this->selectFrame("relative=top");
            $this->selectFrame("basefrm");
            $this->waitForText("Home");
            $this->assertTrue($this->isTextPresent("Welcome to the OXID eShop Admin."), "Missing text: Welcome to the OXID eShop Admin.");
            $this->checkForErrors();

            // downloading eFire connector
            $sFilter = getenv('TEST_FILE_FILTER');  //for efire tests
            if ($sFilter) {
                $this->executeSql("UPDATE `oxshops` SET `OXVERSION`='4.6.5'");
            }
            switch ($sFilter) {
                case 'factfinder':
                    $this->downloadConnector("factfinder@oxid-esales.com", "factfinder@oxid-esales.com");
                    break;
                case 'webmiles':
                    $this->downloadConnector("pe_webmiles", "pe_webmiles");
                    break;
                case 'paypal':
                    $this->downloadConnector("ee_paypal_demo", "pp6677ggR");
                    break;
                case 'dhl':
                        $this->downloadConnector("Stefan_Werner", "pugapa33");
                    break;
                default:
                    break;
            }

        } catch (Exception $e) {
            $this->stopTesting($e->getMessage());
        }
        try {
            if ( OXID_THEME == 'basic' ) {
                $this->executeSql("DELETE FROM `oxactions2article` WHERE `OXACTIONID` = 'oxcatoffer';");
            }
                $this->addDemoData();
                $this->clearTmp();

            // going to Modules, to activate particular module by pressing "Activate"
            if (getenv('OXID_TEST_EFIRE') && !getenv('SKIP_MODULE_SETUP')) {
                    $this->selectMenu("Extensions", "Modules");
                    switch ($sFilter) {
                        case 'factfinder':
                            $this->openTab("link=FACT-Finder");
                            break;
                        case 'webmiles':
                            $this->openTab("link=webmiles");
                            break;
                        case 'paypal':
                            $this->openTab("link=PayPal");
                             break;
                        case 'dhl':
                            $this->openTab("link=DHL Paket");
                            break;
                        default:
                            break;
                    }
                    $this->frame("edit");
                    $this->clickAndWait("module_activate");
                    // check, if after activation, the button "Deactivate" occured
                    $this->assertTrue($this->isElementPresent("id=module_deactivate"));
                }
            } catch (Exception $e) {
                $this->stopTesting("Failed to insert demodata: ". $e->getMessage() );
        }

        //checking if shop is loading after setup
        try {
            //checking frontend
            $this->selectWindow(null);
            $this->windowMaximize(null);
            $this->selectFrame("relative=top");
            $this->open(shopURL."_cc.php");
            $this->checkForErrors();
            $this->assertFalse($this->isElementPresent("link=subshop"), "Element should not exist: link=subshop");
            $this->assertTrue($this->isTextPresent("Just arrived!"));
            $this->assertFalse($this->isTextPresent("Frisch eingetroffen!"));
            if ( OXID_THEME == 'basic' ) {
                $this->assertTrue($this->isTextPresent("You are here: / Home"), "Wrong home path. Text not found: You are here: / Home");
                $this->assertTrue($this->isTextPresent("Welcome to OXID eShop 4"), "Missing' text: Welcome to OXID eShop 4");
            }
        } catch (Exception $e) {
            $this->stopTesting("Failed to load shop frontend after setup was finished. ".$e->getMessage(), $e);
        }
    }
}
