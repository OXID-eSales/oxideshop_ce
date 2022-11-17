<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyConfigurationFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContext;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\TestingLibrary\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SmartyBuilderTest extends UnitTestCase
{
    private int $debugMode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->debugMode = (int) Registry::getConfig()->getConfigParam('iDebug');
    }

    protected function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('iDebug', $this->debugMode);
        parent::tearDown();
    }

    public function testSmartySettingsWithSecurityOnWillBeSetCorrectly(): void
    {
        $smarty = $this->getSmarty(1);

        foreach ($this->getSmartySettingsWithSecurityOn() as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals($varValue, $smarty->$varName, 'Not correct value of the smarts setting: ' . $varName);
        }
    }

    public function testSmartySettingsWithSecurityOffWillBeSetCorrectly(): void
    {
        $smarty = $this->getSmarty(0);

        foreach ($this->getSmartySettingsWithSecurityOff() as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals($varValue, $smarty->$varName, 'Not correct value of the smarts setting: ' . $varName);
        }
    }

    private function getSmartySettingsWithSecurityOn(): array
    {
        $config = Registry::getConfig();
        return [
            'security' => true,
            'php_handling' => SMARTY_PHP_REMOVE,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => Registry::getUtilsView()->getTemplateDirs(),
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
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
            'plugins_dir' => $this->getSmartyPluginDirectories(),
        ];
    }

    private function getSmartySettingsWithSecurityOff(): array
    {
        $config = Registry::getConfig();
        return [
            'security' => false,
            'php_handling' => $config->getConfigParam('iSmartyPhpHandling'),
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => Registry::getUtilsView()->getTemplateDirs(),
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'plugins_dir' => $this->getSmartyPluginDirectories(),
        ];
    }

    private function getSmartyPluginDirectories(): array
    {
        return array_merge(Registry::getUtilsView()->getSmartyPluginDirectories(), ['plugins']);
    }

    private function getSmarty(int $securityMode): \Smarty
    {
        $configuration = $this->setupAndConfigureContainer($this->getSmartyContext($securityMode))
            ->get(SmartyConfigurationFactoryInterface::class)
            ->getConfiguration();

        return (new SmartyBuilder())
            ->setSettings($configuration->getSettings())
            ->setSecuritySettings($configuration->getSecuritySettings())
            ->registerPlugins($configuration->getPlugins())
            ->registerPrefilters($configuration->getPrefilters())
            ->registerResources($configuration->getResources())
            ->getSmarty();
    }

    private function getSmartyContext(int $securityMode): SmartyContext
    {
        $config = Registry::getConfig();
        $config->setConfigParam('blDemoShop', $securityMode);
        $config->setConfigParam('iDebug', 0);

        return new SmartyContext(new BasicContext(), $config, Registry::getUtilsView());
    }

    private function setupAndConfigureContainer(SmartyContext $smartyContext): ContainerBuilder
    {
        $container = (new TestContainerFactory())->create();

        $container->set(SmartyContextInterface::class, $smartyContext);
        $container->autowire(SmartyContextInterface::class, SmartyContext::class);

        $container->compile();

        return $container;
    }
}
