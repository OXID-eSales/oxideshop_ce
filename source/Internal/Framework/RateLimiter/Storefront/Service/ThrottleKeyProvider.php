<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class ThrottleKeyProvider implements ThrottleKeyProviderInterface
{
    public function __construct(private SessionInterface $session)
    {
    }

    /**
     * @param array<string, mixed> $rule
     */
    public function keyFor(array $rule, Request $request): string
    {
        return match ((string) $rule['key']) {
            'user' => $this->hashKey($this->userIdentifier($request)),
            'email' => $this->emailKey($request),
            default => $this->hashKey($request->getClientIp() ?? 'unknown'),
        };
    }

    private function userIdentifier(Request $request): string
    {
        return ((string) $this->session->get('usr', '')) ?: ($request->getClientIp() ?? 'unknown');
    }

    private function emailKey(Request $request): string
    {
        $email = strtolower(trim($this->param($request, 'lgn_usr')));

        return $email === '' ? '' : $this->hashKey($email . '-' . ($request->getClientIp() ?? 'unknown'));
    }

    private function param(Request $request, string $name): string
    {
        $value = $request->request->all()[$name] ?? $request->query->all()[$name] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }

    private function hashKey(string $data): string
    {
        return hash('sha256', $data);
    }
}
