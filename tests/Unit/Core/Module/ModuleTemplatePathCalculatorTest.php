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
namespace Unit\Core\Module;

use Exception;
use OxidEsales\EshopCommunity\Core\FileSystem\FileSystem;
use OxidEsales\EshopCommunity\Core\Module\ModuleTemplatePathCalculator;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxModuleList;
use PHPUnit_Framework_MockObject_MockObject;

class ModuleTemplatePathFormatterTest extends UnitTestCase
{
    /**
     * Full path to modules directory. Any path like string for testing purposes.
     *
     * @var string
     */
    protected $pathToModules = '/pathToModules/';

    /**
     * Example module id to use in mocking module configurations.
     *
     * @var string
     */
    protected $exampleModuleId = 'moduleId';

    /**
     * Example module templates configuration.
     *
     * @var array
     */
    protected $exampleModuleTemplateConfiguration = [
        'moduleId' => [
            'first.tpl' => 'test_path/first_default.tpl',
            'second.tpl' => 'test_path/second_default.tpl',
            'third.tpl' => 'test_path/third_default.tpl',
            'fourth.tpl' => 'test_path/fourth_default.tpl',

            'firstTheme' => [
                'first.tpl' => 'test_path/first_firstTheme.tpl',
                'second.tpl' => 'test_path/second_firstTheme.tpl',
                'fifth.tpl' => 'test_path/fifth_firstTheme.tpl',
            ],
            'secondTheme' => [
                'first.tpl' => 'test_path/first_secondTheme.tpl',
                'third.tpl' => 'test_path/third_secondTheme.tpl',
                'sixth.tpl' => 'test_path/sixth_secondTheme.tpl',
            ]
        ]
    ];

    /**
     * Check if Class can be loaded with default shop methods.
     */
    public function testCanCreateClass()
    {
        oxNew(ModuleTemplatePathCalculator::class);
    }

    /**
     * Data provider for testCalculateModuleTemplatePath
     */
    public function providerCalculateModuleTemplatePath()
    {
        return [
            ['first.tpl', $this->pathToModules . 'test_path/first_default.tpl', null, null],
            ['first.tpl', $this->pathToModules . 'test_path/first_default.tpl', 'azure', null],
            ['first.tpl', $this->pathToModules . 'test_path/first_firstTheme.tpl', 'firstTheme', null],
            ['first.tpl', $this->pathToModules . 'test_path/first_secondTheme.tpl', 'secondTheme', null],
            ['first.tpl', $this->pathToModules . 'test_path/first_secondTheme.tpl', 'firstTheme', 'secondTheme'],

            ['second.tpl', $this->pathToModules . 'test_path/second_default.tpl', 'azure', null],
            ['second.tpl', $this->pathToModules . 'test_path/second_firstTheme.tpl', 'firstTheme', null],
            ['second.tpl', $this->pathToModules . 'test_path/second_firstTheme.tpl', 'firstTheme', 'secondTheme'],

            ['third.tpl', $this->pathToModules . 'test_path/third_default.tpl', 'azure', null],
            ['third.tpl', $this->pathToModules . 'test_path/third_default.tpl', 'firstTheme', null],
            ['third.tpl', $this->pathToModules . 'test_path/third_secondTheme.tpl', 'firstTheme', 'secondTheme'],

            ['fourth.tpl', $this->pathToModules . 'test_path/fourth_default.tpl', 'azure', null],
            ['fourth.tpl', $this->pathToModules . 'test_path/fourth_default.tpl', 'firstTheme', null],
            ['fourth.tpl', $this->pathToModules . 'test_path/fourth_default.tpl', 'firstTheme', 'secondTheme'],

            ['fifth.tpl', $this->pathToModules . 'test_path/fifth_firstTheme.tpl', 'firstTheme', null],
            ['fifth.tpl', $this->pathToModules . 'test_path/fifth_firstTheme.tpl', 'firstTheme', 'secondTheme'],

            ['sixth.tpl', $this->pathToModules . 'test_path/sixth_secondTheme.tpl', 'firstTheme', 'secondTheme'],
        ];
    }

