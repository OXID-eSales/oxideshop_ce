<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class IdTest extends TestCase
{
    public function testGenerate(): void
    {
        $id1 = Id::generate();
        $id2 = Id::generate();

        $this->assertNotEquals($id1, $id2);
    }

    public function testFromUidWithMd5String(): void
    {
        $md5 = md5('1');

        $id = Id::fromUid($md5);

        $this->assertEquals($id, $md5);
    }

    public function testFromUidWithLegacyIdsWillNotFail(): void
    {
        $legacyId = str_repeat('z1-.', 4);

        $id = Id::fromUid($legacyId);

        $this->assertEquals($id, $legacyId);
    }

    public function testFromUidWithEmptyStringWillThrow(): void
    {
        $string = '';

        $this->expectException(\InvalidArgumentException::class);

        Id::fromUid($string);
    }

    public function testFromUidWithStringTooLongWillThrow(): void
    {
        $string = str_repeat('1', 33);

        $this->expectException(\InvalidArgumentException::class);

        Id::fromUid($string);
    }
}
