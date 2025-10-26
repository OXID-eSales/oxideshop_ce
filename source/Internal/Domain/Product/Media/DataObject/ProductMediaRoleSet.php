<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductMediaRoleSet
{
    private Collection $roles;

    public function __construct(ProductMediaRole ...$roles)
    {
        $this->roles = new ArrayCollection();
        foreach ($roles as $r) {
            $this->roles->set($r->value(), $r);
        }
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(ProductMediaRole $role): void
    {
        $this->roles->set($role->value(), $role);
    }

    public function removeRole(ProductMediaRole $role): void
    {
        $this->roles->remove($role->value());
    }

    public function has(ProductMediaRole $role): bool
    {
        return $this->roles->containsKey($role->value());
    }
}
