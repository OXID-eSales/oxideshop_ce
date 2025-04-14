<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

use ArrayIterator;

class ProductMediaRoleSet
{
    private ArrayIterator $roles;

    public function __construct(
        ProductMediaRole ...$roles
    ) {
        $this->roles = new ArrayIterator($roles);
    }

    /** @return ArrayIterator<int, ProductMediaRole> */
    public function getRoleIterator(): ArrayIterator
    {
        return $this->roles;
    }

    public function addRole(ProductMediaRole $role): void
    {
        foreach ($this->roles as $existingRole) {
            if ($existingRole->value() === $role->value()) {
                return;
            }
        }
        $this->roles->append($role);
    }

    public function removeRole(ProductMediaRole $role): void
    {
        foreach ($this->roles as $key => $existingRole) {
            if ($existingRole->value() === $role->value()) {
                $this->roles->offsetUnset($key);
            }
        }
    }

    public function is(string $roleCode): bool
    {
        foreach ($this->roles as $existingRoles) {
            if ($existingRoles->value() === $roleCode) {
                return true;
            }
        }

        return false;
    }
}
