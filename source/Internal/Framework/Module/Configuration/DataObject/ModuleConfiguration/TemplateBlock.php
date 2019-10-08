<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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

    /**
     * @param string $shopTemplatePath
     * @param string $blockName
     * @param string $moduleTemplatePath
     */
    public function __construct(string $shopTemplatePath, string $blockName, string $moduleTemplatePath)
    {
        $this->shopTemplatePath = $shopTemplatePath;
        $this->blockName = $blockName;
        $this->moduleTemplatePath = $moduleTemplatePath;
    }

    /**
     * @return string
     */
    public function getShopTemplatePath(): string
    {
        return $this->shopTemplatePath;
    }

    /**
     * @return string
     */
    public function getBlockName(): string
    {
        return $this->blockName;
    }

    /**
     * @return string
     */
    public function getModuleTemplatePath(): string
    {
        return $this->moduleTemplatePath;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }
}
