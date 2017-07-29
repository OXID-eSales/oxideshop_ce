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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use oxTestModules;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleChainsGeneratorTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    public function testGetActiveModuleChain()
    {
        $aModuleChain = array("oe/moduleName2/myorder");

        /** @var ModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocator = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class, array('getModuleVariable'), array(), '', false);
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
        $moduleVariablesLocator = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class, array('getModuleVariable'), array(), '',
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
        $moduleVariablesLocator = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class, array(), array(), '', false);

        $moduleChainsGenerator = oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

        $oModuleInstaller = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, array('deactivate'));
        $oModuleInstaller->expects($this->once())->method('deactivate')->with($oModule);
        oxTestModules::addModuleObject('oxModuleInstaller', $oModuleInstaller);

        $moduleChainsGenerator->disableModule($sModuleId);
    }

    /**
     * @dataProvider dataProviderTestOnModuleExtensionCreationError
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::onModuleExtensionCreationError
     */
    public function testOnModuleExtensionCreationError($blDoNotDisableModuleOnError, $expectedException, $message)
    {
        $this->setExpectedException($expectedException);

        $moduleChainsGeneratorMock = $this->generateModuleChainsGeneratorWithNonExistingFileConfiguration($blDoNotDisableModuleOnError);

        $actualClassName = $moduleChainsGeneratorMock->createClassChain('content');

        $this->assertEquals('content', $actualClassName, $message);
    }

    /**
     * @param bool $blDoNotDisableModuleOnError
     *
     * @return \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator
     */
    private function generateModuleChainsGeneratorWithNonExistingFileConfiguration($blDoNotDisableModuleOnError)
    {
        /** @var ModuleVariablesLocator|MockObject $oUtilsObject */
        $moduleVariablesLocatorMock = $this->getMock(
            \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class,
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
            ['getConfigBlDoNotDisableModuleOnError', 'getConfigDebugMode', 'isUnitTest'],
            [$moduleVariablesLocatorMock]
        );
        $moduleChainsGeneratorMock
            ->expects($this->any())
            ->method('getConfigBlDoNotDisableModuleOnError')
            ->will($this->returnValue($blDoNotDisableModuleOnError));

        /**
         * It is fake not to be a unit test in order to execute the error handling, which is not done for the rest of
         * the tests.
         */
        $moduleChainsGeneratorMock
            ->expects($this->any())
            ->method('isUnitTest')
            ->will($this->returnValue(false));

        return $moduleChainsGeneratorMock;
    }

    public function dataProviderTestOnModuleExtensionCreationError()
    {
        return [
          [
            'blDoNotDisableModuleOnError' => 0,
            'expectedException' => null,
            'message' => 'If blDoNotDisableModuleOnError is false, no Exception will be thrown.
                          In this case the module will be disabled and createClassChain will return the shop class and
                          not the module class.'
          ],
          [
            'blDoNotDisableModuleOnError' => 1,
            'expectedException' => SystemComponentException::class,
            'message' => 'If blDoNotDisableModuleOnError is true, an Exception will be thrown.
                          In this case the module will not be disabled.'
          ],
        ];
    }
}
