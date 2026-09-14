<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Event\RateLimitExceededEvent;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimiterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class StorefrontRateLimiterTest extends TestCase
{
    use ContainerTrait;

    #[RunInSeparateProcess]
    public function testGlobalRuleThrottlesAnonymousByIp(): void
    {
        $this->configureLimiter([$this->rule('global', 'user', 1)]);

        $this->limiter()->enforce();

        $this->expectException(TooManyRequestsException::class);

        $this->limiter()->enforce();
    }

    #[RunInSeparateProcess]
    public function testGlobalRuleKeysByLoggedInUser(): void
    {
        $this->configureLimiter([$this->rule('global', 'user', 1)], userId: 'user-x');

        $this->limiter()->enforce();

        $this->expectException(TooManyRequestsException::class);

        $this->limiter()->enforce();
    }

    #[RunInSeparateProcess]
    public function testExcludedIpBypassesAllRules(): void
    {
        $this->configureLimiter([$this->rule('global', 'user', 1)], excludedIps: ['203.0.113.10']);

        $this->limiter()->enforce();
        $this->limiter()->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testExcludedRouteBypassesAllRules(): void
    {
        $this->configureLimiter(
            [$this->rule('global', 'user', 1)],
            excludedRoutes: ['/health'],
            path: '/health'
        );

        $this->limiter()->enforce();
        $this->limiter()->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testActionRuleThrottlesByIp(): void
    {
        $this->configureLimiter(
            [$this->rule('login_ip', 'ip', 1, ['fnc' => ['login', 'login_noredirect']])],
            post: ['fnc' => 'login_noredirect']
        );

        $this->limiter()->enforce();

        $this->expectException(TooManyRequestsException::class);

        $this->limiter()->enforce();
    }

    #[RunInSeparateProcess]
    public function testActionRuleThrottlesByEmail(): void
    {
        $this->configureLimiter(
            [$this->rule('login_email', 'email', 1, ['fnc' => ['login']])],
            post: ['fnc' => 'login', 'lgn_usr' => 'shopper@example.com']
        );

        $this->limiter()->enforce();

        $this->expectException(TooManyRequestsException::class);

        $this->limiter()->enforce();
    }

    #[RunInSeparateProcess]
    public function testActionRuleThrottlesByEmailSubmittedViaGet(): void
    {
        $this->configureLimiter(
            [$this->rule('login_email', 'email', 1, ['fnc' => ['login']])],
            query: ['fnc' => 'login', 'lgn_usr' => 'shopper@example.com']
        );

        $this->limiter()->enforce();

        $this->expectException(TooManyRequestsException::class);

        $this->limiter()->enforce();
    }

    #[RunInSeparateProcess]
    public function testEmailRuleIsSkippedWithoutLoginUser(): void
    {
        $this->configureLimiter(
            [$this->rule('login_email', 'email', 1, ['fnc' => ['login']])],
            post: ['fnc' => 'login']
        );

        $this->limiter()->enforce();
        $this->limiter()->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testControllerBoundRuleIgnoresOtherController(): void
    {
        $this->configureLimiter(
            [$this->rule('contact_ip', 'ip', 1, ['cl' => 'contact', 'fnc' => 'send'])],
            post: ['cl' => 'newsletter', 'fnc' => 'send']
        );

        $this->limiter()->enforce();
        $this->limiter()->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testEnforcesOncePerRequest(): void
    {
        $this->configureLimiter([$this->rule('global', 'user', 1)]);

        $limiter = $this->limiter();
        $limiter->enforce();
        $limiter->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testEmailRuleIsolatesDifferentSenders(): void
    {
        $this->configureLimiter(
            [$this->rule('login_email', 'email', 1, ['fnc' => ['login']])],
            post: ['fnc' => 'login', 'lgn_usr' => 'victim@example.com']
        );

        $this->limiter()->enforce();
        $_SERVER['REMOTE_ADDR'] = '198.51.100.7';
        $this->limiter()->enforce();

        $this->addToAssertionCount(1);
    }

    #[RunInSeparateProcess]
    public function testIpRuleIsEnforcedBeforeUserAndEmailRules(): void
    {
        $this->configureLimiter(
            [
                $this->rule('login_email', 'email', 1, ['fnc' => ['login']]),
                $this->rule('login_ip', 'ip', 1, ['fnc' => ['login']]),
            ],
            post: ['fnc' => 'login', 'lgn_usr' => 'shopper@example.com']
        );
        $events = [];
        $this->captureRateLimitEvents($events);

        $this->limiter()->enforce();

        try {
            $this->limiter()->enforce();
            $this->fail('Second request was not blocked');
        } catch (TooManyRequestsException) {
        }

        $this->assertCount(1, $events);
        $this->assertSame('login_ip', $events[0]->getRuleId());
    }

    #[RunInSeparateProcess]
    public function testBlockedRequestDispatchesRateLimitExceededEvent(): void
    {
        $this->configureLimiter([$this->rule('global', 'user', 1)]);
        $events = [];
        $this->captureRateLimitEvents($events);

        $this->limiter()->enforce();

        try {
            $this->limiter()->enforce();
            $this->fail('Second request was not blocked');
        } catch (TooManyRequestsException) {
        }

        $this->assertCount(1, $events);
        $this->assertSame('global', $events[0]->getRuleId());
        $this->assertSame(hash('sha256', '203.0.113.10'), $events[0]->getKey());
        $this->assertGreaterThan(0, $events[0]->getRetryAfter());
    }

    #[RunInSeparateProcess]
    public function testAcceptedRequestDispatchesNoEvent(): void
    {
        $this->configureLimiter([$this->rule('global', 'user', 2)]);
        $events = [];
        $this->captureRateLimitEvents($events);

        $this->limiter()->enforce();
        $this->limiter()->enforce();

        $this->assertCount(0, $events);
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
     * @param array<string, string> $query
     * @param string[] $excludedRoutes
     * @param string[] $excludedIps
     */
    private function configureLimiter(
        array $rules,
        string $userId = '',
        array $post = [],
        array $query = [],
        array $excludedRoutes = [],
        array $excludedIps = [],
        string $path = '/'
    ): void {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.10';
        $_SERVER['REQUEST_URI'] = $path;
        $_POST = $post;
        $_GET = $query;

        $this->createContainer();
        $this->container->register('oxid_esales.rate_limiter.storefront.storage', InMemoryStorage::class);
        $this->container->register(SessionInterface::class)->setSynthetic(true)->setPublic(true);
        $this->container->findDefinition(StorefrontRateLimiterInterface::class)->setShared(false);
        $this->setParameter('oxid_esales.rate_limiter.storefront.rules', $rules);
        $this->setParameter('oxid_esales.rate_limiter.storefront.excluded_routes', $excludedRoutes);
        $this->setParameter('oxid_esales.rate_limiter.storefront.excluded_ips', $excludedIps);
        $this->compileContainer();
        $this->container->set(SessionInterface::class, $this->session($userId));
    }

    private function limiter(): StorefrontRateLimiterInterface
    {
        return $this->get(StorefrontRateLimiterInterface::class);
    }

    /**
     * @param RateLimitExceededEvent[] $events
     */
    private function captureRateLimitEvents(array &$events): void
    {
        $this->get(EventDispatcherInterface::class)->addListener(
            RateLimitExceededEvent::class,
            static function (RateLimitExceededEvent $event) use (&$events): void {
                $events[] = $event;
            }
        );
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
