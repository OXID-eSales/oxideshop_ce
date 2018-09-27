<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\TemplateExtension;

/**
 * @internal
 */
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
    private $priority = 0;

    /**
     * @var string
     */
    private $moduleId;

    /**
     * @var int
     */
    private $shopId;

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
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return TemplateBlockExtension
     */
    public function setPriority(int $priority): TemplateBlockExtension
    {
        $this->priority = $priority;
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
}
