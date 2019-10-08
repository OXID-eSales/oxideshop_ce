<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     *
     * @param string $ShopClassName
     * @param string $moduleExtensionClassName
     */
    public function __construct(string $ShopClassName, string $moduleExtensionClassName)
    {
        $this->ShopClassName = $ShopClassName;
        $this->moduleExtensionClassName = $moduleExtensionClassName;
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
