<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxException;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Module\ModuleTemplateBlockPathFormatter;
use OxidEsales\Eshop\Core\Module\ModuleTemplateBlockContentReader;

/**
 * @group module
 * @package Integration\Modules
 */
class ModuleTemplateBlockTest extends UnitTestCase
{
    public function testGetContentForModuleTemplateBlock()
    {
        $shopPath = implode(DIRECTORY_SEPARATOR, [__DIR__, 'TestData', 'shop']);
        $moduleId = 'oeTestTemplateBlockModuleId';

        $this->setConfigParam(
            'aModulePaths',
            [$moduleId => 'oe/testTemplateBlockModuleId']
        );

        $pathFormatter = oxNew(ModuleTemplateBlockPathFormatter::class);
        $pathFormatter->setModulesPath($shopPath . DIRECTORY_SEPARATOR . 'modules');
        $pathFormatter->setModuleId($moduleId);
        $pathFormatter->setFileName('blocks/blocktemplate.tpl');

        $blockContentGetter = oxNew(ModuleTemplateBlockContentReader::class);
        $actualContent = $blockContentGetter->getContent($pathFormatter);

        $expectedContent = 'block template content';
        $this->assertSame($expectedContent, $actualContent);
    }

    public function testThrowExcpetionWhenModuleTemplateBlockFileDoesNotExist()
    {
        $this->expectException(oxException::class);

        $shopPath = implode(DIRECTORY_SEPARATOR, [__DIR__, 'TestData', 'shop']);
        $moduleId = 'oeTestTemplateBlockModuleId';

        $this->setConfigParam(
            'aModulePaths',
            [$moduleId => 'oe/testTemplateBlockModuleId']
        );

        $pathFormatter = oxNew(ModuleTemplateBlockPathFormatter::class);
        $pathFormatter->setModulesPath($shopPath . DIRECTORY_SEPARATOR . 'modules');
        $pathFormatter->setModuleId($moduleId);
        $pathFormatter->setFileName('blocks/blocktemplate_notExist.tpl');

        $blockContentReader = oxNew(ModuleTemplateBlockContentReader::class);
        $blockContentReader->getContent($pathFormatter);
    }
}
