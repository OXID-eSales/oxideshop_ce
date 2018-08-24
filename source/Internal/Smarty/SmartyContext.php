<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;

/**
 * Class SmartyContext
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyContext implements SmartyContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UtilsView
     */
    private $utilsView;

    /**
     * Context constructor.
     *
     * @param Config    $config
     * @param UtilsView $utilsView
     */
    public function __construct(Config $config, UtilsView $utilsView)
    {
        $this->config = $config;
        $this->utilsView = $utilsView;
    }

    /**
     * @return bool
     */
    public function getTemplateEngineDebugMode()
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode == 1 || $debugMode == 3 || $debugMode == 4);
    }

    /**
     * @return bool
     */
    public function showTemplateNames()
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode == 8 && !$this->getBackendMode());
    }

    /**
     * @return bool
     */
    public function getTemplateSecurityMode()
    {
        return (bool) $this->getDemoShopMode();
    }

    /**
     * @return string
     */
    public function getTemplateCompileDirectory()
    {
        return $this->utilsView->getSmartyDir();
    }

    /**
     * @return array
     */
    public function getTemplateDirectories()
    {
        return $this->utilsView->getTemplateDirs();
    }

    /**
     * @return string
     */
    public function getTemplateCompileId()
    {
        return $this->utilsView->getTemplateCompileId();
    }

    /**
     * @return bool
     */
    public function getTemplateCompileCheck()
    {
        return (bool) $this->getConfigParameter('blCheckTemplates');
    }

    /**
     * @return array
     */
    public function getModuleTemplatePluginDirectories()
    {
        return $this->utilsView->getModuleSmartyPluginDirectories();
    }

    /**
     * @return array
     */
    public function getShopTemplatePluginDirectories()
    {
        return $this->utilsView->getShopSmartyPluginDirectories();
    }

    /**
     * @return int
     */
    public function getTemplatePhpHandlingMode()
    {
        return $this->getConfigParameter('iSmartyPhpHandling');
    }

    /**
     * @return string
     */
    public function getShopTemplatePluginDirectory()
    {
        $coreDirectory = $this->getConfigParameter('sCoreDir');

        return $coreDirectory . 'Smarty/Plugin';
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getTemplatePath($templateName)
    {
        return $this->config->getTemplatePath($templateName, $this->getBackendMode());
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getConfigParameter($name)
    {
        return $this->config->getConfigParam($name);
    }

    /**
     * @return bool
     */
    private function getBackendMode()
    {
        return $this->config->isAdmin();
    }

    /**
     * @return bool
     */
    private function getDemoShopMode()
    {
        return $this->config->isDemoShop();
    }
}
