<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RuleProviderTest extends TestCase
{
    public function testReturnsIpRuleBeforeEmailRuleRegardlessOfConfiguredOrder(): void
    {
        $provider = new RuleProvider([
            ['id' => 'login_email', 'key' => 'email'],
            ['id' => 'login_ip', 'key' => 'ip'],
        ]);

        $ids = array_column($provider->rulesFor($this->request()), 'id');

        $this->assertSame(['login_ip', 'login_email'], $ids);
    }

    public function testReturnsOnlyRulesMatchingControllerAndFunction(): void
    {
        $provider = new RuleProvider([
            ['id' => 'contact_ip', 'cl' => 'contact', 'fnc' => 'send', 'key' => 'ip'],
            ['id' => 'login_ip', 'fnc' => ['login', 'login_noredirect'], 'key' => 'ip'],
        ]);

        $ids = array_column($provider->rulesFor($this->request(['cl' => 'Contact', 'fnc' => 'Send'])), 'id');

        $this->assertSame(['contact_ip'], $ids);
    }

    public function testRuleWithoutControllerAndFunctionMatchesEveryRequest(): void
    {
        $provider = new RuleProvider([['id' => 'global', 'key' => 'user']]);

        $ids = array_column($provider->rulesFor($this->request()), 'id');

        $this->assertSame(['global'], $ids);
    }

    public function testReturnsNothingWithoutConfiguredRules(): void
    {
        $this->assertSame([], (new RuleProvider([]))->rulesFor($this->request()));
    }

    public function testReturnsNothingForNonMatchingRequest(): void
    {
        $provider = new RuleProvider([['id' => 'login_ip', 'fnc' => ['login'], 'key' => 'ip']]);

        $this->assertSame([], $provider->rulesFor($this->request(['fnc' => 'tobasket'])));
    }

    /**
     * @param array<string, string> $query
     */
    private function request(array $query = []): Request
    {
        return Request::create('/', 'GET', $query);
    }
}
