<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class TemplateBlock
{
    /**
     * @var string
     */
    private $shopTemplatePath;

    /**
     * @var string
     */
    private $blockName;

    /**
     * @var string
     */
    private $moduleTemplatePath;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var string
     */
    private $theme = '';

    public function __construct(string $shopTemplatePath, string $blockName, string $moduleTemplatePath)
    {
        $this->shopTemplatePath = $shopTemplatePath;
        $this->blockName = $blockName;
        $this->moduleTemplatePath = $moduleTemplatePath;
    }

    public function getShopTemplatePath(): string
    {
        return $this->shopTemplatePath;
    }

    public function getBlockName(): string
    {
        return $this->blockName;
    }

    public function getModuleTemplatePath(): string
    {
        return $this->moduleTemplatePath;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }
}
