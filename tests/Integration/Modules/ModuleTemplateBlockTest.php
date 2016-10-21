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
namespace Integration\Modules;

use oxException;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Module\ModuleTemplateBlockPathFormatter;
use OxidEsales\EshopCommunity\Core\Module\ModuleTemplateBlockContentReader;

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
        $pathFormatter->setModulesPath($shopPath . DIRECTORY_SEPARATOR . modules);
        $pathFormatter->setModuleId($moduleId);
        $pathFormatter->setFileName('blocks/blocktemplate.tpl');

        $blockContentGetter = oxNew(ModuleTemplateBlockContentReader::class);
        $actualContent = $blockContentGetter->getContent($pathFormatter);

        $expectedContent = 'block template content';
        $this->assertSame($expectedContent, $actualContent);
    }

    public function testThrowExcpetionWhenModuleTemplateBlockFileDoesNotExist()
    {
        $this->setExpectedException(oxException::class);

        $shopPath = implode(DIRECTORY_SEPARATOR, [__DIR__, 'TestData', 'shop']);
        $moduleId = 'oeTestTemplateBlockModuleId';

        $this->setConfigParam(
            'aModulePaths',
            [$moduleId => 'oe/testTemplateBlockModuleId']
        );

        $pathFormatter = oxNew(ModuleTemplateBlockPathFormatter::class);
        $pathFormatter->setModulesPath($shopPath . DIRECTORY_SEPARATOR . modules);
        $pathFormatter->setModuleId($moduleId);
        $pathFormatter->setFileName('blocks/blocktemplate_notExist.tpl');

        $blockContentReader = oxNew(ModuleTemplateBlockContentReader::class);
        $blockContentReader->getContent($pathFormatter);
    }
}
