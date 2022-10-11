<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRenderer;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

class TemplateRendererTest extends TestCase
{
    public function testRenderTemplate()
    {
        $response = 'rendered template';
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('render')
            ->with('template')
            ->will($this->returnValue($response));

        $renderer = new TemplateRenderer($engine, $this->getContextMock());

        $this->assertSame($response, $renderer->renderTemplate('template', []));
    }

    public function testRenderFragment()
    {
        $response = 'rendered template';
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('renderFragment')
            ->with('template')
            ->will($this->returnValue($response));

        $renderer = new TemplateRenderer($engine, $this->getContextMock());

        $this->assertSame($response, $renderer->renderFragment('template', 'testId', []));
    }

    public function testRenderFragmentIfDemoShop()
    {
        $engine = $this->getEngineMock();
        $engine->expects($this->never())
            ->method('renderFragment')
            ->with('template');

        $context = $this->getContextMock();
        $context->expects($this->once())
            ->method('isShopInDemoMode')
            ->will($this->returnValue(true));
        $renderer = new TemplateRenderer($engine, $context);

        $this->assertSame('template', $renderer->renderFragment('template', 'testId', []));
    }

    public function testGetExistingEngine()
    {
        $engine = $this->getEngineMock();

        $renderer = new TemplateRenderer($engine, $this->getContextMock());

        $this->assertSame($engine, $renderer->getTemplateEngine());
    }

    public function testExists()
    {
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('exists')
            ->with('template')
            ->will($this->returnValue(true));

        $renderer = new TemplateRenderer($engine, $this->getContextMock());

        $this->assertTrue($renderer->exists('template'));
    }

    /**
     * @return \OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface
     */
    private function getEngineMock()
    {
        $engine = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface')
            ->getMock();

        return $engine;
    }

    private function getContextMock(): ContextInterface
    {
        return $this->getMockBuilder(ContextInterface::class)->getMock();
    }
}
