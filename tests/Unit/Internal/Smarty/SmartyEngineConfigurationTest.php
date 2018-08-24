<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty;


use OxidEsales\EshopCommunity\Internal\Smarty\Extension\CacheResourcePlugin;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\SmartyDefaultTemplateHandler;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngineConfiguration;

class SmartyEngineConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetOptions()
    {
        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock();
        $options = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => 'testCompileDir',
            'cache_dir' => 'testCompileDir',
            'template_dir' => 'testTemplateDir',
            'compile_id' => 'testCompileId',
            'default_template_handler_func' => [new SmartyDefaultTemplateHandler($smartyContextMock), 'handleTemplate'],
            'debugging' => '2',
            'compile_check' => true
        ];

        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertEquals($options, $configuration->getOptions());
    }

    public function testGetSecurityOptionsIfOff()
    {
        $options = [
            'php_handling' => 1,
            'security' => false
        ];

        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertSame($options, $configuration->getSecurityOptions());
    }

    public function testGetSecurityOptionsIfOn()
    {
        $options = [
            'php_handling' => SMARTY_PHP_REMOVE,
            'security' => true,
            'secure_dir' => 'testTemplateDir',
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
                ]
            ];

        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock(true);
        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertSame($options, $configuration->getSecurityOptions());
    }

    public function testGetResources()
    {
        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock();
        $resource = new CacheResourcePlugin($smartyContextMock);
        $options = ['ox' => [
            $resource,
            'getTemplate',
            'getTimestamp',
            'getSecure',
            'getTrusted'
            ]
        ];

        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertEquals($options, $configuration->getResources());
    }

    public function testGetPrefilterPlugin()
    {
        $options = [
            'smarty_prefilter_oxblock' => 'testPluginDir/prefilter.oxblock.php',
            'smarty_prefilter_oxtpldebug' => 'testPluginDir/prefilter.oxtpldebug.php',
        ];

        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertSame($options, $configuration->getPrefilterPlugin());
    }

    public function testGetPlugin()
    {
        $options = ['testModuleDir', 'testShopDir'];

        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertSame($options, $configuration->getPlugins());
    }

    public function testGetParameters()
    {
        $options = ['options', 'securityOptions', 'plugins', 'prefilters', 'resources'];

        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock);
        $this->assertSame($options, array_keys($configuration->getParameters()));
    }

    private function getSmartyContextMock($securityMode = false)
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateCompileDirectory')
            ->willReturn('testCompileDir');

        $smartyContextMock
            ->method('getTemplateDirectories')
            ->willReturn('testTemplateDir');

        $smartyContextMock
            ->method('getTemplateCompileId')
            ->willReturn('testCompileId');

        $smartyContextMock
            ->method('getTemplateEngineDebugMode')
            ->willReturn('2');

        $smartyContextMock
            ->method('getTemplateCompileCheck')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplatePhpHandlingMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        $smartyContextMock
            ->method('getShopTemplatePluginDirectory')
            ->willReturn('testPluginDir');

        $smartyContextMock
            ->method('showTemplateNames')
            ->willReturn(true);

        $smartyContextMock
            ->method('getModuleTemplatePluginDirectories')
            ->willReturn(['testModuleDir']);

        $smartyContextMock
            ->method('getShopTemplatePluginDirectories')
            ->willReturn(['testShopDir']);

        return $smartyContextMock;
    }
}