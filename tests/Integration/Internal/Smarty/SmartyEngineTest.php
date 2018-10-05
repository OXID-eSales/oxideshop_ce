<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;

class SmartyEngineTest extends \PHPUnit\Framework\TestCase
{
    private $templateFilePath;
    private $tmpPath;

    public function setUp()
    {
        parent::setUp();

        $this->tmpPath = $this->getTmpDirectory();
        if (!is_dir($this->tmpPath)) {
            mkdir($this->tmpPath);
        }
        $this->templateFilePath = $this->getTemplateDirectory() . 'smartyTemplate.tpl';
    }

    public function tearDown()
    {
        array_map('unlink', glob($this->tmpPath."*"));
        rmdir($this->tmpPath);

        parent::tearDown();
    }

    public function testExists()
    {
        $engine = $this->getEngine();

        $this->assertTrue($engine->exists($this->templateFilePath));
    }

    public function testExistsWithNonExistentTemplates()
    {
        $engine = $this->getEngine();

        $this->assertFalse($engine->exists('foobar'));
    }

    public function testRender()
    {
        $engine = $this->getEngine();

        $this->assertSame("Hello OXID!", $engine->render($this->templateFilePath));
    }

    public function testRenderWithGlobalParameters()
    {
        $engine = $this->getEngine();
        $engine->addGlobal('title', 'Hello Global!');

        $this->assertSame("Hello Global!", $engine->render($this->templateFilePath));
    }

    public function testRenderWithParameters()
    {
        $engine = $this->getEngine();

        $this->assertSame("Hello World!", $engine->render($this->templateFilePath, ['title' => 'Hello World!']));
    }

    /**
     * @dataProvider supportDataProvider
     *
     * @param $templateName
     * @param $result
     */
    public function testSupport($templateName, $result)
    {
        $engine = $this->getEngine();

        $this->assertSame($result, $engine->supports($templateName));
    }

    public function supportDataProvider()
    {
        return [
            ['smartyTemplate.tpl', true],
            ['smartyTemplate.smarty', true],
            ['twigTemplate.twig', false],
            ['phpTemplate.php', false],
        ];
    }

    private function getEngine()
    {
        /** @var TemplateNameParserInterface $parser */
        $parser = new \Symfony\Component\Templating\TemplateNameParser();
        $smarty = new \Smarty();
        $smarty->caching = false;
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';
        $smarty->compile_dir =  $this->tmpPath;
        $smarty->template_dir = $this->getTemplateDirectory();
        return new SmartyEngine($smarty, $parser);
    }

    private function getTemplateDirectory()
    {
        return __DIR__ . '/Fixtures/';
    }

    private function getTmpDirectory()
    {
        return sys_get_temp_dir() . '/test_smarty/';
    }
}