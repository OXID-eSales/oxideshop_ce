<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RequestExclusions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RequestExclusionsTest extends TestCase
{
    public function testExcludesListedClientIp(): void
    {
        $exclusions = new RequestExclusions([], ['203.0.113.10']);

        $this->assertTrue($exclusions->excludes($this->request('/')));
    }

    public function testExcludesExactRoute(): void
    {
        $exclusions = new RequestExclusions(['/health'], []);

        $this->assertTrue($exclusions->excludes($this->request('/health')));
    }

    public function testExcludesWildcardRoute(): void
    {
        $exclusions = new RequestExclusions(['/api/*'], []);

        $this->assertTrue($exclusions->excludes($this->request('/api/status')));
    }

    public function testWildcardPatternKeepsDotLiteral(): void
    {
        $exclusions = new RequestExclusions(['/api/v1.0/*'], []);

        $this->assertTrue($exclusions->excludes($this->request('/api/v1.0/status')));
        $this->assertFalse($exclusions->excludes($this->request('/api/v1X0/status')));
    }

    public function testWildcardPatternKeepsRegexMetacharactersLiteral(): void
    {
        $exclusions = new RequestExclusions(['/health(x*'], []);

        $this->assertTrue($exclusions->excludes($this->request('/health(xyz')));
        $this->assertFalse($exclusions->excludes($this->request('/healthx')));
    }

    public function testDoesNotExcludeUnlistedRequest(): void
    {
        $exclusions = new RequestExclusions(['/health'], ['198.51.100.7']);

        $this->assertFalse($exclusions->excludes($this->request('/shop')));
    }

    private function request(string $path): Request
    {
        return Request::create($path, 'GET', [], [], [], ['REMOTE_ADDR' => '203.0.113.10']);
    }
}
