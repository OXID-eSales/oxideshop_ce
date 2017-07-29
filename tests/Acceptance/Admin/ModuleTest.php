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

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin interface functionality.
 *
 * @group module
 */
class ModuleTest extends ModuleBaseTest
{
    /**
     * Testing modules in vendor directory. Checking when any file with source code class of module is deleted.
     *
     * @group adminFunctionality
     */
    public function testModuleSettings()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped("Test is not for SubShop");
        }

        $this->loginAdmin("Extensions", "Modules");

        // checking If the same class extend two modules
        $this->openListItem("Test module #1");
        $this->openTab("Settings");
        $this->assertTextPresent("Empty Settings Group");
        $this->assertTextPresent("Filled Settings Group");

        // Asserting module settings values when default values are not set
        $this->click("link=Empty Settings Group");

        $this->assertNotChecked("//input[@name='confbools[testEmptyBoolConfig]' and @type='checkbox']");
        $this->assertElementValue("confstrs[testEmptyStrConfig]", '', 'Without default value text input (str) should be empty');
        $this->assertElementValue("confarrs[testEmptyArrConfig]", '', 'Without default value text area array (arr) should be empty');
        $this->assertElementValue("confaarrs[testEmptyAArrConfig]", '', 'Without default value text area assoc array (aarr) should be empty');
        $this->assertElementValue("confselects[testEmptySelectConfig]", '0', 'Without default value first option should be selected for selects');

        $oPassword = $this->getElement("confpassword[testEmptyPasswordConfig]");
        $this->assertEquals('', $oPassword->getValue(), 'Without default value password should be empty');
        $this->assertTrue($oPassword->isVisible(), 'Password confirm field should be visible when default value is not set');

        // Asserting module settings values when default values are set
        $this->click("link=Filled Settings Group");

        $this->assertChecked("//input[@name='confbools[testFilledBoolConfig]' and @type='checkbox']");
        $this->assertElementValue("confstrs[testFilledStrConfig]", 'testStr', 'Default value of text input (str) should be taken from metadata');
        $this->assertElementValue("confarrs[testFilledArrConfig]", "option1\noption2", 'Default value of text area array (arr) should be taken from metadata');
        $this->assertElementValue("confaarrs[testFilledAArrConfig]", "key1 => option1\nkey2 => option2", 'Default value of text area assoc array (aarr) should be taken from metadata');
        $this->assertElementValue("confselects[testFilledSelectConfig]", '2', 'Default value of select should be taken from metadata');

        $oPassword = $this->getElement("confpassword[testFilledPasswordConfig]");
        $this->assertEquals('', $oPassword->getValue(), 'Default value of password should be empty');
        $this->assertFalse($oPassword->isVisible(), 'Password confirm field should be invisible when default value is set');

        // Add some information to the input fields
        $this->check("//input[@name='confbools[testEmptyBoolConfig]' and @type='checkbox']");
        $this->type("confstrs[testEmptyStrConfig]", 'testString');
        $this->type("confarrs[testEmptyArrConfig]", "option1\noption2\noption3");
        $this->type("confaarrs[testEmptyAArrConfig]", "key1 => option1\nkey2 => option2");
        $this->select("confselects[testEmptySelectConfig]", "2");
        $this->type("css=.password_input", "testPassword");
        $this->type("confpassword[testEmptyPasswordConfig]", "testPassword");

        $this->clickAndWait('save');

        // Assert that added information appeared.
        $this->assertChecked("//input[@name='confbools[testEmptyBoolConfig]' and @type='checkbox']");
        $this->assertElementValue("confstrs[testEmptyStrConfig]", 'testString', 'Value for text input (str) should have been saved');
        $this->assertElementValue("confarrs[testEmptyArrConfig]", "option1\noption2\noption3", 'Values for text area array (arr) should have been saved');
        $this->assertElementValue("confaarrs[testEmptyAArrConfig]", "key1 => option1\nkey2 => option2", 'Values for text area assoc array (aarr) should have been saved');
        $this->assertElementValue("confselects[testEmptySelectConfig]", '2', 'Value for select should have been saved');

        $oPassword = $this->getElement("confpassword[testEmptyPasswordConfig]");
        $this->assertEquals('', $oPassword->getValue(), 'With saved value password should be empty');
        $this->assertFalse($oPassword->isVisible(), 'Password confirm field should be invisible when value is saved');
    }

    /**
     * Testing modules in vendor directory. Checking when any file with source code class of module is deleted.
     *
     * @group adminFunctionality
     */
    public function testModulesHandlingExtendingClass()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped("Test is not for SubShop");
        }

        $this->loginAdmin("Extensions", "Modules");

        $this->activateModule("Test module #1");
        $this->activateModule("Test module #2");
        $this->activateModule("Test module #7");
        $this->activateModule("Namespaced module #1");

        //checking if module all entry is displayed
        $this->openTab("Installed Shop Modules");
        $this->assertTextPresent("Drag items to change modules order. After changing order press Save button to save current modules order.");
        $childClassesContainerId = 'OxidEsales---Eshop---Application---Controller---ContentController';
        $namespacedModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='OxidEsales\\EshopCommunity\\Tests\\Acceptance\\Admin\\testData\\modules\\oxid\\namespace1\\Controllers\\ContentController']/span";
        $this->assertEquals(
            'OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\namespace1\Controllers\ContentController',
            $this->getText($namespacedModuleClassElementLocator)
        );

        $test1ModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='test1/controllers/test1content']/span";
        $this->assertEquals('test1/controllers/test1content', $this->getText($test1ModuleClassElementLocator));
        $test2ModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='test2/view/myinfo2']/span";
        $this->assertEquals('test2/view/myinfo2', $this->getText($test2ModuleClassElementLocator));
        $test7ModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='oxid/test7/view/myinfo7']/span";
        $this->assertEquals('oxid/test7/view/myinfo7', $this->getText($test7ModuleClassElementLocator));

        $this->open(shopURL."?cl=content&fnc=showContent");
        $this->assertTextPresent(' + namespace1');

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL . "en/About-Us/");
        $this->assertTextPresent("About Us + info1 + info2 + info7 + namespace1");

        $this->deleteModuleClass();

        $this->loginAdmin("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("Problematic Files");
        $this->assertTextPresent('test1/controllers/test1content');
        $this->clickAndWait("yesButton");

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL . "en/About-Us/");
        $this->assertTextPresent("About Us + info2 + info7 + namespace1");
        $this->assertTextNotPresent("About Us + info1 + info2 + info7 + namespace1");
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleList::_getInvalidExtensions()
     */
    public function testGetDeletedExtensionsForNamespaceModuleShowErrorForNonLoadableClasses()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for SubShop');
        }

        $this->loginAdmin('Extensions', 'Modules');
        /** The metadata.php of module 'Invalid Namespaced Module #1' refers to 2 non loadable classes */
        $this->activateModule('Invalid Namespaced Module #1');

        //checking if all expected non loadable classes are displayed
        $this->openTab("Installed Shop Modules");
        $this->assertTextPresent("Problematic Files");
        $this->assertTextPresent('NonExistentFile', 'The class name of a non existing class file should be listed in the section "Problematic Files"');

        //checking if clicking "Yes" fixes the problematic files
        $this->clickAndWait("yesButton");
        $this->openTab("Installed Shop Modules");
        $this->assertTextNotPresent("Problematic Files");
        $this->assertTextNotPresent('NonExistentClass');
        $this->assertTextNotPresent('NonExistentFile');
    }

    /**
     * Test, that the module deactivation works in the non demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleActivationWorksInNormalMode()
    {
        $this->loginAdmin("Extensions", "Modules");
        $this->openListItem("Test module #6 (in vendor dir)");
        $this->assertActivationButtonIsPresent();
        $this->assertDeactivationButtonIsNotPresent();
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertDeactivationButtonIsPresent();
        $this->assertActivationButtonIsNotPresent();
    }

    /**
     * Test, that the module deactivation works in the non demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleDeactivationWorksInNormalMode()
    {
        $this->testModuleActivationWorksInNormalMode();

        $this->clickAndWait("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertActivationButtonIsPresent();
        $this->assertDeactivationButtonIsNotPresent();
    }

    /**
     * Test, that the module activation won't work in the demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleActivationIsSwitchedOffInDemoMode()
    {
        $this->loginAdmin("Extensions", "Modules");
        $this->switchToDemoMode();

        $this->openListItem("Test module #6 (in vendor dir)");
        $this->assertActivationButtonIsNotPresent();
        $this->assertDeactivationButtonIsNotPresent();
        $this->assertTextPresent('Please note: modules can\'t be activated or deactivated in demo shop mode.', "N");
    }

    /**
     * Test, that the module deactivation won't work in the demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleDeactivationIsSwitchedOffInDemoMode()
    {
        $this->testModuleActivationWorksInNormalMode();
        $this->switchToDemoMode();

        $this->openListItem("Test module #6 (in vendor dir)");
        $this->assertActivationButtonIsNotPresent();
        $this->assertDeactivationButtonIsNotPresent();
        $this->assertTextPresent('Please note: modules can\'t be activated or deactivated in demo shop mode.', "N");
    }

    /**
     * Testing modules in vendor directory. Checking when any file with source code class of module is deleted.
     *
     * @group adminFunctionality
     */
    public function testModulesHandlingDeleteFile()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped("Test is not for SubShop");
        }

        $this->loginAdmin("Extensions", "Modules");
        $this->openListItem("Test module #6 (in vendor dir)");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertTextPresent("1.0");
        $this->assertTextPresent("OXID eSales");
        $this->assertTextPresent("info@oxid-esales.com");
        $this->assertTextPresent("http://www.oxid-esales.com");

        $this->openListItem("Test module #6 (in vendor dir)");
        $this->openTab("Test tab EN");

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextPresent("About Us + info6");

        // vendor file name is change from myinfo6 to myinfo6test
        $this->updateInformationAboutShopExtension('oxid/test6/view/myinfo6', 'oxid/test6/view/myinfo6test');

        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextNotPresent(strtoupper("About Us + info6"));
        $this->clearCache();
        $this->openShop();
        $this->assertTextNotPresent("Module #6 title EN");
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("Module #6 title DE");

        //checking if module is deactive after  vendor file rename
        $this->loginAdmin("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("Invalid modules were detected");
        $this->assertTextPresent("Do you want to delete all registered module information and saved configurations");
        $this->assertTextPresent("OxidEsales\\Eshop\\Application\\Controller\\ContentController => oxid/test6/view/myinfo6test");
        $this->clickAndWaitFrame("yesButton");
        $this->openListItem("link=Test module #6 (in vendor dir)");
        $this->assertElementNotPresent("//form[@id='myedit']//input[@value='Deactivate']");

        //checking if module (oxblock, menu.xml) is disabled in shop after vendor file rename
        $this->clearCache();
        $this->openShop();
        $this->assertTextNotPresent("Module #6 title EN");
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("Module #6 title DE");
        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextNotPresent("About Us + info6");

        //file name is reset to the original
        $aModules = array('content' => 'oxid/test6/view/myinfo6');
        Registry::getConfig()->saveShopConfVar("aarr", "aModules", $aModules);

        $this->loginAdmin("Extensions", "Modules");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->selectMenu("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("oxid/test6/view/myinfo6");
    }

    /**
     * Testing modules in vendor directory. Checking when any file with source code class of module is deleted.
     *
     * @group adminFunctionality
     */
    public function testModulesHandlingDeleteVendorDir()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped("Test is not for SubShop");
        }

        $this->loginAdmin("Extensions", "Modules");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->selectMenu("Extensions", "Modules");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->waitForElement("link=Test tab EN");
        $this->openTab("Test tab EN");
        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextPresent("About Us + info6");

        //vendor dir name is changed from test6 to test6test
        $this->updateInformationAboutShopExtension('oxid/test6/view/myinfo6', 'oxid/test6test/view/myinfo6');

        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextNotPresent("About Us + info6");
        $this->assertTextPresent("About Us");

        //checking if module is deactivated after /dir rename
        $this->loginAdmin("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("Invalid modules were detected");
        $this->assertTextPresent("Do you want to delete all registered module information and saved configurations");
        $this->assertTextPresent("oxid/metadata.php");
        $this->clickAndWait("yesButton");
        $this->frame("list");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->frame("edit");
        $this->assertElementNotPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertTextNotPresent("Invalid modules were detected");

        //checking if module (oxblock, menu.xml) is disabled in shop after vendor dir rename
        //NOTE: we need functionality to clean the tmp folder before reimplementing he check for oxblock
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=%HOME%");

        $this->assertTextNotPresent("Module #6 title EN");
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("Module #6 title DE");
        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextNotPresent("About Us + info6");

        //checking if is restore the vendor dir name to original
        Registry::getConfig()->saveShopConfVar("aarr", "aModules", array());
        $this->loginAdmin("Extensions", "Modules");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->clickAndWait("link=Test module #6 (in vendor dir)");
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->selectMenu("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("oxid/test6/view/myinfo6");
    }

    /**
     * Change module id in chain to imitate that module was updated in file system.
     *
     * @param string $existingExtensionInformation
     * @param string $newExtensionInformation
     */
    private function updateInformationAboutShopExtension($existingExtensionInformation, $newExtensionInformation)
    {
        Registry::set(\OxidEsales\Eshop\Core\Config::class, null);
        $activeModules = Registry::getConfig()->getConfigParam("aModules");
        foreach ($activeModules as $shopClassName => $moduleClassName) {
            if (strpos($moduleClassName, $existingExtensionInformation) !== false) {
                $updatedModuleClassName = str_replace($existingExtensionInformation, $newExtensionInformation, $moduleClassName);
                $activeModules[$shopClassName] = $updatedModuleClassName;
            }
        }
        Registry::getConfig()->saveShopConfVar("aarr", "aModules", $activeModules);
        $this->clearCache();
    }
}
