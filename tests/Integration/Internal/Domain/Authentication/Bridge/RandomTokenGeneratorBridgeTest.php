<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

use function strlen;

final class RandomTokenGeneratorBridgeTest extends TestCase
{
    use ContainerTrait;

    private RandomTokenGeneratorBridgeInterface $bridge;

    public function testGetAlphanumericToken(): void
    {
        $length = 32;

        $token = $this->get(RandomTokenGeneratorBridgeInterface::class)->getAlphanumericToken($length);

        $this->assertEquals($length, strlen($token));
        $this->assertTrue(ctype_alnum($token));
        $this->assertFalse(ctype_xdigit($token));
    }

    public function testGetHexToken(): void
    {
        $length = 32;

        $token = $this->get(RandomTokenGeneratorBridgeInterface::class)->getHexToken($length);

        $this->assertEquals($length, strlen($token));
        $this->assertTrue(ctype_alnum($token));
        $this->assertTrue(ctype_xdigit($token));
    }
}
