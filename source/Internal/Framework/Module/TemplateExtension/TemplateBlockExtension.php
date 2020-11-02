<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension;

class TemplateBlockExtension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $extendedBlockTemplatePath;

    /**
     * @var int
     */
    private $position = 1;

    /**
     * @var string
     */
    private $moduleId;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var string
     */
    private $themeId = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getExtendedBlockTemplatePath(): string
    {
        return $this->extendedBlockTemplatePath;
    }

    public function setExtendedBlockTemplatePath(string $extendedBlockTemplatePath): self
    {
        $this->extendedBlockTemplatePath = $extendedBlockTemplatePath;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function setModuleId(string $moduleId): self
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function setThemeId(string $themeId): self
    {
        $this->themeId = $themeId;

        return $this;
    }
}
