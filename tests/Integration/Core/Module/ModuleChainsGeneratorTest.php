<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Module\ModuleChainsGenerator;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\FirstUser;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\FourthUser;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\SecondUser;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\ThirdUser;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ModuleChainsGeneratorTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Core
 * @covers  OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator
 */
class ModuleChainsGeneratorTest extends UnitTestCase
{
    /**
     * Test classChainGeneration for different constellations
     *
     * @dataProvider dataProviderTestCreateClassChain
     *
     * @param $modulesArray
     * @param $expectedResult
     * @param $message
     */
    public function testCreateClassChain($modulesArray, $expectedResult, $message): void
    {
        /** @var ModuleVariablesLocator|MockObject $moduleVariablesLocatorMock */
        $moduleVariablesLocatorMock = $this->getMock(ModuleVariablesLocator::class, array(), array(), '', false);

        /**
         * Create a Mock with disabled constructor
         *
         * @var ModuleChainsGenerator|MockObject $moduleChainsGeneratorMock
         */
        $moduleChainsGeneratorMock = $this->getMock(ModuleChainsGenerator::class, ['getClassExtensionChain'], [$moduleVariablesLocatorMock]);
        $moduleChainsGeneratorMock->method('getClassExtensionChain')->willReturn($modulesArray);
        $class = $moduleChainsGeneratorMock->createClassChain(\OxidEsales\Eshop\Application\Model\User::class, 'oxuser');

        self::assertSame(basename($expectedResult), $class, $message);
    }

    /**
     * Test creating active class chain for different constellations.
     *
     * @dataProvider dataProviderTestCreateClassChain
     *
     * @param $modulesArray
     * @param $expectedResult
     * @param $message
     */
    public function testGetActiveChain($modulesArray, $expectedResult, $message): void
    {
        /** @var ModuleVariablesLocator|MockObject $moduleVariablesLocatorMock */
        $moduleVariablesLocatorMock = $this->getMock(ModuleVariablesLocator::class, array(), array(), '', false);

        /**
         * Create a Mock with disabled constructor
         *
         * @var ModuleChainsGenerator|MockObject $moduleChainsGeneratorMock
         */
        $moduleChainsGeneratorMock = $this->getMock(ModuleChainsGenerator::class, ['getClassExtensionChain'], [$moduleVariablesLocatorMock]);
        $moduleChainsGeneratorMock->method('getClassExtensionChain')->willReturn($modulesArray);
        $chain = $moduleChainsGeneratorMock->getActiveChain(\OxidEsales\Eshop\Application\Model\User::class, 'oxuser');

        //verify that the chain is filled and that the last class in chain is as expected
        self::assertCount(2, $chain, $message);
        self::assertSame(basename($expectedResult), basename($chain[count($chain) - 1]), $message);
    }

    /**
     * The expected result is always the last class name of the last element of the modulesArray
     *
     * @return array
     */
    public function dataProviderTestCreateClassChain(): array
    {
        $mockedModules = [
            'module_1' => FirstUser::class,
            'module_2' => SecondUser::class,
            'module_3' => ThirdUser::class,
            'module_4' => FourthUser::class,
        ];

        return [
            [
                'modulesArray'   => [
                    User::class => [$mockedModules['module_3'] , $mockedModules['module_4']],
                ],
                'expectedResult' => $mockedModules['module_4'],
                'message'        => 'oemodulefouruser is the last class in the chain'
            ],
            [
                'modulesArray'   => [
                    'oxuser'    => [$mockedModules['module_1'] , $mockedModules['module_2']],
                ],
                'expectedResult' => $mockedModules['module_2'],
                'message'        => 'oemoduletwouser is the last class in the chain'
            ],
        ];
    }
}
