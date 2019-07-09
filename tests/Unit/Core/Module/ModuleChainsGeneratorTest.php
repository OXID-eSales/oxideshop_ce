<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use oxTestModules;
use PHPUnit\Framework\MockObject\MockObject;

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

    /**
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::onModuleExtensionCreationError
     */
    public function testOnModuleExtensionCreationError()
    {
        $moduleChainsGeneratorMock = $this->generateModuleChainsGeneratorWithNonExistingFileConfiguration();

        $actualClassName = $moduleChainsGeneratorMock->createClassChain('content');

        $this->assertEquals('content', $actualClassName);
        $this->assertLoggedException(SystemComponentException::class);
    }

    /**
     *
     * @return \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator
     */
    private function generateModuleChainsGeneratorWithNonExistingFileConfiguration()
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
            ['aModules', ['content' => 'notExistingClass']],
            ['aDisabledModules', []]
        ];
        $moduleVariablesLocatorMock
            ->expects($this->any())
            ->method('getModuleVariable')
            ->will($this->returnValueMap($valueMap));

        $moduleChainsGeneratorMock = $this->getMock(
            \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::class,
            ['getConfigDebugMode', 'isUnitTest'],
            [$moduleVariablesLocatorMock]
        );

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
}
