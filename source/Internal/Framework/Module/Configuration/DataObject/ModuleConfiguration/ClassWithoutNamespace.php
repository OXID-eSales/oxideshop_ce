<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @deprecated 6.6 Will be removed completely
 */
class ClassWithoutNamespace
{
    /**
     * @var string
     */
    private $shopClass;

    /**
     * @var string
     */
    private $moduleClass;

    /**
     * @param string $shopClass
     * @param string $moduleClass
     */
    public function __construct(string $shopClass, string $moduleClass)
    {
        $this->shopClass = $shopClass;
        $this->moduleClass = $moduleClass;
    }

    /**
     * @return string
     */
    public function getShopClass(): string
    {
        return $this->shopClass;
    }

    /**
     * @return string
     */
    public function getModuleClass(): string
    {
        return $this->moduleClass;
    }
}
