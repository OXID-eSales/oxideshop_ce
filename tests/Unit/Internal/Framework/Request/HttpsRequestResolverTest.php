<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Request;

use OxidEsales\EshopCommunity\Internal\Framework\Request\HttpsRequestResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class HttpsRequestResolverTest extends TestCase
{
    private const TRUSTED_PROXY = '10.0.0.1';
    private const TRUSTED_HEADER_SET = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO;

    private array $originalTrustedProxies;
    private int $originalTrustedHeaderSet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalTrustedProxies = Request::getTrustedProxies();
        $this->originalTrustedHeaderSet = Request::getTrustedHeaderSet();
    }

    protected function tearDown(): void
    {
        Request::setTrustedProxies(
            $this->originalTrustedProxies,
            $this->originalTrustedHeaderSet
        );

        parent::tearDown();
    }

    public function testPlainHttpRequestIsNotHttps(): void
    {
        $this->assertFalse($this->isHttps([], []));
    }

    public function testHttpsServerVariableIsHttps(): void
    {
        $this->assertTrue($this->isHttps(['HTTPS' => 'on'], []));
    }

    public function testDisabledHttpsServerVariableIsNotHttps(): void
    {
        $this->assertFalse($this->isHttps(['HTTPS' => 'off'], []));
    }

    public function testNonStandardHttpsServerVariableIsHttps(): void
    {
        $this->assertTrue($this->isHttps(['HTTPS' => 'yes'], []));
    }

    public function testXForwardedProtoIsIgnoredWithoutConfiguredTrustedProxies(): void
    {
        $this->assertFalse($this->isHttps(['HTTP_X_FORWARDED_PROTO' => 'https'], []));
    }

    public function testXForwardedProtoIsIgnoredFromUntrustedSource(): void
    {
        $this->assertFalse(
            $this->isHttps(
                ['REMOTE_ADDR' => '203.0.113.9', 'HTTP_X_FORWARDED_PROTO' => 'https'],
                [self::TRUSTED_PROXY]
            )
        );
    }

    public function testXForwardedProtoHttpsFromTrustedProxyIsHttps(): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy(['HTTP_X_FORWARDED_PROTO' => 'https']));
    }

    public function testXForwardedProtoHttpBlocksWeakerSslHeaders(): void
    {
        $this->assertFalse($this->isHttpsBehindTrustedProxy([
            'HTTP_X_FORWARDED_PROTO' => 'http',
            'HTTP_X_FORWARDED_SSL' => 'on',
        ]));
    }

    public function testXForwardedProtoWinsWhenForwardedDisagrees(): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy([
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_FORWARDED' => 'for=203.0.113.9;proto=http',
        ]));
    }

    public function testForwardedProtoHttpsIsHttps(): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy(['HTTP_FORWARDED' => 'for=203.0.113.9;proto=https']));
    }

    public function testForwardedProtoOnLaterElementIsHonoured(): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy([
            'HTTP_FORWARDED' => 'for=203.0.113.9, for=' . self::TRUSTED_PROXY . ';proto=https',
        ]));
    }

    public function testMalformedForwardedProtoIsIgnored(): void
    {
        $this->assertFalse($this->isHttpsBehindTrustedProxy(['HTTP_FORWARDED' => 'proto']));
    }

    public static function nonStandardSslHeaderProvider(): array
    {
        return [
            'X-Forwarded-Protocol' => ['HTTP_X_FORWARDED_PROTOCOL', 'https'],
            'X-Forwarded-Scheme' => ['HTTP_X_FORWARDED_SCHEME', 'https'],
            'X-Url-Scheme' => ['HTTP_X_URL_SCHEME', 'https'],
            'X-Forwarded-Ssl' => ['HTTP_X_FORWARDED_SSL', 'on'],
            'Front-End-Https' => ['HTTP_FRONT_END_HTTPS', 'on'],
        ];
    }

    #[DataProvider('nonStandardSslHeaderProvider')]
    public function testResolvesNonStandardSslHeaderFromTrustedProxy(string $header, string $value): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy([$header => $value]));
    }

    public function testMalformedForwardedProtoFallsBackToNonStandardSslHeader(): void
    {
        $this->assertTrue($this->isHttpsWithSslFallback('proto'));
    }

    public function testForwardedElementWithoutProtoFallsBackToNonStandardSslHeader(): void
    {
        $this->assertTrue($this->isHttpsWithSslFallback('for=203.0.113.9'));
    }

    public function testNonHttpsForwardedProtoBlocksNonStandardSslHeader(): void
    {
        $this->assertFalse($this->isHttpsWithSslFallback('proto=http'));
    }

    public function testEmptyForwardedProtoIsSkippedBeforeHttps(): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy(['HTTP_FORWARDED' => 'proto=, proto=https']));
    }

    public function testQuotedEmptyForwardedProtoIsSkippedBeforeHttps(): void
    {
        $this->assertTrue($this->isHttpsBehindTrustedProxy(['HTTP_FORWARDED' => 'proto="", proto=https']));
    }

    public function testLaterNonHttpsForwardedProtoBlocksNonStandardSslHeader(): void
    {
        $this->assertFalse($this->isHttpsWithSslFallback('proto=, proto=http'));
    }

    private function isHttpsWithSslFallback(string $forwardedHeader): bool
    {
        return $this->isHttpsBehindTrustedProxy([
            'HTTP_FORWARDED' => $forwardedHeader,
            'HTTP_X_FORWARDED_SSL' => 'on',
        ]);
    }

    private function isHttpsBehindTrustedProxy(array $serverVars): bool
    {
        return $this->isHttps(
            ['REMOTE_ADDR' => self::TRUSTED_PROXY, ...$serverVars],
            [self::TRUSTED_PROXY]
        );
    }

    private function isHttps(array $serverVars, array $trustedProxies): bool
    {
        Request::setTrustedProxies($trustedProxies, self::TRUSTED_HEADER_SET);

        return (new HttpsRequestResolver(Request::create('/', server: $serverVars)))->isHttps();
    }
}
