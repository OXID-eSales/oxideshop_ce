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
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;
use OxidEsales\TestingLibrary\ServiceCaller;
use OxidEsales\TestingLibrary\Services\Files\Remove;
use OxidEsales\TestingLibrary\Services\Library\FileHandler;

/** Admin interface functionality. */
class ModuleTest extends AdminTestCase
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
        $this->assertElementValue("confstrs[testEmptyStrConfig]", 'testString', 'Without default value text input (str) should be empty');
        $this->assertElementValue("confarrs[testEmptyArrConfig]", "option1\noption2\noption3", 'Without default value text area array (arr) should be empty');
        $this->assertElementValue("confaarrs[testEmptyAArrConfig]", "key1 => option1\nkey2 => option2", 'Without default value text area assoc array (aarr) should be empty');
        $this->assertElementValue("confselects[testEmptySelectConfig]", '2', 'Without default value first option should be selected for selects');

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

        $this->activateModuleAndCheck("Test module #1");
        $this->activateModuleAndCheck("Test module #2");
        $this->activateModuleAndCheck("Test module #7");
        $this->activateModuleAndCheck("Namespaced module #1");

        //checking if module all entry is displayed
        $this->openTab("Installed Shop Modules");
        $this->assertTextPresent("Drag items to change modules order. After changing order press Save button to save current modules order.");
        $childClassesContainerId = 'OxidEsales---Eshop---Application---Controller---ContentController';
        $namespacedModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='OxidEsales\\EshopCommunity\\Tests\\Acceptance\\Admin\\testData\\modules\\oxid\\namespace1\\Controllers\\ContentController']/span";
        $this->assertEquals(
            'OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\namespace1\Controllers\ContentController',
            $this->getText($namespacedModuleClassElementLocator)
        );

        //TODO: uncomment when classes ordering issue will be solved.
//        $test1ModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='test1/controllers/test1content']/span";
//        $this->assertEquals('test1/controllers/test1content', $this->getText($test1ModuleClassElementLocator));
//        $test2ModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='test2/view/myinfo2']/span";
//        $this->assertEquals('test2/view/myinfo2', $this->getText($test2ModuleClassElementLocator));
//        $test7ModuleClassElementLocator = "//li[@id='$childClassesContainerId']//li[@id='oxid/test7/view/myinfo7']/span";
//        $this->assertEquals('oxid/test7/view/myinfo7', $this->getText($test7ModuleClassElementLocator));

        //TODO: uncomment when solution will be implemented.
//        $this->open(shopURL."?cl=ModuleController&fnc=showContent");
//        $this->assertTextPresent(' + namespace1');

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextPresent("About Us + info1 + info2 + info7 + namespace1");

        $this->deleteModuleClass();

        $this->loginAdmin("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("Problematic Files");
        $this->assertTextPresent('test1/controllers/test1content');
        $this->clickAndWait("yesButton");

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextPresent("About Us + info2 + info7 + namespace1");
        $this->assertTextNotPresent("About Us + info1 + info2 + info7 + namespace1");
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

    protected function assertActivationButtonIsPresent()
    {
        $this->assertButtonIsPresent('Activate');
    }

    protected function assertDeactivationButtonIsPresent()
    {
        $this->assertButtonIsPresent('Deactivate');
    }

    protected function assertButtonIsPresent($buttonValue)
    {
        $this->assertElementPresent("//form[@id='myedit']//input[@value='{$buttonValue}']");
    }

    protected function assertActivationButtonIsNotPresent()
    {
        $this->assertButtonIsNotPresent('Activate');
    }

    protected function assertDeactivationButtonIsNotPresent()
    {
        $this->assertButtonIsNotPresent('Deactivate');
    }

    protected function assertButtonIsNotPresent($buttonValue)
    {
        $this->assertElementNotPresent("//form[@id='myedit']//input[@value='{$buttonValue}']");
    }

    protected function switchToDemoMode()
    {
        $this->callShopSC("oxConfig", null, null, array("blDemoShop" => array("type" => "bool", "value" => "true")));
    }

    protected function deleteModuleClass()
    {
        $oServiceCaller = new ServiceCaller($this->getTestConfig());
        $oServiceCaller->setParameter(Remove::FILES_PARAMETER_NAME,
            [
                $this->getTestConfig()->getShopPath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'test1'
                . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'test1content.php'
            ]
        );
        $oServiceCaller->callService(Remove::class);
    }

    /**
     * Method activates module and checks if module information is present.
     *
     * @param string $moduleTitle
     */
    protected function activateModuleAndCheck($moduleTitle)
    {
        $this->openListItem($moduleTitle);
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']", "list");
        $this->waitForFrameToLoad('list');
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertTextPresent($moduleTitle);
        $this->assertTextPresent("1.0");
        $this->assertTextPresent("OXID");
        $this->assertTextPresent("-");
        $this->assertTextPresent("-");
    }
}
