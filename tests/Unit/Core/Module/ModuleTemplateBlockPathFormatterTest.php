<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Module\ModuleTemplateBlockPathFormatter;

class ModuleTemplateBlockPathFormatterTest extends UnitTestCase
{
    public function testCanCreateClass()
    {
        oxNew(ModuleTemplateBlockPathFormatter::class);
    }

    public function providerGetPathThrowExceptionWhenNoParametersAreSet()
    {
        return [
            [null, 'filePathForBlock'],
            ['myTestmodule', null],
            [null, null],
        ];
    }

    /**
     * @param $moduleId
     * @param $fileName
     *
     * @dataProvider providerGetPathThrowExceptionWhenNoParametersAreSet
     */
    public function testGetPathThrowExceptionWhenNoParametersAreSet($moduleId, $fileName)
    {
        $this->setExpectedException('oxException');

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);

        $pathFormatter->getPath();
    }

    public function testGetPathDoesNotThrowExceptionWhenParametersAreSet()
    {
        $pathFormatter = $this->getModuleTemplateBlockPathFormatter('myTestModule', 'filePathForBlock');
        $this->stubActiveModulesList();

        $pathFormatter->getPath();
    }

    public function providerGetPathWhenFileContainsValueFromModuleRoot()
    {
        return [
            ['myTestModule', 'myTestModulePath', 'pathToFile/filePathForBlock.tpl', 'pathToShop/modules/myTestModulePath/pathToFile/filePathForBlock.tpl'],
            ['myTestModule', 'myTestModulePath', 'pathToFile/filePathForBlock', 'pathToShop/modules/myTestModulePath/pathToFile/filePathForBlock'],
            ['myTestModule2', 'myTestModulePath2', 'pathToFile/filePathForBlock.tpl', 'pathToShop/modules/myTestModulePath2/pathToFile/filePathForBlock.tpl'],
            ['myTestModule', 'myTestModulePath', 'pathToFile/filePathForBlock2.tpl', 'pathToShop/modules/myTestModulePath/pathToFile/filePathForBlock2.tpl'],
        ];
    }

    /**
     * To test case when file contains path from module root:
     * as defined in metadata 1.1 and above.
     *
     * @param $moduleId
     * @param $modulePath
     * @param $fileName
     * @param  $expectedFullPathToFile
     *
     * @dataProvider providerGetPathWhenFileContainsValueFromModuleRoot
     */
    public function testGetPathWhenFileContainsValueFromModuleRoot($moduleId, $modulePath, $fileName, $expectedFullPathToFile)
    {
        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);
        $this->stubActiveModulesList([$moduleId => $modulePath]);

        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
    }

    public function testGetPathWhenFileContainsOnlyFileName()
    {
        $moduleId = 'myTestModule';
        $fileName = 'filePathForBlock.tpl';

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);
        $this->stubActiveModulesList();

        $expectedFullPathToFile = 'pathToShop/modules/myTestModulePath/out/blocks/filePathForBlock.tpl';
        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
    }

    public function testGetPathWhenFileNameDoesNotContainAnExtension()
    {
        $moduleId = 'myTestModule';
        $fileName = 'filePathForBlock';

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);
        $this->stubActiveModulesList();

        $expectedFullPathToFile = 'pathToShop/modules/myTestModulePath/out/blocks/filePathForBlock.tpl';
        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
    }

    public function testGetPathForDifferentShopDirectory()
    {
        $moduleId = 'myTestModule';
        $fileName = 'pathToFile/filePathForBlock.tpl';

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName, 'differentShopPath/modules');
        $this->stubActiveModulesList();

        $expectedFullPathToFile = 'differentShopPath/modules/myTestModulePath/pathToFile/filePathForBlock.tpl';
        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
    }

    public function testGetPathThrowExceptionIfModuleIsNotActiveOrIsNotAvailable()
    {
        $this->setExpectedException('oxException');

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter('myTestModule', 'pathToFile/filePathForBlock.tpl');
        $pathFormatter->getPath();
    }

    /**
     * Return testable object.
     *
     * @param string $moduleId
     * @param string $fileName
     * @param string $modulesPath
     *
     * @return ModuleTemplateBlockPathFormatter
     */
    private function getModuleTemplateBlockPathFormatter($moduleId, $fileName, $modulesPath = 'pathToShop/modules')
    {
        $pathFormatter = oxNew(ModuleTemplateBlockPathFormatter::class);
        $pathFormatter->setModuleId($moduleId);
        $pathFormatter->setFileName($fileName);
        $pathFormatter->setModulesPath($modulesPath);

        return $pathFormatter;
    }

    /**
     * Force system to Show module as active for provided module id list.
     *
     * @param array $activeModules
     */
    private function stubActiveModulesList($activeModules = ['myTestModule' => 'myTestModulePath'])
    {
        $moduleListMock = $this->getMock('oxmodulelist', ['getActiveModuleInfo']);
        $moduleListMock->method('getActiveModuleInfo')->willReturn($activeModules);
        oxTestModules::addModuleObject('oxmodulelist', $moduleListMock);
    }
}
