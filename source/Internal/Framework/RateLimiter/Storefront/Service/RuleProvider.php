<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\HttpFoundation\Request;

readonly class RuleProvider implements RuleProviderInterface
{
    private array $rules;

    /**
     * @param array<int, array<string, mixed>> $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $this->ordered($rules);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rulesFor(Request $request): array
    {
        $controllerKey = strtolower((string) $request->get('cl', ''));
        $function = strtolower((string) $request->get('fnc', ''));

        return array_filter(
            $this->rules,
            fn (array $rule): bool => $this->matches($rule, $controllerKey, $function),
        );
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function matches(array $rule, string $controllerKey, string $function): bool
    {
        return (!isset($rule['fnc']) || in_array($function, array_map('strtolower', (array) $rule['fnc']), true))
            && (!isset($rule['cl']) || strtolower((string) $rule['cl']) === $controllerKey);
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     * @return array<int, array<string, mixed>>
     */
    private function ordered(array $rules): array
    {
        usort(
            $rules,
            fn (array $first, array $second): int => $this->keyRank($first) <=> $this->keyRank($second),
        );

        return $rules;
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function keyRank(array $rule): int
    {
        return match ((string) ($rule['key'] ?? '')) {
            'user' => 1,
            'email' => 2,
            default => 0,
        };
    }
}
