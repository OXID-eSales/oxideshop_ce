<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator\RandomTokenGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\FallbackTokenGenerator;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class RandomTokenGeneratorBridgeTest extends TestCase
{
    use ContainerTrait;

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

    public function testGetHexTokenWithFallbackWillReturnHexValue(): void
    {
        $systemSecurityChecker = $this->getMockBuilder(SystemSecurityCheckerInterface::class)->getMock();
        $systemSecurityChecker
            ->method('isCryptographicallySecure')
            ->willReturn(false);


        $bridge = new RandomTokenGeneratorBridge(
            $this->get(RandomTokenGeneratorInterface::class),
            $systemSecurityChecker,
            $this->get(FallbackTokenGenerator::class),
            $this->get(LoggerInterface::class)
        );

        $this->assertTrue(ctype_xdigit($bridge->getHexTokenWithFallback(32)));
    }

    public function testGetHexTokenWithFallbackWithNoSourceOfRandomnessWillWriteToLog(): void
    {
        $systemSecurityChecker = $this->getMockBuilder(SystemSecurityCheckerInterface::class)->getMock();
        $systemSecurityChecker
            ->method('isCryptographicallySecure')
            ->willReturn(false);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->once())
            ->method('warning');


        $bridge = new RandomTokenGeneratorBridge(
            $this->get(RandomTokenGeneratorInterface::class),
            $systemSecurityChecker,
            $this->get(FallbackTokenGenerator::class),
            $logger
        );

        $bridge->getHexTokenWithFallback(32);
    }
}
