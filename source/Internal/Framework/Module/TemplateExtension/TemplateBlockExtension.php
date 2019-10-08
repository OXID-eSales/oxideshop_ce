<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TemplateBlockExtension
     */
    public function setName(string $name): TemplateBlockExtension
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return TemplateBlockExtension
     */
    public function setFilePath(string $filePath): TemplateBlockExtension
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtendedBlockTemplatePath(): string
    {
        return $this->extendedBlockTemplatePath;
    }

    /**
     * @param string $extendedBlockTemplatePath
     * @return TemplateBlockExtension
     */
    public function setExtendedBlockTemplatePath(string $extendedBlockTemplatePath): TemplateBlockExtension
    {
        $this->extendedBlockTemplatePath = $extendedBlockTemplatePath;
        return $this;
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
     * @return TemplateBlockExtension
     */
    public function setPosition(int $position): TemplateBlockExtension
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    /**
     * @param string $moduleId
     * @return TemplateBlockExtension
     */
    public function setModuleId(string $moduleId): TemplateBlockExtension
    {
        $this->moduleId = $moduleId;
        return $this;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     * @return TemplateBlockExtension
     */
    public function setShopId(int $shopId): TemplateBlockExtension
    {
        $this->shopId = $shopId;
        return $this;
    }

    /**
     * @return string
     */
    public function getThemeId(): string
    {
        return $this->themeId;
    }

    /**
     * @param string $themeId
     * @return TemplateBlockExtension
     */
    public function setThemeId(string $themeId): TemplateBlockExtension
    {
        $this->themeId = $themeId;
        return $this;
    }
}
