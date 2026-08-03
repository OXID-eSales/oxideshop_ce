<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Request;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;

readonly class HttpsRequestResolver implements HttpsRequestResolverInterface
{
    private const NON_STANDARD_SSL_HEADERS = [
        'X-Forwarded-Protocol' => 'https',
        'X-Forwarded-Scheme' => 'https',
        'X-Url-Scheme' => 'https',
        'X-Forwarded-Ssl' => 'on',
        'Front-End-Https' => 'on',
    ];

    public function __construct(private Request $request)
    {
    }

    public function isHttps(): bool
    {
        if ($this->request->isSecure()) {
            return true;
        }

        if (!$this->request->isFromTrustedProxy()) {
            return false;
        }

        if ($this->request->headers->has('X-Forwarded-Proto')) {
            return false;
        }

        return $this->isHttpsFromAdditionalForwardedHeaders();
    }

    private function isHttpsFromAdditionalForwardedHeaders(): bool
    {
        $forwardedHeader = $this->request->headers->get('Forwarded', '');

        foreach (HeaderUtils::split($forwardedHeader, ',;=') as $forwardedElement) {
            $proto = HeaderUtils::combine($forwardedElement)['proto'] ?? null;

            if (is_string($proto) && $proto !== '') {
                return strtolower($proto) === 'https';
            }
        }

        return $this->hasNonStandardSslHeader();
    }

    private function hasNonStandardSslHeader(): bool
    {
        foreach (self::NON_STANDARD_SSL_HEADERS as $header => $secureValue) {
            if ($this->getFirstHeaderValue($header) === $secureValue) {
                return true;
            }
        }

        return false;
    }

    private function getFirstHeaderValue(string $header): string
    {
        $headerValue = $this->request->headers->get($header, '');

        return strtolower(trim(explode(',', $headerValue)[0]));
    }
}
