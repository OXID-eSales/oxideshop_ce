<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/**
 * Test metadata 2.0 functionality.
 *
 * @group module
 */
class ModuleControllersTest extends AdminTestCase
{
    const TEST_NAMESPACE_MODULE_FOLDER = 'test_module_controller_routing_ns';
    const TEST_NAMESPACE_MODULE_TITLE = 'Test metadata_controllers_feature_ns';
    const TEST_NAMESPACE_MODULE_ID = 'test_module_controller_routing_ns';

    const TEST_PLAIN_MODULE_FOLDER = 'test_module_controller_routing';
    const TEST_PLAIN_MODULE_TITLE = 'Test metadata_controllers_feature';
    const TEST_PLAIN_MODULE_ID = 'test_namespace_module_controller_routing';

    /**
     * Set up
     */
    protected function setUp()
    {
        parent::setUp();

        $this->installModule(self::TEST_NAMESPACE_MODULE_FOLDER);
        $this->installModule(self::TEST_PLAIN_MODULE_FOLDER);

        //TODO: check if test works for subshop as well (which login to use, do we need to provide shopid somewhere ...)
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for SubShop');
        }
    }

    /**
     * Test module controller for namespace module
     */
    public function testNamespaceModule()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TEST_NAMESPACE_MODULE_TITLE);
        $this->assertNoProblem();
        $this->checkFrontendForNamespaceModule();
    }

    /**
     * Test module controller for module without namespace
     */
    public function testPlainModule()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TEST_PLAIN_MODULE_TITLE);
        $this->assertNoProblem();
        $this->checkFrontendForPlainModule();
    }

    /**
     * Test module deactivation.
     */
    public function testModuleDeactivation()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TEST_NAMESPACE_MODULE_TITLE);
        $this->assertNoProblem();
        $this->deactivateModule(self::TEST_NAMESPACE_MODULE_TITLE);

        $this->clearCookies();
        $this->openShop();
        $this->open(shopURL . '/index.php?cl=test_module_controller_routing_ns_MyModuleController');
        $this->assertTextNotPresent('Test module for controller routing');

        $this->assertLoggedException(
            \OxidEsales\Eshop\Core\Exception\SystemComponentException::class,
            "EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND test_module_controller_routing_ns_mymodulecontroller"
        );
    }

    /**
     * Test module activation, deactivation and again activation
     */
    public function testModuleActivationDeactivationActivation()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TEST_NAMESPACE_MODULE_TITLE);
        $this->deactivateModule(self::TEST_NAMESPACE_MODULE_TITLE);
        $this->activateModule(self::TEST_NAMESPACE_MODULE_TITLE);
        $this->checkFrontendForNamespaceModule();
    }

    /**
     * Helper function for module activation
     *
     * @param string $module
     */
    protected function activateModule($moduleTitle)
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

    /**
     * Test if frontend module functionality works
     */
    protected function checkFrontendForNamespaceModule()
    {
        $this->clearCookies();

        $this->openShop();
        $message = 'some message to controller test module';

        $this->open(shopURL . '/index.php?cl=test_module_controller_routing_ns_MyModuleController');
        $this->type("mymodule_message", $message);
        $this->clickAndWait("//button[text()='%SUBMIT%']");
        $this->assertTextPresent('Test module for controller routing - ' . $message);

        $this->open(shopURL . '/index.php?cl=test_module_controller_routing_ns_MyOtherModuleController');
        $this->type("mymodule_message", $message);
        $this->clickAndWait("//button[text()='%SUBMIT%']");
        $this->assertTextPresent('Test module for controller routing other template - MyOtherModuleController - ' . $message);
    }

    /**
     * Test if frontend module functionality works
     */
    protected function checkFrontendForPlainModule()
    {
        $this->clearCookies();

        $this->openShop();
        $message = 'some message to test module';

        $this->open(shopURL . '/index.php?cl=test_module_controller_routing_MyModuleController');
        $this->type("mymodule_message", $message);
        $this->clickAndWait("//button[text()='%SUBMIT%']");
        $this->assertTextPresent('Test module for controller routing noNamespace - ' . $message);

        $this->open(shopURL . '/index.php?cl=test_module_controller_routing_MyOtherModuleController');
        $this->type("mymodule_message", $message);
        $this->clickAndWait("//button[text()='%SUBMIT%']");
        $this->assertTextPresent('Test module for controller routing noNS other template - MyOtherModuleController - ' . $message);
    }

    /**
     * Check for problematic extensions
     */
    protected function assertNoProblem()
    {
        $this->selectMenu('Extensions', 'Modules');
        $this->frame('edit');
        $this->assertTextNotPresent('Problematic Files');
    }

    /**
     * Helper function for module deactivation
     *
     * @param string $module
     */
    protected function deactivateModule($moduleTitle)
    {
        $this->openListItem($moduleTitle);
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Deactivate']", "list");
        $this->waitForFrameToLoad('list');
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Activate']");
    }

    private function installModule(string $path)
    {
        $moduleConfigurationInstaller = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleConfigurationInstallerInterface::class);

        $moduleConfigurationInstaller->install(
            __DIR__ . '/testData/modules/' . $path,
            $path
        );
    }
}
