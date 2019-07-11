<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ClassExtension
{
    /**
     * @var string
     */
    private $shopClassNamespace;

    /**
     * @var string
     */
    private $moduleExtensionClassNamespace;

    /**
     * ClassExtension constructor.
     *
     * @param string $shopClassNamespace
     * @param string $moduleExtensionClassNamespace
     */
    public function __construct(string $shopClassNamespace, string $moduleExtensionClassNamespace)
    {
        $this->shopClassNamespace = $shopClassNamespace;
        $this->moduleExtensionClassNamespace = $moduleExtensionClassNamespace;
    }

    /**
     * @return string
     */
    public function getShopClassNamespace(): string
    {
        return $this->shopClassNamespace;
    }

    /**
     * @return string
     */
    public function getModuleExtensionClassNamespace(): string
    {
        return $this->moduleExtensionClassNamespace;
    }
}
