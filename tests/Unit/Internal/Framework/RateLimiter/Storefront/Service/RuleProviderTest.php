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
        $matchingRules = $this->matchingRules([
            ['id' => 'login_email', 'key' => 'email'],
            ['id' => 'login_ip', 'key' => 'ip'],
        ]);

        $ids = array_column($matchingRules, 'id');

        $this->assertSame(['login_ip', 'login_email'], $ids);
    }

    public function testReturnsOnlyRulesMatchingControllerAndFunction(): void
    {
        $matchingRules = $this->matchingRules(
            [
                ['id' => 'contact_ip', 'cl' => 'contact', 'fnc' => 'send', 'key' => 'ip'],
                ['id' => 'login_ip', 'fnc' => ['login', 'login_noredirect'], 'key' => 'ip'],
            ],
            ['cl' => 'Contact', 'fnc' => 'Send'],
        );

        $ids = array_column($matchingRules, 'id');

        $this->assertSame(['contact_ip'], $ids);
    }

    public function testRuleWithoutControllerAndFunctionMatchesEveryRequest(): void
    {
        $matchingRules = $this->matchingRules([['id' => 'global', 'key' => 'user']]);

        $ids = array_column($matchingRules, 'id');

        $this->assertSame(['global'], $ids);
    }

    public function testFormatBoundRuleMatchesOnlyRequestsOfThatFormat(): void
    {
        $rules = [
            ['id' => 'api', 'format' => 'json', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute'],
            ['id' => 'pages', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute'],
        ];
        $jsonRequest = Request::create('/api/products');
        $jsonRequest->attributes->set('_format', 'json');

        $this->assertSame(
            ['api', 'pages'],
            array_column(new RuleProvider($rules)->getMatchingRules($jsonRequest), 'id')
        );
        $this->assertSame(
            ['pages'],
            array_column(new RuleProvider($rules)->getMatchingRules(Request::create('/')), 'id')
        );
    }

    public function testReturnsNothingWithoutConfiguredRules(): void
    {
        $this->assertSame([], $this->matchingRules([]));
    }

    public function testReturnsNothingForNonMatchingRequest(): void
    {
        $matchingRules = $this->matchingRules(
            [['id' => 'login_ip', 'fnc' => ['login'], 'key' => 'ip']],
            ['fnc' => 'tobasket'],
        );

        $this->assertSame([], $matchingRules);
    }

    public function testPostParameterWinsOverQueryParameter(): void
    {
        $matchingRules = $this->matchingRules(
            [['id' => 'login_ip', 'fnc' => ['login'], 'key' => 'ip']],
            ['fnc' => 'login'],
            ['fnc' => 'start'],
        );

        $this->assertSame(['login_ip'], array_column($matchingRules, 'id'));
    }

    public function testFunctionBoundRuleNeverMatchesNonScalarParameter(): void
    {
        $matchingRules = $this->matchingRules(
            [['id' => 'trap', 'fnc' => ['array'], 'key' => 'ip']],
            ['fnc' => ['array']],
        );

        $this->assertSame([], $matchingRules);
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     * @param array<string, mixed> $post
     * @param array<string, mixed> $query
     * @return array<int, array<string, mixed>>
     */
    private function matchingRules(array $rules, array $post = [], array $query = []): array
    {
        return new RuleProvider($rules)->getMatchingRules(
            Request::create('/?' . http_build_query($query), 'POST', $post)
        );
    }
}
