<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject;

/**
 * @internal
 */
class ShopModuleSetting
{
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
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * ShopModuleSetting constructor.
     *
     * @param string $moduleId
     * @param int    $shopId
     * @param string $name
     * @param mixed  $value
     */
    public function __construct(
        string $moduleId,
        int $shopId,
        string $name,
        $value
    ) {
        $this->moduleId = $moduleId;
        $this->shopId = $shopId;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
     * @return ShopModuleSetting
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
