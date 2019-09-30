<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge\SmartyEngineBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyEngine;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SmartyEngineTest extends \PHPUnit\Framework\TestCase
{

    public function testExists()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

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
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertTrue(file_exists($template));
        $this->assertSame('Hello OXID!', $engine->render($template));
    }

    public function testRenderWithContext()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertTrue(file_exists($template));
        $this->assertSame('Hello Test!', $engine->render($template, ['title' => 'Hello Test!']));
    }

    public function testRenderWithCacheId()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();
        $context = ['title' => 'Hello Test!', 'oxEngineTemplateId' => md5('smartyTemplate.tpl')];

        $this->assertTrue(file_exists($template));
        $this->assertSame('Hello Test!', $engine->render($template, $context));
        $this->assertSame('Hello Test!', $engine->render($template, $context));
    }

    public function testGetDefaultFileExtension()
    {
        $engine = $this->getEngine();
        $this->assertSame('tpl', $engine->getDefaultFileExtension());
    }

    public function testAddAndGetGlobals()
    {
        $engine = $this->getEngine();
        $engine->addGlobal('testGlobal', 'testValue');
        $this->assertSame(['testGlobal' => 'testValue'], $engine->getGlobals());
        $this->assertSame('testValue', $engine->_tpl_vars['testGlobal']);
    }

    public function testRenderFragment()
    {
        $fragment = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}][{$title}]';
        $context = ['title' => 'Hello Test!'];

        $factory = $this->getCompiledTestContainer();
        $engine = $factory->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame('Hello Test!', $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testMagicSetterAndGetter()
    {
        $factory = $this->getCompiledTestContainer();
        $engine = $factory->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $engine->_tpl_vars = 'testValue';
        $this->assertSame('testValue', $engine->_tpl_vars);
    }

    private function getEngine()
    {
        $smarty = new \Smarty();
        $smarty->compile_dir = sys_get_temp_dir();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';
        return new SmartyEngine($smarty, new SmartyEngineBridge());
    }

    private function getTemplateDirectory()
    {
        return __DIR__ . '/Fixtures/';
    }

    /**
     * @return ContainerBuilder
     */
    private function getCompiledTestContainer(): ContainerBuilder
    {
        $container = (new TestContainerFactory())->create();
        $container->compile();

        return $container;
    }
}
