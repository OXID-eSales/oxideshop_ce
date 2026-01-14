<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Exception\EmptyProductMediaRoleException;
use PHPUnit\Framework\TestCase;

final class ProductMediaRoleTest extends TestCase
{
    public function testFromWithDifferentValues(): void
    {
        $this->assertNotEquals(
            ProductMediaRole::from(ProductMediaRole::ICON)->value(),
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)->value()
        );
    }

    public function testFromWithSameValues(): void
    {
        $this->assertEquals(
            ProductMediaRole::from(ProductMediaRole::DETAIL)->value(),
            ProductMediaRole::from(ProductMediaRole::DETAIL)->value(),
        );
    }

    public function testFromWithEmptyStringThrowsException(): void
    {
        $this->expectException(EmptyProductMediaRoleException::class);

        ProductMediaRole::from('');
    }
}
