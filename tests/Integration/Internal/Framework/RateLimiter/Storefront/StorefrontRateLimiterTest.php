<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimiterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class StorefrontRateLimiterTest extends TestCase
{
    use ContainerTrait;

    private const PREFIX = 'oxid_esales.rate_limiter.storefront.';
    private const CLIENT_IP = '203.0.113.10';

    #[RunInSeparateProcess]
    public function testGlobalRuleThrottlesAnonymousByIp(): void
    {
        $rateLimiter = $this->createLimiter([$this->rule('global', 'user', 1)]);

        $rateLimiter->enforce();

        $this->expectException(TooManyRequestsException::class);

        $rateLimiter->enforce();
    }

    #[RunInSeparateProcess]
    public function testGlobalRuleKeysByLoggedInUser(): void
    {
        $rateLimiter = $this->createLimiter([$this->rule('global', 'user', 1)], userId: 'user-x');

        $rateLimiter->enforce();

        $this->expectException(TooManyRequestsException::class);

        $rateLimiter->enforce();
    }

    #[RunInSeparateProcess]
    public function testExcludedIpBypassesAllRules(): void
    {
        $rateLimiter = $this->createLimiter([$this->rule('global', 'user', 1)], excludedIps: [self::CLIENT_IP]);

        $rateLimiter->enforce();
        $rateLimiter->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testExcludedRouteBypassesAllRules(): void
    {
        $rateLimiter = $this->createLimiter(
            [$this->rule('global', 'user', 1)],
            excludedRoutes: ['/health'],
            path: '/health'
        );

        $rateLimiter->enforce();
        $rateLimiter->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testActionRuleThrottlesByIp(): void
    {
        $rateLimiter = $this->createLimiter(
            [$this->rule('login_ip', 'ip', 1, ['fnc' => ['login', 'login_noredirect']])],
            post: ['fnc' => 'login_noredirect']
        );

        $rateLimiter->enforce();

        $this->expectException(TooManyRequestsException::class);

        $rateLimiter->enforce();
    }

    #[RunInSeparateProcess]
    public function testActionRuleThrottlesByEmail(): void
    {
        $rateLimiter = $this->createLimiter(
            [$this->rule('login_email', 'email', 1, ['fnc' => ['login']])],
            post: ['fnc' => 'login', 'lgn_usr' => 'shopper@example.com']
        );

        $rateLimiter->enforce();

        $this->expectException(TooManyRequestsException::class);

        $rateLimiter->enforce();
    }

    #[RunInSeparateProcess]
    public function testEmailRuleIsSkippedWithoutLoginUser(): void
    {
        $rateLimiter = $this->createLimiter(
            [$this->rule('login_email', 'email', 1, ['fnc' => ['login']])],
            post: ['fnc' => 'login']
        );

        $rateLimiter->enforce();
        $rateLimiter->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testControllerBoundRuleIgnoresOtherController(): void
    {
        $rateLimiter = $this->createLimiter(
            [$this->rule('contact_ip', 'ip', 1, ['cl' => 'contact', 'fnc' => 'send'])],
            post: ['cl' => 'newsletter', 'fnc' => 'send']
        );

        $rateLimiter->enforce();
        $rateLimiter->enforce();

        $this->addToAssertionCount(1);
    }

    /**
     * @param array<string, mixed> $match
     * @return array<string, mixed>
     */
    private function rule(string $id, string $key, int $limit, array $match = []): array
    {
        return array_merge(['id' => $id, 'key' => $key, 'limit' => $limit, 'interval' => '1 minute'], $match);
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     * @param array<string, string> $post
     * @param string[] $excludedRoutes
     * @param string[] $excludedIps
     */
    private function createLimiter(
        array $rules,
        string $userId = '',
        array $post = [],
        array $excludedRoutes = [],
        array $excludedIps = [],
        string $path = '/'
    ): StorefrontRateLimiterInterface {
        $_SERVER['REMOTE_ADDR'] = self::CLIENT_IP;
        $_SERVER['REQUEST_URI'] = $path;
        $_POST = $post;

        $this->createContainer();
        $this->container->register(self::PREFIX . 'storage', InMemoryStorage::class);
        $this->container->register(SessionInterface::class)->setSynthetic(true)->setPublic(true);
        $this->setParameter(self::PREFIX . 'rules', $rules);
        $this->setParameter(self::PREFIX . 'excluded_routes', $excludedRoutes);
        $this->setParameter(self::PREFIX . 'excluded_ips', $excludedIps);
        $this->compileContainer();
        $this->container->set(SessionInterface::class, $this->session($userId));

        return $this->get(StorefrontRateLimiterInterface::class);
    }

    private function session(string $userId): SessionInterface
    {
        return new class ($userId) implements SessionInterface {
            public function __construct(private string $userId)
            {
            }

            public function get(string $name, mixed $default = null): mixed
            {
                return $name === 'usr' && $this->userId !== '' ? $this->userId : $default;
            }

            public function has(string $name): bool
            {
                return $name === 'usr' && $this->userId !== '';
            }

            public function set(string $name, mixed $value): void
            {
            }

            public function remove(string $name): mixed
            {
                return null;
            }
        };
    }
}
