<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Smarty\Extension\CacheResourcePlugin;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\SmartyDefaultTemplateHandler;

/**
 * Class SmartyEngineConfiguration
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyEngineConfiguration implements TemplateEngineConfigurationInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * TemplateEngineConfiguration constructor.
     *
     * @param SmartyContextInterface $context
     */
    public function __construct(SmartyContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Return an array of smarty options to configure.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => $this->context->getTemplateCompileDirectory(),
            'cache_dir' => $this->context->getTemplateCompileDirectory(),
            'template_dir' => $this->context->getTemplateDirectories(),
            'compile_id' => $this->context->getTemplateCompileId(),
            'default_template_handler_func' => [new SmartyDefaultTemplateHandler($this->context), 'handleTemplate'],
            'debugging' => $this->context->getTemplateEngineDebugMode(),
            'compile_check' => $this->context->getTemplateCompileCheck()
        ];
    }

    /**
     * Return an array of smarty security options to configure.
     *
     * @return array
     */
    public function getSecurityOptions()
    {
        $options = [
            'php_handling' => (int) $this->context->getTemplatePhpHandlingMode(),
            'security' => false
        ];
        if ($this->context->getTemplateSecurityMode()) {
            $options = [
                'php_handling' => SMARTY_PHP_REMOVE,
                'security' => true,
                'secure_dir' => $this->context->getTemplateDirectories(),
                'security_settings' => $this->getSecuritySettings()
            ];
        }
        return $options;
    }

    /**
     * Return an array of smarty plugins to assign.
     *
     * @return array
     */
    public function getPlugins()
    {
        return array_merge(
            $this->context->getModuleTemplatePluginDirectories(),
            $this->context->getShopTemplatePluginDirectories()
        );
    }

    /**
     * Return an array of prefilters to register.
     *
     * @return array
     */
    public function getPrefilterPlugin()
    {
        $shopSmartyPluginPath = $this->context->getShopTemplatePluginDirectory() ;
        $prefilter['smarty_prefilter_oxblock'] = $shopSmartyPluginPath . '/prefilter.oxblock.php';
        if ($this->context->showTemplateNames()) {
            $prefilter['smarty_prefilter_oxtpldebug'] = $shopSmartyPluginPath . '/prefilter.oxtpldebug.php';
        }

        return $prefilter;
    }

    /**
     * Return an array of resources to register.
     *
     * @return array
     */
    public function getResources()
    {
        $resource = new CacheResourcePlugin($this->context);
        return ['ox' => [
                    $resource,
                    'getTemplate',
                    'getTimestamp',
                    'getSecure',
                    'getTrusted'
                    ]
                ];
    }

    /**
     * Return an array of security settings.
     *
     * @return array
     */
    private function getSecuritySettings()
    {
        return [
            'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
            'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
            'ALLOW_CONSTANTS' => true,
        ];
    }

    /**
     * Get properties for smarty:
     * [
     *   'options' => 'smartyCommonOptions',
     *   'securityOptions' => 'smartySecurityOptions',
     *   'plugins' => 'smartyPluginsToRegister',
     *   'prefilters' => 'smartyPreFiltersToRegister',
     *   'resources' => 'smartyResourcesToRegister',
     * ]
     *
     * @return array
     */
    public function getParameters()
    {
        $params['options'] = $this->getOptions();
        $params['securityOptions'] = $this->getSecurityOptions();
        $params['plugins'] = $this->getPlugins();
        $params['prefilters'] = $this->getPrefilterPlugin();
        $params['resources'] = $this->getResources();
        return $params;
    }
}
