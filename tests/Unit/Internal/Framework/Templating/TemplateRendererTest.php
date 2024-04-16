<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRenderer;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class TemplateRendererTest extends TestCase
{
    #[DataProvider('twigTemplateNameFileDataProvider')]
    public function testRenderTemplateFilenameExtension(string $filename, string $expectedFilename): void
    {
        $engine = $this->getEngineMock();
        $engine->method('render')
            ->willReturnCallback(function ($templateName) {
                return $templateName;
            });

        $renderer = new TemplateRenderer($engine, $this->getContextMock(), 'html.twig');

        $this->assertSame($expectedFilename, $renderer->renderTemplate($filename, []));
    }

    public function testRenderTemplateCallsRender(): void
    {
        $response = 'rendered template';
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('render')
            ->with('template.html.twig')
            ->willReturn($response);

        $renderer = new TemplateRenderer($engine, $this->getContextMock(), 'html.twig');

        $this->assertSame($response, $renderer->renderTemplate('template', []));
    }

    public function testRenderFragment(): void
    {
        $response = 'rendered template';
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('renderFragment')
            ->with('template')
            ->willReturn($response);

        $renderer = new TemplateRenderer($engine, $this->getContextMock(), 'html.twig');

        $this->assertSame($response, $renderer->renderFragment('template', 'testId', []));
    }

    public function testRenderFragmentIfDemoShop(): void
    {
        $engine = $this->getEngineMock();
        $engine->expects($this->never())
            ->method('renderFragment')
            ->with('template');

        $context = $this->getContextMock();
        $context->expects($this->once())
            ->method('isShopInDemoMode')
            ->willReturn(true);
        $renderer = new TemplateRenderer($engine, $context, 'html.twig');

        $this->assertSame('template', $renderer->renderFragment('template', 'testId', []));
    }

    public function testGetExistingEngine(): void
    {
        $engine = $this->getEngineMock();

        $renderer = new TemplateRenderer($engine, $this->getContextMock(), 'html.twig');

        $this->assertSame($engine, $renderer->getTemplateEngine());
    }

    public function testExists(): void
    {
        $templateName = 'template';
        $fileNameExtension = 'html.twig';
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('exists')
            ->with("$templateName.$fileNameExtension")
            ->willReturn(true);

        $renderer = new TemplateRenderer($engine, $this->getContextMock(), $fileNameExtension);

        $this->assertTrue($renderer->exists($templateName));
    }

    private function getContextMock(): ContextInterface
    {
        return $this->getMockBuilder(ContextInterface::class)->getMock();
    }

    private function getEngineMock(): TemplateEngineInterface
    {
        return $this
            ->getMockBuilder(TemplateEngineInterface::class)
            ->getMock();
    }

    public static function twigTemplateNameFileDataProvider(): array
    {
        return [
            [
                'template',
                'template.html.twig',
            ],
            [
                'template.html.twig',
                'template.html.twig'
            ],
            [
                'some/path/template_name.html.twig',
                'some/path/template_name.html.twig'
            ],
            [
                'some/path/template.name.html.twig',
                'some/path/template.name.html.twig'
            ],
            [
                'some/path/template.name',
                'some/path/template.name.html.twig'
            ],
        ];
    }
}
