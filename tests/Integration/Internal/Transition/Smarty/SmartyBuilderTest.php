<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyConfigurationFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContext;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyConfigurationFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;

class SmartyBuilderTest extends \PHPUnit\Framework\TestCase
{
    private $debugMode;

    public function setUp()
    {
        parent::setUp();
        $this->debugMode = Registry::getConfig()->getConfigParam('iDebug');
    }

    public function tearDown()
    {
        Registry::getConfig()->setConfigParam('iDebug', $this->debugMode);
        parent::tearDown();
    }

    /**
     * @dataProvider smartySettingsDataProvider
     *
     * @param bool  $securityMode
     * @param array $smartySettings
     */
    public function testSmartySettingsAreSetCorrect($securityMode, $smartySettings)
    {
        /** @var SmartyConfigurationFactory $configurationFactory */
        $configurationFactory = $this->setupAndConfigureContainer($securityMode)
            ->get(SmartyConfigurationFactoryInterface::class);
        $configuration = $configurationFactory->getConfiguration();
        $smarty = (new SmartyBuilder())
            ->setSettings($configuration->getSettings())
            ->setSecuritySettings($configuration->getSecuritySettings())
            ->registerPlugins($configuration->getPlugins())
            ->registerPrefilters($configuration->getPrefilters())
            ->registerResources($configuration->getResources())
            ->getSmarty();

        foreach ($smartySettings as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals($varValue, $smarty->$varName, 'Not correct value of the smarts setting: ' . $varName);
        }
    }

    /**
     * @return array
     */
    public function smartySettingsDataProvider()
    {
        return [
            'security on' => [1, $this->getSmartySettingsWithSecurityOn()],
            'security off' => [0, $this->getSmartySettingsWithSecurityOff()]
        ];
    }

    private function getSmartySettingsWithSecurityOn(): array
    {
        $config = Registry::getConfig();
        $templateDirs = Registry::getUtilsView()->getTemplateDirs();
        return [
            'security' => true,
            'php_handling' => SMARTY_PHP_REMOVE,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => $templateDirs,
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
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartySettingsWithSecurityOff(): array
    {
        $config = Registry::getConfig();
        $templateDirs = Registry::getUtilsView()->getTemplateDirs();
        return [
            'security' => false,
            'php_handling' => $config->getConfigParam('iSmartyPhpHandling'),
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => $templateDirs,
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartyPlugins()
    {
        return array_merge(Registry::getUtilsView()->getSmartyPluginDirectories(), ['plugins']);
    }

    private function getSmartyContext($securityMode = false): SmartyContext
    {
        $config = Registry::getConfig();
        $config->setConfigParam('blDemoShop', $securityMode);
        $config->setConfigParam('iDebug', 0);

        return new SmartyContext(new BasicContext(), $config, Registry::getUtilsView());
    }

    /**
     * We need to replace services in the container with a mock
     *
     * @param bool $securityMode
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function setupAndConfigureContainer($securityMode = false)
    {
        $container = (new TestContainerFactory())->create();

        $container->set(SmartyContextInterface::class, $this->getSmartyContext($securityMode));
        $container->autowire(SmartyContextInterface::class, SmartyContext::class);

        $container->compile();

        return $container;
    }
}
