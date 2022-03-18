<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface;
use PHPUnit\Framework\TestCase;

final class RandomTokenGeneratorBridgeTest extends TestCase
{
    private RandomTokenGeneratorBridgeInterface $bridge;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bridge = ContainerFactory::getInstance()->getContainer()->get(RandomTokenGeneratorBridgeInterface::class);
    }

    public function testGetAlphanumericToken(): void
    {
        $length = 32;

        $token = $this->bridge->getAlphanumericToken($length);

        $this->assertEquals($length, strlen($token));
        $this->assertTrue(ctype_alnum($token));
        $this->assertFalse(ctype_xdigit($token));
    }

    public function testGetHexToken(): void
    {
        $length = 32;

        $token = $this->bridge->getHexToken($length);

        $this->assertEquals($length, strlen($token));
        $this->assertTrue(ctype_alnum($token));
        $this->assertTrue(ctype_xdigit($token));
    }
}
