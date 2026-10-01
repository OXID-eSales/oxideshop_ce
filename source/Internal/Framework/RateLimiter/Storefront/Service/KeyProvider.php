<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class KeyProvider implements KeyProviderInterface
{
    public function __construct(
        private SessionInterface $session,
    ) {
    }

    /**
     * @param array<string, mixed> $rule
     */
    public function get(array $rule, Request $request): string
    {
        return match ((string) $rule['key']) {
            'user' => $this->hashKey($this->userIdentifier($request)),
            'email' => $this->emailKey($request),
            default => $this->hashKey($this->clientIp($request)),
        };
    }

    private function userIdentifier(Request $request): string
    {
        return ((string) $this->session->get('usr', '')) ?: $this->clientIp($request);
    }

    private function emailKey(Request $request): string
    {
        $email = strtolower(trim($request->request->getString('lgn_usr')));

        return $email === '' ? '' : $this->hashKey($email . '-' . $this->clientIp($request));
    }

    private function clientIp(Request $request): string
    {
        return $request->getClientIp() ?? 'unknown';
    }

    private function hashKey(string $data): string
    {
        return hash('sha256', $data);
    }
}
