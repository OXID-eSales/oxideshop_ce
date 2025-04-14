<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use PHPUnit\Framework\TestCase;

final class ProductMediaRoleTest extends TestCase
{
    public function testFromWithDifferentValues(): void
    {
        $this->assertNotEquals(
            ProductMediaRole::from(SystemProductMediaRole::Icon->value)->value(),
            ProductMediaRole::from(SystemProductMediaRole::Thumb->value)->value()
        );
    }

    public function testFromWithSameValues(): void
    {
        $this->assertEquals(
            ProductMediaRole::from(SystemProductMediaRole::Detail->value)->value(),
            ProductMediaRole::from(SystemProductMediaRole::Detail->value)->value(),
        );
    }

    public function testIsSingleByDefault(): void
    {
        $this->assertTrue(ProductMediaRole::from('custom')->isSingleAssignmentRole());
    }

    public function testAllowMultiple(): void
    {
        $role = ProductMediaRole::from('custom')->allowMultipleAssignments();
        $this->assertFalse($role->isSingleAssignmentRole());
    }
}
