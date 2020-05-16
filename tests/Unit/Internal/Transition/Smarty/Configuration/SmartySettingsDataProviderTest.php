<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartySettingsDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;
use PHPUnit\Framework\TestCase;

class SmartySettingsDataProviderTest extends TestCase
{
    public function testGetSmartySettings()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $dataProvider = new SmartySettingsDataProvider($smartyContextMock);
        $settings = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => 'testCompileDir',
            'cache_dir' => 'testCompileDir',
            'template_dir' => ['testTemplateDir'],
            'compile_id' => '7f96e0d92070fd4733296e5118fd5a01',
            'default_template_handler_func' => [Registry::getUtilsView(), '_smartyDefaultTemplateHandler'],
            'debugging' => true,
            'compile_check' => true,
            'php_handling' => 1,
            'security' => false
        ];

        $this->assertEquals($settings, $dataProvider->getSettings());
    }

    private function getSmartyContextMock(): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateEngineDebugMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplateCompileDirectory')
            ->willReturn('testCompileDir');

        $smartyContextMock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        $smartyContextMock
            ->method('getTemplateCompileCheckMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplatePhpHandlingMode')
            ->willReturn(1);

        $smartyContextMock
            ->method('getTemplateCompileId')
            ->willReturn('7f96e0d92070fd4733296e5118fd5a01');

        $smartyContextMock
            ->method('getSmartyPluginDirectories')
            ->willReturn(['testModuleDir', 'testShopPath/Core/Smarty/Plugin']);

        return $smartyContextMock;
    }
}
