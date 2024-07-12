<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\TestingLibrary\VfsStreamWrapper;
use PHPUnit\Framework\TestCase;

final class GenExportDoTest extends TestCase
{
    public function testNextTickNoMoreArticleFound(): void
    {
        $controller = $this
            ->createPartialMock(
                GenericExportDo::class,
                ['getOneArticle', 'write']
            );
        $controller
            ->expects($this->once())
            ->method('getOneArticle')
            ->willReturn(false);
        $controller
            ->expects($this->never())
            ->method('write');

        $this->assertFalse($controller->nextTick(1));
    }

    public function testNextTick(): void
    {
        $product = oxNew(Article::class);
        $parameters = [
            'sCustomHeader' => '',
            'linenr' => 1,
            'article' => $product,
            'spr' => Registry::getConfig()->getConfigParam('sCSVSign'),
            'encl' => Registry::getConfig()->getConfigParam('sGiCsvFieldEncloser'),
            'oxEngineTemplateId' => 'dyn_interface'
        ];
        $templateRenderer = $this
            ->createPartialMock(
                TemplateRendererInterface::class,
                ['renderTemplate', 'renderFragment', 'getTemplateEngine', 'exists']
            );
        $templateRenderer
            ->method('renderTemplate')
            ->with(
                'genexport',
                $parameters
            );

        $templateRendererBridge = $this
            ->createPartialMock(
                TemplateRendererBridgeInterface::class,
                ['setEngine', 'getEngine', 'getTemplateRenderer']
            );
        $templateRendererBridge
            ->method('getTemplateRenderer')
            ->willReturn($templateRenderer);

        $controller = $this
            ->createPartialMock(
                GenericExportDo::class,
                ['getOneArticle', 'write', 'getViewId', 'getService']
            );
        $controller
            ->expects($this->once())
            ->method('getOneArticle')
            ->willReturn($product);
        $controller
            ->expects($this->once())
            ->method('write');
        $controller
            ->expects($this->once())
            ->method('getViewId')
            ->willReturn('dyn_interface');
        $controller
            ->method('getService')
            ->willReturn($templateRendererBridge);

        $this->assertSame(
            2,
            $controller->nextTick(1)
        );
    }

    public function testWrite(): void
    {
        $controller = oxNew(GenericExportDo::class);
        $someContents = uniqid('TestExport-', true);
        $testFile = (new VfsStreamWrapper())
            ->createFile('test.txt');

        $controller->fpFile = fopen($testFile, 'wb');
        $controller->write($someContents);
        fclose($controller->fpFile);

        $this->assertSame(
            $someContents . PHP_EOL,
            file_get_contents($testFile, true)
        );
    }

    public function testRender(): void
    {
        $this->assertSame(
            'dynbase_do',
            oxNew(GenericExportDo::class)
                ->render()
        );
    }
}
