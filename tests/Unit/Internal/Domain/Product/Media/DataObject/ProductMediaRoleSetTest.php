<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use PHPUnit\Framework\TestCase;

final class ProductMediaRoleSetTest extends TestCase
{
    public function testAddRoleAddsNewRole(): void
    {
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(SystemProductMediaRole::Icon->value));
        $roleSet->addRole(ProductMediaRole::from(SystemProductMediaRole::Thumb->value));

        $this->assertCount(2, $roleSet->getRoleIterator());
        $this->assertTrue($roleSet->is(SystemProductMediaRole::Icon->value));
        $this->assertTrue($roleSet->is(SystemProductMediaRole::Thumb->value));
    }

    public function testAddRoleDoesNotAddDuplicate(): void
    {
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(SystemProductMediaRole::Icon->value));
        $roleSet->addRole(ProductMediaRole::from(SystemProductMediaRole::Icon->value));

        $this->assertCount(1, $roleSet->getRoleIterator());
    }

    public function testRemoveRole(): void
    {
        $roleSet = new ProductMediaRoleSet(
            ProductMediaRole::from(SystemProductMediaRole::Icon->value),
            ProductMediaRole::from(SystemProductMediaRole::Thumb->value),
        );
        $roleSet->removeRole(ProductMediaRole::from(SystemProductMediaRole::Icon->value));

        $this->assertCount(1, $roleSet->getRoleIterator());
        $this->assertFalse($roleSet->is(SystemProductMediaRole::Icon->value));
        $this->assertTrue($roleSet->is(SystemProductMediaRole::Thumb->value));
    }
}
