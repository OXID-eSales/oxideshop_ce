<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting;

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
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array
     */
    private $constraints = [];

    /**
     * @var string
     */
    private $groupName = '';

    /**
     * @var int
     */
    private $positionInGroup = 1;

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    /**
     * @param string $moduleId
     * @return ShopModuleSetting
     */
    public function setModuleId(string $moduleId): ShopModuleSetting
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
     * @return ShopModuleSetting
     */
    public function setShopId(int $shopId): ShopModuleSetting
    {
        $this->shopId = $shopId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ShopModuleSetting
     */
    public function setName(string $name): ShopModuleSetting
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ShopModuleSetting
     */
    public function setType(string $type): ShopModuleSetting
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
     * @return ShopModuleSetting
     */
    public function setValue($value): ShopModuleSetting
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param array $constraints
     * @return ShopModuleSetting
     */
    public function setConstraints(array $constraints): ShopModuleSetting
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     * @return ShopModuleSetting
     */
    public function setGroupName(string $groupName): ShopModuleSetting
    {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * @return int
     */
    public function getPositionInGroup(): int
    {
        return $this->positionInGroup;
    }

    /**
     * @param int $positionInGroup
     * @return ShopModuleSetting
     */
    public function setPositionInGroup(int $positionInGroup): ShopModuleSetting
    {
        $this->positionInGroup = $positionInGroup;
        return $this;
    }
}
