<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Authentication\Bridge;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder as Container;

final class RandomTokenGeneratorBridgeTest extends TestCase
{
    private const FIXTURE_PATH_SHOP = '/Fixtures/shop/';

    private RandomTokenGeneratorBridgeInterface $bridge;
    private Container $container;
    private SystemSecurityCheckerInterface $systemSecurityChecker;
    private string $logFile = __DIR__ . self::FIXTURE_PATH_SHOP . 'log/oxideshop.log';

    protected function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->setConfigParam('sShopDir', __DIR__ . self::FIXTURE_PATH_SHOP);
        Registry::getConfig()->setConfigParam('sLogLevel', 'warning');

        ContainerFactory::getInstance()->resetContainer();
        $this->container = (new ContainerBuilder(new BasicContextStub()))->getContainer();
        $this->mockDependencies();
        $this->container->compile();

        $this->bridge = $this->container->get(RandomTokenGeneratorBridgeInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupTestData();
    }

    public function testGetHexTokenWithFallbackWithNoSourceOfRandomnessWillWriteToLog(): void
    {
        $logSizeBefore = \filesize($this->logFile);
        $this->systemSecurityChecker
            ->method('isCryptographicallySecure')
            ->willReturn(false);

        $this->bridge->getHexTokenWithFallback(32);

        $logSizeAfter = \filesize($this->logFile);
        $this->assertGreaterThan($logSizeBefore, $logSizeAfter);
    }

    public function testGetHexTokenWithFallbackWillReturnHexValue(): void
    {
        $this->systemSecurityChecker
            ->method('isCryptographicallySecure')
            ->willReturn(false);

        $token = $this->bridge->getHexTokenWithFallback(32);

        $this->assertTrue(ctype_xdigit($token));
    }

    public function testGetHexTokenWithFallbackAndShortToken(): void
    {
        $tokenLength = 1;
        $this->systemSecurityChecker
            ->method('isCryptographicallySecure')
            ->willReturn(false);

        $token = $this->bridge->getHexTokenWithFallback($tokenLength);

        $this->assertEquals($tokenLength, strlen($token));
    }

    public function testGetHexTokenWithFallbackAndLongToken(): void
    {
        $tokenLength = 1024;
        $this->systemSecurityChecker
            ->method('isCryptographicallySecure')
            ->willReturn(false);

        $token = $this->bridge->getHexTokenWithFallback($tokenLength);

        $this->assertEquals($tokenLength, strlen($token));
    }

    protected function mockDependencies(): void
    {
        $this->systemSecurityChecker = $this->createMock(SystemSecurityCheckerInterface::class);
        $this->container->set(SystemSecurityCheckerInterface::class, $this->systemSecurityChecker);
        $this->container->autowire(SystemSecurityCheckerInterface::class);
    }

    private function cleanupTestData(): void
    {
        if (\is_file($this->logFile)) {
            \unlink($this->logFile);
        }
    }
}
