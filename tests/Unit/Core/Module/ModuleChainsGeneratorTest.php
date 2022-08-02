<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator;
use oxTestModules;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleChainsGeneratorTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::onModuleExtensionCreationError
     */
    public function testOnModuleExtensionCreationError(): void
    {
        $moduleChainsGeneratorMock = $this->generateModuleChainsGeneratorWithNonExistingFileConfiguration();

        $actualClassName = $moduleChainsGeneratorMock->createClassChain('content');

        $this->assertEquals('content', $actualClassName);
        $this->assertLoggedException(SystemComponentException::class);
    }

    /**
     *
     * @return ModuleChainsGenerator
     */
    private function generateModuleChainsGeneratorWithNonExistingFileConfiguration(): ModuleChainsGenerator
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
            ['aModules', ['content' => ['notExistingClass']]],
            ['aDisabledModules', []]
        ];
        $moduleVariablesLocatorMock
            ->method('getModuleVariable')
            ->willReturnMap($valueMap);

        $moduleChainsGeneratorMock = $this->getMock(
            ModuleChainsGenerator::class,
            ['getConfigDebugMode', 'isUnitTest'],
            [$moduleVariablesLocatorMock]
        );

        /**
         * It is fake not to be a unit test in order to execute the error handling, which is not done for the rest of
         * the tests.
         */
        $moduleChainsGeneratorMock
            ->method('isUnitTest')
            ->willReturn(false);

        return $moduleChainsGeneratorMock;
    }
}
