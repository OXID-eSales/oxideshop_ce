<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;

class SmartyEngineTests extends \PHPUnit\Framework\TestCase
{

    public function testExists()
    {
        $templateDir = vfsStream::setup('testTemplateDir');
        $template = vfsStream::newFile('testSmartyTemplate.tpl')->at($templateDir)->url();

        $engine = $this->getEngine();

        $this->assertTrue($engine->exists($template));
    }

    public function testExistsWithNonExistentTemplates()
    {
        $engine = $this->getEngine();

        $this->assertFalse($engine->exists('foobar'));
    }

    public function testRender()
    {
        $templateDir = vfsStream::setup('testTemplateDir');
        $template = vfsStream::newFile('testSmartyTemplate.tpl')
            ->at($templateDir)
            ->setContent("The new contents of the file")
            ->url();

        $engine = $this->getEngine();

        $this->assertTrue(file_exists($template));
        $this->assertSame('foo', $engine->render($template));
    }

    private function getEngine()
    {
        /** @var TemplateNameParserInterface $parser */
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();

        return new SmartyEngine(new \Smarty(), $parser);
    }
}