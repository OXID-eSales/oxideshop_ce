<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty;


use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyFactory;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngineConfiguration;

class SmartyFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider smartySettingsProvider
     *
     * @param bool  $securityMode
     * @param array $smartySettings
     */
    public function testFillSmartyProperties($securityMode, $smartySettings)
    {
        /** @var SmartyContextInterface $smartyContextMock */
        $smartyContextMock = $this->getSmartyContextMock($securityMode);
        $smartyFactory = new SmartyFactory(new SmartyEngineConfiguration($smartyContextMock));

        $smarty = $smartyFactory->getSmarty();

        foreach ($smartySettings as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName);
            $this->assertEquals($varValue, $smarty->$varName, $varName);
        }
    }

    /**
     * @return array
     */
    public function smartySettingsProvider()
    {
        return [
                'security on' => [true, $this->getSmartySettingsWithSecurityOn()],
                'security off' => [false, $this->getSmartySettingsWithSecurityOff()]
        ];
    }

    /**
     * @return array
     */
    private function getSmartySettingsWithSecurityOn()
    {
        $aCheck = [
            'security' => true,
            'php_handling' => SMARTY_PHP_REMOVE,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => "testCompileDir",
            'cache_dir' => "testCompileDir",
            'compile_id' => "testCompileId",
            'template_dir' => "testTemplateDir",
            'debugging' => true,
            'compile_check' => true,
            'security_settings' => [
                'PHP_HANDLING' => false,
                'IF_FUNCS' =>
                    [
                        0 => 'array',
                        1 => 'list',
                        2 => 'isset',
                        3 => 'empty',
                        4 => 'count',
                        5 => 'sizeof',
                        6 => 'in_array',
                        7 => 'is_array',
                        8 => 'true',
                        9 => 'false',
                        10 => 'null',
                        11 => 'XML_ELEMENT_NODE',
                        12 => 'is_int',
                    ],
                'INCLUDE_ANY' => false,
                'PHP_TAGS' => false,
                'MODIFIER_FUNCS' =>
                    [
                        0 => 'count',
                        1 => 'round',
                        2 => 'floor',
                        3 => 'trim',
                        4 => 'implode',
                        5 => 'is_array',
                        6 => 'getimagesize',
                    ],
                'ALLOW_CONSTANTS' => true,
                'ALLOW_SUPER_GLOBALS' => true,
            ],
            'plugins_dir' => [
                'testModuleDir',
                'testShopDir',
                'plugins'
            ],
        ];
        return $aCheck;
    }

    /**
     * @return array
     */
    private function getSmartySettingsWithSecurityOff()
    {
        $aCheck = [
            'security' => false,
            'php_handling' => 1,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => "testCompileDir",
            'cache_dir' => "testCompileDir",
            'compile_id' => "testCompileId",
            'template_dir' => "testTemplateDir",
            'debugging' => true,
            'compile_check' => true,
            'plugins_dir' => [
                'testModuleDir',
                'testShopDir',
                'plugins'
            ],
        ];
        return $aCheck;
    }

    private function getSmartyContextMock($securityMode = false)
    {
        $smartyPluginDir = vfsStream::setup('testPluginDir');
        vfsStream::newFile('prefilter.oxblock.php')->at($smartyPluginDir);

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
            ->willReturn(true);

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
            ->willReturn($smartyPluginDir->url());

        $smartyContextMock
            ->method('showTemplateNames')
            ->willReturn(false);

        $smartyContextMock
            ->method('getModuleTemplatePluginDirectories')
            ->willReturn(['testModuleDir']);

        $smartyContextMock
            ->method('getShopTemplatePluginDirectories')
            ->willReturn(['testShopDir']);

        return $smartyContextMock;
    }
}