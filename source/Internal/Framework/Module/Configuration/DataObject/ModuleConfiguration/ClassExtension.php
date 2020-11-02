<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class ClassExtension
{
    /**
     * @var string
     */
    private $ShopClassName;

    /**
     * @var string
     */
    private $moduleExtensionClassName;

    /**
     * ClassExtension constructor.
     */
    public function __construct(string $ShopClassName, string $moduleExtensionClassName)
    {
        $this->ShopClassName = $ShopClassName;
        $this->moduleExtensionClassName = $moduleExtensionClassName;
    }

    public function getShopClassName(): string
    {
        return $this->ShopClassName;
    }

    public function getModuleExtensionClassName(): string
    {
        return $this->moduleExtensionClassName;
    }
}
