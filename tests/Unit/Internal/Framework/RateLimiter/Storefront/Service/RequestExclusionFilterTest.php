<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RequestExclusionFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RequestExclusionFilterTest extends TestCase
{
    public function testExcludesListedClientIp(): void
    {
        $filter = $this->createFilter([], ['203.0.113.10']);

        $this->assertTrue($filter->isExcluded($this->createRequest('/')));
    }

    public function testExcludesExactRoute(): void
    {
        $filter = $this->createFilter(['/health'], []);

        $this->assertTrue($filter->isExcluded($this->createRequest('/health')));
    }

    public function testExcludesWildcardRoute(): void
    {
        $filter = $this->createFilter(['/api/*'], []);

        $this->assertTrue($filter->isExcluded($this->createRequest('/api/status')));
    }

    public function testWildcardPatternKeepsDotLiteral(): void
    {
        $filter = $this->createFilter(['/api/v1.0/*'], []);

        $this->assertTrue($filter->isExcluded($this->createRequest('/api/v1.0/status')));
        $this->assertFalse($filter->isExcluded($this->createRequest('/api/v1X0/status')));
    }

    public function testWildcardPatternKeepsRegexMetacharactersLiteral(): void
    {
        $filter = $this->createFilter(['/health(x*'], []);

        $this->assertTrue($filter->isExcluded($this->createRequest('/health(xyz')));
        $this->assertFalse($filter->isExcluded($this->createRequest('/healthx')));
    }

    public function testDoesNotExcludeUnlistedRequest(): void
    {
        $filter = $this->createFilter(['/health'], ['198.51.100.7']);

        $this->assertFalse($filter->isExcluded($this->createRequest('/shop')));
    }

    /**
     * @param string[] $excludedRoutes
     * @param string[] $excludedIps
     */
    private function createFilter(array $excludedRoutes, array $excludedIps): RequestExclusionFilter
    {
        return new RequestExclusionFilter($excludedRoutes, $excludedIps);
    }

    private function createRequest(string $path): Request
    {
        return Request::create($path, 'GET', [], [], [], ['REMOTE_ADDR' => '203.0.113.10']);
    }
}
