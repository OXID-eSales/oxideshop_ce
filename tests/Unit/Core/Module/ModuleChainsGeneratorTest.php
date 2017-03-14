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

use OxidEsales\EshopCommunity\Core\Module\ModuleVariablesLocator;
use oxTestModules;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleChainsGeneratorTest extends \OxidTestCase
{

    public function testGetActiveModuleChain()
    {
        $aModuleChain = array("oe/moduleName2/myorder");

        /** @var ModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array('getModuleVariable'), array(), '', false);
        $valueMap = array(
            array('aDisabledModules', array('moduleName')),
            array('aModulePaths', array("moduleName2" => "oe/moduleName2", "moduleName" => "oe/moduleName")),
        );
        $moduleVariablesLocator->expects($this->any())->method('getModuleVariable')->will($this->returnValueMap($valueMap));

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $this->assertEquals($aModuleChain, $moduleChainsGenerator->filterInactiveExtensions($aModuleChain));
    }

    public function testGetActiveModuleChainIfDisabled()
    {
        $aModuleChain = array("oe/moduleName/myorder");
        $aModuleChainResult = array();

        /** @var ModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array('getModuleVariable'), array(), '',
          false);
        $valueMap = array(
            array('aDisabledModules', array('moduleName')),
            array('aModuleExtensions', array("moduleName" => array("oe/moduleName/myorder"))),
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

        /** @var ModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock('oxModuleVariablesLocator', array(), array(), '', false);

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $oModuleInstaller = $this->getMock('oxModuleInstaller', array('deactivate'));
        $oModuleInstaller->expects($this->once())->method('deactivate')->with($oModule);
        oxTestModules::addModuleObject('oxModuleInstaller', $oModuleInstaller);

        $moduleChainsGenerator->disableModule($sModuleId);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::onModuleExtensionCreationError
     */
    public function testOnModuleExtensionCreationError()
    {
        $blDoNotDisableModuleOnError = false;
        $message= 'If blDoNotDisableModuleOnError is false, no Exception will be thrown.
                   In this case then the module will be disabled and createClassChain will return the shop class and
                   not the module class.';

        /** @var ModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocatorMock = $this->getMock(
          \OxidEsales\EshopCommunity\Core\Module\ModuleVariablesLocator::class,
          ['getModuleVariable'],
          [],
          '',
          false
        );
        $valueMap = [
          ['aModules', ['content' => 'content&notExistingClass']],
          ['aDisabledModules', []]
        ];
        $moduleVariablesLocatorMock
          ->expects($this->any())
          ->method('getModuleVariable')
          ->will($this->returnValueMap($valueMap));

        $moduleChainsGeneratorMock = $this->getMock(
          \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::class,
          ['getConfigBlDoNotDisableModuleOnError', 'getConfigDebugMode'],
          [$moduleVariablesLocatorMock]
        );
        $moduleChainsGeneratorMock
          ->expects($this->any())
          ->method('getConfigBlDoNotDisableModuleOnError')
          ->will($this->returnValue($blDoNotDisableModuleOnError));

        /**
         * Real error handling on missing files is disabled for the tests, but when the shop tries to include that not
         * existing file we expect an error to be thrown
         */
        $this->setExpectedException(\PHPUnit_Framework_Error_Warning::class);
        $actualClassName = $moduleChainsGeneratorMock->createClassChain('content');

        $this->assertEquals('content', $actualClassName, $message);
    }

    public function dataProviderTestOnModuleExtensionCreationError()
    {
        return [
          [
            'blDoNotDisableModuleOnError' => 0,
            'expectedException' => null,
            'message' => 'If blDoNotDisableModuleOnError is false, no Exception will be thrown.
                          In this case then the module will be disabled and createClassChain will return the shop class and
                          not the module class.'
          ],
        ];
    }
}
