<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting;

class Setting
{
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
    private $positionInGroup = 0;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Setting
     */
    public function setName(string $name): Setting
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        if ($this->type === null) {
            return gettype($this->value);
        }

        return $this->type;
    }

    /**
     * @param string $type
     * @return Setting
     */
    public function setType(string $type): Setting
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
     * @return Setting
     */
    public function setValue($value): Setting
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
     * @return Setting
     */
    public function setConstraints(array $constraints): Setting
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
     * @return Setting
     */
    public function setGroupName(string $groupName): Setting
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
     * @return Setting
     */
    public function setPositionInGroup(int $positionInGroup): Setting
    {
        $this->positionInGroup = $positionInGroup;
        return $this;
    }
}
