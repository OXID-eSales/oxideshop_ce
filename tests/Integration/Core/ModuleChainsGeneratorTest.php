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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;


use OxidEsales\EshopCommunity\Core\ModuleChainsGenerator;
use OxidEsales\EshopCommunity\Core\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class ModuleChainsGeneratorTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Core
 * @covers  \OxidEsales\EshopCommunity\Core\ModuleChainsGenerator
 */
class ModuleChainsGeneratorTest extends UnitTestCase
{

    /**
     * Test classChainGeneration for different constellations
     *
     * @dataProvider dataProviderTestCreateClassChain
     *
     * @param $mockedModules
     * @param $modulesArray
     * @param $expectedResult
     * @param $message
     */
    public function testCreateClassChain($mockedModules, $modulesArray, $expectedResult, $message)

    {
        $classFilePaths = [];
        foreach ($mockedModules as $mockedModule) {
            $classFilePaths[] = $this->createModuleClassFile($mockedModule);
            $this->assertNotFalse(current($classFilePaths), 'Class file could not be created');
        }

        /** @var ModuleVariablesLocator|\PHPUnit_Framework_MockObject_MockObject $moduleVariablesLocatorMock */
        $moduleVariablesLocatorMock = $this->getMock('oxModuleVariablesLocator', array(), array(), '', false);

        /**
         * Create a Mock with disabled constructor
         *
         * @var ModuleChainsGenerator|\PHPUnit_Framework_MockObject_MockObject $moduleChainsGeneratorMock
         */
        $moduleChainsGeneratorMock = $this->getMock(ModuleChainsGenerator::class, ['getModulesArray'], [$moduleVariablesLocatorMock]);
        $moduleChainsGeneratorMock->expects($this->any())->method('getModulesArray')->will($this->returnValue($modulesArray));
        $class = $moduleChainsGeneratorMock->createClassChain(\OxidEsales\Eshop\Application\Model\User::class, 'oxuser');

        foreach ($classFilePaths as $classFilePath) {
            $this->assertTrue(unlink($classFilePath), 'Class file could not be deleted');
        }

        $this->assertSame(basename($expectedResult), $class, $message);
    }

    /**
     * The expected result is always the last class name of the last element of the modulesArray
     *
     * @return array
     */
    public function dataProviderTestCreateClassChain()
    {
        $mockedModules = [
            'module_1' => 'oe/testmoduleone/application/model/oemoduleoneuser',
            'module_2' => 'oe/testmoduletwo/application/model/oemoduletwouser',
            'module_3' => 'oe/testmodulethree/application/model/oemodulethreeuser',
            'module_4' => 'oe/testmodulefour/application/model/oemodulefouruser',
        ];

        return [
            ['mockedModules'  => $mockedModules,
             'modulesArray'   => [
                 'oxuser'                                       => $mockedModules['module_1'] . '&' . $mockedModules['module_2'],
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_3'] . '&' . $mockedModules['module_4'],
             ],
             'expectedResult' => $mockedModules['module_4'],
             'message'        => 'oemodulefouruser is the last class in the chain'
            ],
            ['mockedModules'  => $mockedModules,
             'modulesArray'   => [
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_3'] . '&' . $mockedModules['module_4'],
                 'oxuser'                                       => $mockedModules['module_1'] . '&' . $mockedModules['module_2'],
             ],
             'expectedResult' => $mockedModules['module_2'],
             'message'        => 'oemoduletwouser is the last class in the chain'
            ],
            ['mockedModules'  => $mockedModules,
             'modulesArray'   => [
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_3'] . '&' . $mockedModules['module_4'],
                 'oxuser'                                       => $mockedModules['module_2'] . '&' . $mockedModules['module_1'],
             ],
             'expectedResult' => $mockedModules['module_1'],
             'message'        => 'oemoduleoneuser is the last class in the chain'
            ],
            ['mockedModules'  => $mockedModules,
             'modulesArray'   => [
                 'oxuser'                                       => $mockedModules['module_1'] . '&' . $mockedModules['module_2'],
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_4'] . '&' . $mockedModules['module_3'],
             ],
             'expectedResult' => $mockedModules['module_3'],
             'message'        => 'oemodulethreeuser is the last class in the chain'
            ],
        ];
    }

    protected function createModuleClassFile($extensionPath)
    {

        $modulesDirectory = Registry::get("oxConfigFile")->getVar("sShopDir");
        $moduleClassFilePath = "$modulesDirectory/modules/$extensionPath.php";
        if (!is_dir(dirname($moduleClassFilePath))) {
            if (!mkdir(dirname($moduleClassFilePath), 0755, true)) {
                return false;
            }
        }

        $class = basename($extensionPath);
        $classDefinition = <<<EOT
            <?php
            /** 
             * This file is generated by \Unit\Core\ModuleChainsGeneratorTest::testCreateClassChain and it should have 
             * been deleted after the test run.
             */
            class $class extends {$class}_parent {}

EOT;

        if (!file_put_contents($moduleClassFilePath, $classDefinition)) {
            return false;
        }

        return $moduleClassFilePath;
    }
}
