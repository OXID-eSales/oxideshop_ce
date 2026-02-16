<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRenderer;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TemplateRendererTest extends TestCase
{
    public function testRenderTemplateWithoutOptionalFileExtension(): void
    {
        $response = 'some template content';
        $templateName = 'some-template';
        $engine = $this->createMock(TemplateEngineInterface::class);
        $engine
            ->method('render')
            ->with($templateName)
            ->willReturn($response);

        $renderer = new TemplateRenderer($engine, $this->getContextStub());

        $this->assertSame($response, $renderer->renderTemplate($templateName, []));
    }

    #[DataProvider('twigTemplateNameFileDataProvider')]
    public function testRenderTemplateFilenameExtension($filename, $expectedFilename): void
    {
        $engine = $this->getEngineStub();
        $engine->method('render')
            ->willReturnCallback(function ($templateName) {
                return $templateName;
            });

        $renderer = new TemplateRenderer($engine, $this->getContextStub(), 'html.twig');

        $this->assertSame($expectedFilename, $renderer->renderTemplate($filename, []));
    }

    public function testRenderTemplateCallsRender(): void
    {
        $response = 'rendered template';
        $engine = $this->createMock(TemplateEngineInterface::class);
        $engine->expects($this->once())
            ->method('render')
            ->with('template.html.twig')
            ->willReturn($response);

        $renderer = new TemplateRenderer($engine, $this->getContextStub(), 'html.twig');

        $this->assertSame($response, $renderer->renderTemplate('template', []));
    }

    public function testRenderFragment()
    {
        $response = 'rendered template';
        $engine = $this->createMock(TemplateEngineInterface::class);
        $engine->expects($this->once())
            ->method('renderFragment')
            ->with('template')
            ->willReturn($response);

        $renderer = new TemplateRenderer($engine, $this->getContextStub(), 'html.twig');

        $this->assertSame($response, $renderer->renderFragment('template', 'testId', []));
    }

    public function testRenderFragmentIfDemoShop()
    {
        $engine = $this->createMock(TemplateEngineInterface::class);
        $engine->expects($this->never())
            ->method('renderFragment')
            ->with('template');

        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->once())
            ->method('isShopInDemoMode')
            ->willReturn(true);
        $renderer = new TemplateRenderer($engine, $context, 'html.twig');

        $this->assertSame('template', $renderer->renderFragment('template', 'testId', []));
    }

    public function testGetExistingEngine(): void
    {
        $engine = $this->getEngineStub();

        $renderer = new TemplateRenderer($engine, $this->getContextStub(), 'html.twig');

        $this->assertSame($engine, $renderer->getTemplateEngine());
    }

    public function testExistsWithoutOptionalFileExtension(): void
    {
        $templateName = 'some-template';
        $engine = $this->createMock(TemplateEngineInterface::class);
        $engine
            ->method('exists')
            ->with($templateName)
            ->willReturn(true);

        $renderer = new TemplateRenderer($engine, $this->getContextStub(), null);

        $this->assertTrue($renderer->exists($templateName));
    }

    public function testExists(): void
    {
        $templateName = 'template';
        $fileNameExtension = 'html.twig';
        $engine = $this->createMock(TemplateEngineInterface::class);
        $engine->expects($this->once())
            ->method('exists')
            ->with("$templateName.$fileNameExtension")
            ->willReturn(true);

        $renderer = new TemplateRenderer($engine, $this->getContextStub(), $fileNameExtension);

        $this->assertTrue($renderer->exists($templateName));
    }

    private function getContextStub(): ContextInterface
    {
        return $this->createStub(ContextInterface::class);
    }

    private function getEngineStub(): TemplateEngineInterface
    {
        return $this->createStub(TemplateEngineInterface::class);
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
