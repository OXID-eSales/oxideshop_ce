<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Module\ModuleTemplatePathCalculator;

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
    public function calculateModuleTemplatePathDataProvider()
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
     * Data provider for testCalculateModuleTemplatePathExceptions
     */
    public function calculateModuleTemplatePathExceptionsDataProvider()
    {
        return [
            ['fifth.tpl', '', 'azure', null],
            ['sixth.tpl', '', 'azure', null],
            ['sixth.tpl', '', 'firstTheme', null],
        ];
    }

    /**
     * Test if correct path to template will be calculated with different theme configurations
     *
     * @dataProvider calculateModuleTemplatePathDataProvider
     */
    public function testCalculateModuleTemplatePath($templateName, $expectedPath, $configTheme, $configCustomTheme)
    {
        $calculator = $this->getModuleTemplatePathCalculator($this->pathToModules, $configTheme, $configCustomTheme);
        $this->assertSame($expectedPath, $calculator->calculateModuleTemplatePath($templateName));
    }

    /**
     * Test if Exceptions will be thrown if no templates by name and theme configurations will be found
     *
     * @dataProvider calculateModuleTemplatePathExceptionsDataProvider
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

        // deactivate modules
        $moduleListStub = $this->getMock(oxModuleList::class, ['getActiveModuleInfo']);
        $moduleListStub->method('getActiveModuleInfo')->willReturn([]);

        // configure Config to return false on searching shop templates
        $configStub = $this->getMock('oxConfig', ['getDir']);

        $templatePathCalculator = $this->getMock(ModuleTemplatePathCalculator::class, ['getConfig', 'getModuleList']);
        $templatePathCalculator->setModulesPath($this->pathToModules);
        $templatePathCalculator->method('getConfig')->will($this->returnValue($configStub));
        $templatePathCalculator->method('getModuleList')->will($this->returnValue($moduleListStub));

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
        $this->setExpectedException('oxException');

        // activate this virtual module
        $moduleListStub = $this->getMock(oxModuleList::class, ['getActiveModuleInfo']);
        $moduleListStub->method('getActiveModuleInfo')->willReturn([$this->exampleModuleId => true]);

        $configStub = $this->getMock(oxConfig::class, ['getModulesDir', 'init', 'getDir']);
        $configStub->setConfigParam('aModuleTemplates', $this->exampleModuleTemplateConfiguration);

        $templatePathCalculator = $this->getMock(ModuleTemplatePathCalculator::class, ['getConfig', 'getModuleList']);
        $templatePathCalculator->method('getConfig')->will($this->returnValue($configStub));
        $templatePathCalculator->method('getModuleList')->will($this->returnValue($moduleListStub));

        try {
            $templatePathCalculator->calculateModuleTemplatePath('first.tpl');
        } catch (Exception $e) {
            $this->assertRegExp("@^Cannot find template file.*?@i", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Return testable object.
     *
     * @param string $modulesPath
     *
     * @return ModuleTemplatePathCalculator
     */
    private function getModuleTemplatePathCalculator($modulesPath, $configTheme, $configCustomTheme)
    {
        // activate this virtual module
        $moduleListStub = $this->getMock(oxModuleList::class, ['getActiveModuleInfo']);
        $moduleListStub->method('getActiveModuleInfo')->willReturn([$this->exampleModuleId => true]);

        // configure Config :)
        $configStub = $this->getMock(oxConfig::class, ['getModulesDir', 'init', 'checkIfReadable', 'getDir']);
        $configStub->method('checkIfReadable')->willReturn($this->returnValue(true));
        $configStub->setConfigParam('aModuleTemplates', $this->exampleModuleTemplateConfiguration);

        $templatePathCalculator = $this->getMock(ModuleTemplatePathCalculator::class, ['getConfig', 'getModuleList']);
        $templatePathCalculator->setModulesPath($modulesPath);
        $templatePathCalculator->method('getConfig')->will($this->returnValue($configStub));
        $templatePathCalculator->method('getModuleList')->will($this->returnValue($moduleListStub));

        $configTheme && $configStub->setConfigParam('sTheme', $configTheme);
        $configCustomTheme && $configStub->setConfigParam('sCustomTheme', $configCustomTheme);

        return $templatePathCalculator;
    }
}
