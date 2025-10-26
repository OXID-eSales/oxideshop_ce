<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use PHPUnit\Framework\TestCase;

final class ProductMediaRoleSetTest extends TestCase
{
    public function testAddRoleAddsNewRole(): void
    {
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::ICON));
        $roleSet->addRole(ProductMediaRole::from(ProductMediaRole::THUMBNAIL));

        $this->assertCount(2, $roleSet->getRoles());
        $this->assertTrue($roleSet->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($roleSet->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
    }

    public function testAddRoleDoesNotAddDuplicate(): void
    {
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::ICON));
        $roleSet->addRole(ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertCount(1, $roleSet->getRoles());
    }

    public function testRemoveRole(): void
    {
        $roleSet = new ProductMediaRoleSet(
            ProductMediaRole::from(ProductMediaRole::ICON),
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL),
        );
        $roleSet->removeRole(ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertCount(1, $roleSet->getRoles());
        $this->assertFalse($roleSet->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($roleSet->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
    }
}
