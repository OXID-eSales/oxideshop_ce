<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Module\ModuleChainsGenerator;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\FirstUser;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\FourthUser;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\SecondUser;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\chainTestModuleClasses\ThirdUser;
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
     * @var \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator
     */
    private $moduleChainsGenerator = null;

    /**
     * Test classChainGeneration for different constellations
     *
     * @dataProvider dataProviderTestCreateClassChain
     *
     * @param $modulesArray
     * @param $expectedResult
     * @param $message
     */
    public function testCreateClassChain($modulesArray, $expectedResult, $message)
    {
        /** @var ModuleVariablesLocator|\PHPUnit\Framework\MockObject\MockObject $moduleVariablesLocatorMock */
        $moduleVariablesLocatorMock = $this->getMock(ModuleVariablesLocator::class, array(), array(), '', false);

        /**
         * Create a Mock with disabled constructor
         *
         * @var ModuleChainsGenerator|\PHPUnit\Framework\MockObject\MockObject $moduleChainsGeneratorMock
         */
        $moduleChainsGeneratorMock = $this->getMock(ModuleChainsGenerator::class, ['getClassExtensionChain'], [$moduleVariablesLocatorMock]);
        $moduleChainsGeneratorMock->expects($this->any())->method('getClassExtensionChain')->will($this->returnValue($modulesArray));
        $class = $moduleChainsGeneratorMock->createClassChain(\OxidEsales\Eshop\Application\Model\User::class, 'oxuser');

        $this->assertSame(basename($expectedResult), $class, $message);
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
    public function testGetActiveChain($modulesArray, $expectedResult, $message)
    {
        /** @var ModuleVariablesLocator|\PHPUnit\Framework\MockObject\MockObject $moduleVariablesLocatorMock */
        $moduleVariablesLocatorMock = $this->getMock(ModuleVariablesLocator::class, array(), array(), '', false);

        /**
         * Create a Mock with disabled constructor
         *
         * @var ModuleChainsGenerator|\PHPUnit\Framework\MockObject\MockObject $moduleChainsGeneratorMock
         */
        $moduleChainsGeneratorMock = $this->getMock(ModuleChainsGenerator::class, ['getClassExtensionChain'], [$moduleVariablesLocatorMock]);
        $moduleChainsGeneratorMock->expects($this->any())->method('getClassExtensionChain')->will($this->returnValue($modulesArray));
        $chain = $moduleChainsGeneratorMock->getActiveChain(\OxidEsales\Eshop\Application\Model\User::class, 'oxuser');

        //verify that the chain is filled and that the last class in chain is as expected
        $this->assertEquals(4, count($chain), $message);
        $this->assertSame(basename($expectedResult), basename($chain[count($chain) - 1]), $message);
    }

    public function testGetDisabledModuleIds()
    {
        $moduleId1 = 'with_class_extensions';
        $moduleId2 = 'with_metadata_v21';

        $moduleChainsGenerator = $this->getModuleChainsGenerator();
        $disabledModuleIds = $moduleChainsGenerator->getDisabledModuleIds();
        $this->assertNotContains([$moduleId1, $moduleId2], $disabledModuleIds);

        $this->installModule($moduleId1);
        $this->installModule($moduleId2);

        $this->assertContains($moduleId1, $moduleChainsGenerator->getDisabledModuleIds());
        $this->assertContains($moduleId2, $moduleChainsGenerator->getDisabledModuleIds());

        $this->activateTestModule($moduleId1);
        $this->assertContains($moduleId2, $moduleChainsGenerator->getDisabledModuleIds());
        $this->assertNotContains($moduleId1, $moduleChainsGenerator->getDisabledModuleIds());

        $this->deactivateTestModule($moduleId1);
        $this->removeTestModule($moduleId1);
        $this->removeTestModule($moduleId2);
    }

    /**
     * The expected result is always the last class name of the last element of the modulesArray
     *
     * @return array
     */
    public function dataProviderTestCreateClassChain()
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
                 'oxuser'                                       => $mockedModules['module_1'] . '&' . $mockedModules['module_2'],
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_3'] . '&' . $mockedModules['module_4'],
             ],
             'expectedResult' => $mockedModules['module_4'],
             'message'        => 'oemodulefouruser is the last class in the chain'
            ],
            [
             'modulesArray'   => [
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_3'] . '&' . $mockedModules['module_4'],
                 'oxuser'                                       => $mockedModules['module_1'] . '&' . $mockedModules['module_2'],
             ],
             'expectedResult' => $mockedModules['module_2'],
             'message'        => 'oemoduletwouser is the last class in the chain'
            ],
            [
             'modulesArray'   => [
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_3'] . '&' . $mockedModules['module_4'],
                 'oxuser'                                       => $mockedModules['module_2'] . '&' . $mockedModules['module_1'],
             ],
             'expectedResult' => $mockedModules['module_1'],
             'message'        => 'oemoduleoneuser is the last class in the chain'
            ],
            [
             'modulesArray'   => [
                 'oxuser'                                       => $mockedModules['module_1'] . '&' . $mockedModules['module_2'],
                 \OxidEsales\Eshop\Application\Model\User::class => $mockedModules['module_4'] . '&' . $mockedModules['module_3'],
             ],
             'expectedResult' => $mockedModules['module_3'],
             'message'        => 'oemodulethreeuser is the last class in the chain'
            ],
        ];
    }

    public function testDisableModuleReturnsTrueIfModuleWasInactiveBefore()
    {
        $moduleId = 'with_class_extensions';
        $this->installModule($moduleId);
        $moduleChainsGenerator = $this->getModuleChainsGenerator();
        $this->assertTrue($moduleChainsGenerator->disableModule($moduleId));
        $this->removeTestModule($moduleId);
    }

    public function testDisableModuleReturnsFalseIfModuleConfigurationIsNotInstalled()
    {
        $moduleChainsGenerator = $this->getModuleChainsGenerator();
        $this->assertFalse($moduleChainsGenerator->disableModule('non-existing-module'));
    }

    public function testGetModuleDirectoryByModuleId()
    {
        $this->installModule('with_class_extensions');

        $moduleChainsGenerator = $this->getModuleChainsGenerator();

        $this->assertEquals('notExistingModuleId', $moduleChainsGenerator->getModuleDirectoryByModuleId('notExistingModuleId'));
        $this->assertEquals('oeTest/with_class_extensions', $moduleChainsGenerator->getModuleDirectoryByModuleId('with_class_extensions'));

        ## Beware of upper / lower case
        $this->assertEquals('With_class_extensions', $moduleChainsGenerator->getModuleDirectoryByModuleId('With_class_extensions'));
        $this->removeTestModule('with_class_extensions');
    }

    private function installModule(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $installService = $container->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $installService->install($package);
    }

    private function activateTestModule(string $moduleId)
    {
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $container = ContainerFactory::getInstance()->getContainer();

        $container->get(ModuleInstallerInterface::class)
            ->install($package);

        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, Registry::getConfig()->getShopId());
    }

    private function deactivateTestModule(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->deactivate($moduleId, Registry::getConfig()->getShopId());
    }

    /**
     * @return \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator
     */
    private function getModuleChainsGenerator(): ModuleChainsGenerator
    {
        if (is_null($this->moduleChainsGenerator)) {
            $this->moduleChainsGenerator = new ModuleChainsGenerator(
                $this->createMock(ModuleVariablesLocator::class)
            );
        }

        return $this->moduleChainsGenerator;
    }

    private function removeTestModule(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $fileSystem = $container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($container->get(ContextInterface::class)->getModulesPath() . '/oeTest/' . $moduleId);
    }
}
