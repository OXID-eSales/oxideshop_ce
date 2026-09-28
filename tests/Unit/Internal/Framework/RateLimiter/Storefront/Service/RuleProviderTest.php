<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);
namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Request\RequestInterface;
use PHPUnit\Framework\TestCase;

final class RuleProviderTest extends TestCase
{
    public function testReturnsIpRuleBeforeEmailRuleRegardlessOfConfiguredOrder(): void
    {
        $provider = $this->createProvider([
            ['id' => 'login_email', 'key' => 'email'],
            ['id' => 'login_ip', 'key' => 'ip'],
        ]);

        $ids = array_column($provider->getMatchingRules(), 'id');

        $this->assertSame(['login_ip', 'login_email'], $ids);
    }

    public function testReturnsOnlyRulesMatchingControllerAndFunction(): void
    {
        $provider = $this->createProvider(
            [
                ['id' => 'contact_ip', 'cl' => 'contact', 'fnc' => 'send', 'key' => 'ip'],
                ['id' => 'login_ip', 'fnc' => ['login', 'login_noredirect'], 'key' => 'ip'],
            ],
            ['cl' => 'Contact', 'fnc' => 'Send'],
        );

        $ids = array_column($provider->getMatchingRules(), 'id');

        $this->assertSame(['contact_ip'], $ids);
    }

    public function testRuleWithoutControllerAndFunctionMatchesEveryRequest(): void
    {
        $provider = $this->createProvider([['id' => 'global', 'key' => 'user']]);

        $ids = array_column($provider->getMatchingRules(), 'id');

        $this->assertSame(['global'], $ids);
    }

    public function testReturnsNothingWithoutConfiguredRules(): void
    {
        $this->assertSame([], $this->createProvider([])->getMatchingRules());
    }

    public function testReturnsNothingForNonMatchingRequest(): void
    {
        $provider = $this->createProvider(
            [['id' => 'login_ip', 'fnc' => ['login'], 'key' => 'ip']],
            ['fnc' => 'tobasket'],
        );

        $this->assertSame([], $provider->getMatchingRules());
    }

    public function testFunctionBoundRuleNeverMatchesNonScalarParameter(): void
    {
        $provider = $this->createProvider(
            [['id' => 'trap', 'fnc' => ['array'], 'key' => 'ip']],
            ['fnc' => ['array']],
        );

        $this->assertSame([], $provider->getMatchingRules());
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     * @param array<string, mixed> $parameters
     */
    private function createProvider(array $rules, array $parameters = []): RuleProvider
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('get')->willReturnCallback(
            fn (string $name, mixed $default = null): mixed => $parameters[$name] ?? $default,
        );

        return new RuleProvider($rules, $request);
    }
}