    /**
     * Test if correct path to template will be calculated with different theme configurations
     *
     * @dataProvider providerCalculateModuleTemplatePath
     */
    public function testCalculateModuleTemplatePath($templateName, $expectedPath, $configTheme, $configCustomTheme)
    {
        $calculator = $this->getModuleTemplatePathCalculator($this->pathToModules, $configTheme, $configCustomTheme);
        $this->assertSame($expectedPath, $calculator->calculateModuleTemplatePath($templateName));
    }

    /**
     * Data provider for testCalculateModuleTemplatePathExceptions
     */
    public function providerCalculateModuleTemplatePathExceptions()
    {
        return [
            ['fifth.tpl', '', 'azure', null],
            ['sixth.tpl', '', 'azure', null],
            ['sixth.tpl', '', 'firstTheme', null],
        ];
    }

    /**
     * Test if Exceptions will be thrown if no templates by name and theme configurations will be found
     *
     * @dataProvider providerCalculateModuleTemplatePathExceptions
     */
    public function testCalculateModuleTemplatePathExceptions($templateName, $expectedPath, $configTheme, $configCustomTheme)
    {
        $this->setExpectedException('oxException');

        $calculator = $this->getModuleTemplatePathCalculator($this->pathToModules, $configTheme, $configCustomTheme);
        $calculator->calculateModuleTemplatePath($templateName);
    }

    /**
     * Test if exception of not found template will be thrown if modules to search templates in are not active
     */
    public function testCalculateModuleTemplatePathWithNoActiveModules()
    {
        $this->setExpectedException('oxException');

        /** @var oxModuleList|PHPUnit_Framework_MockObject_MockObject $moduleListMock */
        $moduleListMock = $this->getMock(oxModuleList::class, ['getActiveModuleInfo']);
        $moduleListMock->method('getActiveModuleInfo')->willReturn([]);

        $templatePathCalculator = new ModuleTemplatePathCalculator($moduleListMock);
        $templatePathCalculator->setModulesPath($this->pathToModules);

        try {
            $templatePathCalculator->calculateModuleTemplatePath('someTemplateName.tpl');
        } catch (Exception $e) {
            $this->assertRegExp("@^Cannot find template@i", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Test if exception of template file not exists will be thrown if no such template file found
     */
    public function testCalculateModuleTemplatePathFileNotExists()
    {
        $this->setExpectedException('oxException', 'Cannot find template file "/test_path/first_default.tpl"');

        /** @var oxModuleList|PHPUnit_Framework_MockObject_MockObject $moduleListMock */
        $moduleListMock = $this->getMock(oxModuleList::class, ['getActiveModuleInfo']);
        $moduleListMock->method('getActiveModuleInfo')->willReturn([$this->exampleModuleId => true]);

        $this->setConfigParam('aModuleTemplates', $this->exampleModuleTemplateConfiguration);

        $templatePathCalculator = new ModuleTemplatePathCalculator($moduleListMock);
        $templatePathCalculator->calculateModuleTemplatePath('first.tpl');
    }

    /**
     * Return testable object.
     *
     * @param string $modulesPath
     * @param string $configTheme
     * @param string $configCustomTheme
     *
     * @return ModuleTemplatePathCalculator
     */
    private function getModuleTemplatePathCalculator($modulesPath, $configTheme, $configCustomTheme)
    {
        $this->setConfigParam('aModuleTemplates', $this->exampleModuleTemplateConfiguration);
        $this->setConfigParam('sTheme', $configTheme);
        $this->setConfigParam('sCustomTheme', $configCustomTheme);

        /** @var oxModuleList|PHPUnit_Framework_MockObject_MockObject $moduleListMock */
        $moduleListMock = $this->getMock(oxModuleList::class, ['getActiveModuleInfo']);
        $moduleListMock->method('getActiveModuleInfo')->willReturn([$this->exampleModuleId => true]);

        /** @var FileSystem|PHPUnit_Framework_MockObject_MockObject $fileSystemMock */
        $fileSystemMock = $this->getMock(FileSystem::class, ['isReadable']);
        $fileSystemMock->method('isReadable')->willReturn($this->returnValue(true));

        $templatePathCalculator = new ModuleTemplatePathCalculator($moduleListMock, oxNew('oxTheme'), $fileSystemMock);
        $templatePathCalculator->setModulesPath($modulesPath);

        return $templatePathCalculator;
    }
}
