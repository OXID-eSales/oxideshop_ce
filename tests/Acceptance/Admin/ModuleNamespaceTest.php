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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

/**
 * Module functionality functionality.
 *
 * @group module
 */
class ModuleNamespaceTest extends ModuleBaseTest
{
    const TEST_MODULE_NAMESPACE = 'Vendor1/WithNamespaceAndMetadataV2';
    const TEST_MODULE_OLDSTYLE = 'without_own_module_namespace';

    const TITLE_MODULE_NAMESPACE = 'Test module #9 - namespaced (EshopAcceptanceTestModuleNine)';
    const TITLE_MODULE_OLDSTYLE = 'Test module #10 - not namespaced';

    const ID_MODULE_NAMESPACE = 'EshopAcceptanceTestModuleNine';
    const ID_MODULE_OLDSTYLE = 'EshopAcceptanceTestModuleTen';

    const TEST_ARTICLE_OXID = 'f4f73033cf5045525644042325355732'; // '/en/Special-Offers/Transport-container-BARREL.html'

    /**
     * Set up
     */
    protected function setUp()
    {
        parent::setUp();

        // make sure the namespaced test module is in place
        $this->deleteModule(self::TEST_MODULE_NAMESPACE);
        $this->restoreTestModule(self::TEST_MODULE_NAMESPACE);
        $this->clearCache();
        $this->clearCookies();
        $this->clearTemp();

        //TODO: check if test works for subshop as well (which login to use, do we need to provide shopid somewhere ...)
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for SubShop');
        }
    }

    /**
     * Test deactivating a namespace module.
     * Verify that the module is gone from class chain after deactivation.
     */
    public function testDeactivateNamespaceModule()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TITLE_MODULE_OLDSTYLE);
        $this->activateModule(self::TITLE_MODULE_NAMESPACE);
        $this->checkFrontend(3 * 3 * 2 * 2); // price multiplies more than expected, some flaw in module

        $this->clearCookies();
        $this->loginAdmin('Extensions', 'Modules');
        $this->deactivateModule(self::TITLE_MODULE_NAMESPACE);
        $this->assertNoProblem();
        $this->checkFrontend(3 * 3); // price multiplies more than expected, some flaw in module
    }

    /**
     * Physically remove an activated module from shop without deactivating it.
     * Shop should detect this and request cleanup in shop admin backend.
     * Case that we have another active module.
     */
    public function testPhysicallyDeleteNamespacedModuleWithoutDeactivation()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TITLE_MODULE_OLDSTYLE);
        $this->activateModule(self::TITLE_MODULE_NAMESPACE);
        $this->assertNoProblem(); // commented cause it interferes with #2 atm
        $this->checkFrontend(3 * 3 * 2 * 2); // price multiplies more than expected, some flaw in module

        $this->deleteModule(self::TEST_MODULE_NAMESPACE);

        $this->loginAdmin('Extensions', 'Modules');
        $this->frame('edit');
        $this->assertTextPresent('Problematic Files');
        $this->assertTextPresent(self::ID_MODULE_NAMESPACE . '/metadata.php');
        $this->clickAndWait('yesButton');

        $this->checkFrontend(3 * 3); // price multiplies more than expected, some flaw in module
    }

    /**
     * Activate then deactivate a namespace module and then physically remove it from shop.
     * The shop should detect this and request cleanup in shop admin backend.
     * Case that there is not other active module around.
     */
    public function testPhysicallyDeleteNamespacedModuleWithDeactivationAsOnlyModule()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TITLE_MODULE_NAMESPACE);
        $this->deactivateModule(self::TITLE_MODULE_NAMESPACE);
        $this->deleteModule(self::TEST_MODULE_NAMESPACE);

        $this->loginAdmin('Extensions', 'Modules');
        $this->frame('edit');
        $this->assertTextPresent('Problematic Files');
        $this->assertTextPresent(self::ID_MODULE_NAMESPACE . '/metadata.php');
        $this->clickAndWait('yesButton');

        $this->checkFrontend(1);
    }

    /**
     * Activate then deactivate a namespace module and then physically remove it from shop.
     * The shop should detect this and request cleanup in shop admin backend.
     * Case that we have another active module.
     */
    public function testPhysicallyDeleteNamespacedModuleWithDeactivation()
    {
        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TITLE_MODULE_OLDSTYLE);
        $this->activateModule(self::TITLE_MODULE_NAMESPACE);
        $this->deactivateModule(self::TITLE_MODULE_NAMESPACE);
        $this->deleteModule(self::TEST_MODULE_NAMESPACE);

        $this->loginAdmin('Extensions', 'Modules');
        $this->frame('edit');
        $this->assertTextPresent('Problematic Files');
        $this->assertTextPresent(self::ID_MODULE_NAMESPACE . '/metadata.php');
        $this->clickAndWait('yesButton');

        $this->checkFrontend(3 * 3); //price multiplies more than expected, some flaw in module
    }

    /**
     * Test modules affect the frontend price.
     *
     * @param integer $factor
     * @param bool    $clearCookies
     */
    protected function checkFrontend($factor = 1, $clearCookies = true)
    {
        if ($clearCookies) {
            $this->clearCookies();
        }

        $this->openShop();
        $this->assertTextPresent('OXID Online Shop - All about watersports, sportswear and fashion');

        $this->openArticle(self::TEST_ARTICLE_OXID, true);
        $standardPrice = 24.95 * $factor;
        $standardPrice = str_replace('.', ',', $standardPrice);
        $this->assertTextPresent($standardPrice);
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
}
