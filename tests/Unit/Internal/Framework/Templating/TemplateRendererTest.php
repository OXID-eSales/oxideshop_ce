<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRenderer;

class TemplateRendererTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderTemplate()
    {
        $response = 'rendered template';
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('render')
            ->with('template')
            ->will($this->returnValue($response));

        $renderer = new TemplateRenderer($engine);

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

        $renderer = new TemplateRenderer($engine);

        $this->assertSame($response, $renderer->renderFragment('template', 'testId', []));
    }

    public function testGetExistingEngine()
    {
        $engine = $this->getEngineMock();

        $renderer= new TemplateRenderer($engine);

        $this->assertSame($engine, $renderer->getTemplateEngine());
    }

    public function testExists()
    {
        $engine = $this->getEngineMock();
        $engine->expects($this->once())
            ->method('exists')
            ->with('template')
            ->will($this->returnValue(true));

        $renderer = new TemplateRenderer($engine);

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
}
