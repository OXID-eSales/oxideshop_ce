<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject;

class ShopConfigurationSetting
{
    public const MODULE_CLASS_EXTENSIONS = 'aModuleExtensions';
    public const MODULE_CLASS_EXTENSIONS_CHAIN = 'aModules';
    public const MODULE_CONTROLLERS = 'aModuleControllers';
    public const MODULE_VERSIONS = 'aModuleVersions';
    public const MODULE_PATHS = 'aModulePaths';
    public const MODULE_TEMPLATES = 'aModuleTemplates';
    public const MODULE_SMARTY_PLUGIN_DIRECTORIES = 'moduleSmartyPluginDirectories';
    public const MODULE_EVENTS = 'aModuleEvents';
    public const ACTIVE_MODULES = 'activeModules';

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
