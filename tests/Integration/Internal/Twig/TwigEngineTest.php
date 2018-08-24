<?php
/**
 * Created by PhpStorm.
 * User: jskoczek
 * Date: 21.08.18
 * Time: 15:34
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Twig\TwigEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;
use OxidEsales\EshopCommunity\Internal\Twig\TemplateEngineConfigurationInterface;
use Symfony\Component\Templating\TemplateReference;
use Twig\Environment;
use Twig_Loader;

class TwigEngineTest extends \PHPUnit\Framework\TestCase
{

    private $templateDir;
    private $templateDirPath;
    private $template;

    protected function setUp()
    {
        parent::setUp();
        $templateDir = vfsStream::setup($this->getTemplateDir());
        $this->template = vfsStream::newFile($this->getTemplateName())->at($templateDir)->setContent("{{ 'twig' }}")->url();
        $this->templateDir = $templateDir;
        $this->templateDirPath = vfsStream::url($this->getTemplateDir());
    }

    public function testExists()
    {
        $engine = $this->getEngine();
        $this->assertTrue($engine->exists($this->getTemplateName()));
        $this->assertFalse($engine->exists('foo'));
    }

    public function testSupports()
    {
        $engine = $this->getEngine();
        $this->assertTrue($engine->supports($this->getTemplateName()));
        $engine = $this->getEngine('bar');
        $this->assertFalse($engine->supports($this->getTemplateName()));
    }

    public function testAddGlobal()
    {
        $engine = $this->getEngine();
        $engine->addGlobal('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $engine->getGlobals());
        $this->assertNotEquals(['not_foo' => 'not_bar'], $engine->getGlobals());
    }

    public function testRender()
    {
        $engine = $this->getEngine();
        $this->assertTrue(file_exists($this->template));
        $rendered = $engine->render($this->getTemplateName());
        $this->assertEquals('twig', $rendered);
        $this->assertNotEquals('foo', $rendered);
    }

    private function getEngine($engine_type = 'twig')
    {
        /** @var TemplateNameParserInterface $parser */
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();
        $parser->method('parse')->willReturn(new TemplateReference($this->getTemplateName(), $engine_type));

        /** @var TemplateEngineConfigurationInterface $configuration */
        $configuration = $this->getMockBuilder('OxidEsales\EshopCommunity\Internal\Twig\TemplateEngineConfigurationInterface')->getMock();
        $configuration->method('getParameters')->willReturn(['template_dir' => [$this->templateDirPath], 'is_debug' => 'false', 'cache_dir' => 'foo']);

        $loader = new \Twig_Loader_Filesystem($this->templateDirPath);

        $engine = new Environment($loader);

        return new TwigEngine($engine, $parser);
    }

    private function getTemplateName()
    {
        return 'testTwigTemplate.twig';
    }

    private function getTemplateDir()
    {
        return 'testTemplateDir';
    }

}
