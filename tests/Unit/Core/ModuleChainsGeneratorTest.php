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
namespace Unit\Core;

use \oxTestModules;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ModuleChainsGeneratorTest extends \OxidTestCase
{

    public function testGetActiveModuleChain()
    {
        $aModuleChain = array("oe/invoicepdf2/myorder");

        /** @var oxModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array('getModuleVariable'), array(), '', false);
        $valueMap = array(
            array('aDisabledModules', array('invoicepdf')),
            array('aModulePaths', array("invoicepdf2" => "oe/invoicepdf2", "invoicepdf" => "oe/invoicepdf")),
        );
        $moduleVariablesLocator->expects($this->any())->method('getModuleVariable')->will($this->returnValueMap($valueMap));

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $this->assertEquals($aModuleChain, $moduleChainsGenerator->filterInactiveExtensions($aModuleChain));
    }

    public function testGetActiveModuleChainIfDisabled()
    {
        $aModuleChain = array("oe/invoicepdf/myorder");
        $aModuleChainResult = array();

        /** @var oxModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array('getModuleVariable'), array(), '', false);
        $valueMap = array(
            array('aDisabledModules', array('invoicepdf')),
            array('aModulePaths', array("invoicepdf" => "oe/invoicepdf")),
        );
        $moduleVariablesLocator->expects($this->any())->method('getModuleVariable')->will($this->returnValueMap($valueMap));

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $this->assertEquals($aModuleChainResult, $moduleChainsGenerator->filterInactiveExtensions($aModuleChain));
    }

    public function testGetActiveModuleChainIfDisabledWithoutPath()
    {
        $aModuleChain = array("invoicepdf/myorder");
        $aModuleChainResult = array();

        /** @var oxModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array('getModuleVariable'), array(), '', false);
        $valueMap = array(
            array('aDisabledModules', array('invoicepdf')),
            array('aModulePaths', array("invoicepdf2" => "oe/invoicepdf2")),
        );
        $moduleVariablesLocator->expects($this->any())->method('getModuleVariable')->will($this->returnValueMap($valueMap));

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $this->assertEquals($aModuleChainResult, $moduleChainsGenerator->filterInactiveExtensions($aModuleChain));
    }

    public function testDisableModule()
    {
        $sModuleId = 'testId';

        $oModule = oxNew('oxModule');
        $oModule->load($sModuleId);

        /** @var oxModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array(), array(), '', false);

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $oModuleInstaller = $this->getMock('oxModuleInstaller', array('deactivate'));
        $oModuleInstaller->expects($this->once())->method('deactivate')->with($oModule);
        oxTestModules::addModuleObject('oxModuleInstaller', $oModuleInstaller);

        $moduleChainsGenerator->disableModule($sModuleId);
    }
}
