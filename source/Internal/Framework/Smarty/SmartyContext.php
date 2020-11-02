<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

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
     * @var BasicContextInterface
     */
    private $basicContext;

    /**
     * Context constructor.
     */
    public function __construct(BasicContextInterface $basicContext, Config $config, UtilsView $utilsView)
    {
        $this->config = $config;
        $this->utilsView = $utilsView;
        $this->basicContext = $basicContext;
    }

    public function getTemplateEngineDebugMode(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');

        return 1 === $debugMode || 3 === $debugMode || 4 === $debugMode;
    }

    public function showTemplateNames(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');

        return 8 === $debugMode && !$this->getBackendMode();
    }

    public function getTemplateSecurityMode(): bool
    {
        return $this->getDemoShopMode();
    }

    public function getTemplateCompileDirectory(): string
    {
        return $this->utilsView->getSmartyDir();
    }

    public function getTemplateDirectories(): array
    {
        return $this->utilsView->getTemplateDirs();
    }

    public function getTemplateCompileId(): string
    {
        return $this->utilsView->getTemplateCompileId();
    }

    public function getTemplateCompileCheckMode(): bool
    {
        $compileCheck = (bool)$this->getConfigParameter('blCheckTemplates');
        if ($this->config->isProductiveMode()) {
            // override in any case
            $compileCheck = false;
        }

        return $compileCheck;
    }

    public function getSmartyPluginDirectories(): array
    {
        return $this->utilsView->getSmartyPluginDirectories();
    }

    public function getTemplatePhpHandlingMode(): int
    {
        return (int)$this->getConfigParameter('iSmartyPhpHandling');
    }

    /**
     * @param string $templateName
     */
    public function getTemplatePath($templateName): string
    {
        return $this->config->getTemplatePath($templateName, $this->getBackendMode());
    }

    public function getSourcePath(): string
    {
        return $this->basicContext->getSourcePath();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    private function getConfigParameter($name)
    {
        return $this->config->getConfigParam($name);
    }

    private function getBackendMode(): bool
    {
        return $this->config->isAdmin();
    }

    private function getDemoShopMode(): bool
    {
        return (bool)$this->config->isDemoShop();
    }
}
