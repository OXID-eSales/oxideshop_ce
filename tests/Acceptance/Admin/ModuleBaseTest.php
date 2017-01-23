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

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;
use OxidEsales\TestingLibrary\ServiceCaller;
use OxidEsales\TestingLibrary\Services\Files\Remove;
use OxidEsales\TestingLibrary\FileCopier;

/**
 * Module functionality.
 *
 * @group module
 */
abstract class ModuleBaseTest extends AdminTestCase
{
    /**
     * Delete all module files from shop
     *
     * @param string $module
     */
    protected function deleteModule($module)
    {
        $serviceCaller = new ServiceCaller($this->getTestConfig());
        $serviceCaller->setParameter(
            Remove::FILES_PARAMETER_NAME,
            [
                $this->getTestConfig()->getShopPath() . DIRECTORY_SEPARATOR . 'modules' .
                DIRECTORY_SEPARATOR . $module
            ]
        );
        $serviceCaller->callService(Remove::class);
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
     * Copy module files to shop.
     *
     * @param string $moduleDirectory
     */
    protected function restoreTestModule($moduleDirectory)
    {
        $config = $this->getTestConfig();
        $testDataPath = realpath(__DIR__ . '/testData/modules/' . $moduleDirectory);
        if ($testDataPath) {
            $target = $config->getRemoteDirectory() ? $config->getRemoteDirectory() : $config->getShopPath();
            $target .= 'modules/';
            $fileCopier = new FileCopier();
            $fileCopier->createEmptyDirectory($target . $moduleDirectory);
            $fileCopier->copyFiles($testDataPath, $target . $moduleDirectory);
        }
    }
}
