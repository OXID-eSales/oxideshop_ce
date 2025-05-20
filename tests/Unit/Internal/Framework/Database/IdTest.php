<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function testGenerate(): void
    {
        $id1 = Id::generate();
        $id2 = Id::generate();

        $this->assertNotEquals($id1, $id2);
    }

    public function testFromString(): void
    {
        $id = md5('12345');
        $this->assertEquals($id, Id::fromUid($id));
    }

    public function testFromInvalidString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Id::fromUid('notMd5');
    }
}
