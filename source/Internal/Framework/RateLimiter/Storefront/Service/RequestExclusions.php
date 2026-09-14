<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\HttpFoundation\Request;

readonly class RequestExclusions implements RequestExclusionsInterface
{
    /**
     * @param string[] $excludedRoutes
     * @param string[] $excludedIps
     */
    public function __construct(
        private array $excludedRoutes,
        private array $excludedIps,
    ) {
    }

    public function excludes(Request $request): bool
    {
        if (in_array($request->getClientIp() ?? 'unknown', $this->excludedIps, true)) {
            return true;
        }

        foreach ($this->excludedRoutes as $pattern) {
            if ($this->matchesRoute($pattern, $request->getPathInfo())) {
                return true;
            }
        }

        return false;
    }

    private function matchesRoute(string $pattern, string $path): bool
    {
        if ($pattern === $path) {
            return true;
        }

        if (!str_contains($pattern, '*')) {
            return false;
        }

        $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';

        return preg_match($regex, $path) === 1;
    }
}
