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
     * ClassExtension constructor.
     */
    public function __construct(private string $ShopClassName, private string $moduleExtensionClassName)
    {
    }

    /**
     * @return string
     */
    public function getShopClassName(): string
    {
        return $this->ShopClassName;
    }

    /**
     * @return string
     */
    public function getModuleExtensionClassName(): string
    {
        return $this->moduleExtensionClassName;
    }
}
