<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

class Setting
{
    private string $name;
    private ?string $type = null;
    private mixed $value = null;
    private array $constraints = [];
    private string $groupName = '';
    private int $positionInGroup = 0;

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
        if ($this->type === null) {
            return gettype($this->value);
        }

        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param string[] $constraints
     */
    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): self
    {
        $this->groupName = $groupName;
        return $this;
    }

    public function getPositionInGroup(): int
    {
        return $this->positionInGroup;
    }

    public function setPositionInGroup(int $positionInGroup): self
    {
        $this->positionInGroup = $positionInGroup;
        return $this;
    }
}
