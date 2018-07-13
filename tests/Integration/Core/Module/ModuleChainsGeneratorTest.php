<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;


use OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator;
use OxidEsales\EshopCommunity\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

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
        $moduleVariablesLocatorMock = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class, array(), array(), '', false);

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
     * Test creating active class chain for different constellations.
     *
     * @dataProvider dataProviderTestCreateClassChain
     *
     * @param $mockedModules
     * @param $modulesArray
     * @param $expectedResult
     * @param $message
     */
    public function testGetActiveChain($mockedModules, $modulesArray, $expectedResult, $message)
    {
        $classFilePaths = [];
        foreach ($mockedModules as $mockedModule) {
            $classFilePaths[] = $this->createModuleClassFile($mockedModule);
            $this->assertNotFalse(current($classFilePaths), 'Class file could not be created');
        }

        /** @var ModuleVariablesLocator|\PHPUnit_Framework_MockObject_MockObject $moduleVariablesLocatorMock */
        $moduleVariablesLocatorMock = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::class, array(), array(), '', false);

        /**
         * Create a Mock with disabled constructor
         *
         * @var ModuleChainsGenerator|\PHPUnit_Framework_MockObject_MockObject $moduleChainsGeneratorMock
         */
        $moduleChainsGeneratorMock = $this->getMock(ModuleChainsGenerator::class, ['getModulesArray'], [$moduleVariablesLocatorMock]);
        $moduleChainsGeneratorMock->expects($this->any())->method('getModulesArray')->will($this->returnValue($modulesArray));
        $chain = $moduleChainsGeneratorMock->getActiveChain(\OxidEsales\Eshop\Application\Model\User::class, 'oxuser');

        foreach ($classFilePaths as $classFilePath) {
            $this->assertTrue(unlink($classFilePath), 'Class file could not be deleted');
        }

        //verify that the chain is filled and that the last class in chain is as expected
        $this->assertEquals(4, count($chain), $message);
        $this->assertSame(basename($expectedResult), basename($chain[count($chain)-1]), $message);
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
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
class $class extends {$class}_parent {}
EOT;

        if (!file_put_contents($moduleClassFilePath, $classDefinition)) {
            return false;
        }

        return $moduleClassFilePath;
    }
}
