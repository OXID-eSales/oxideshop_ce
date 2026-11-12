<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimiterInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class StorefrontRateLimiterSeoTest extends IntegrationTestCase
{
    use ContainerTrait;

    private array $server;

    public function setUp(): void
    {
        parent::setUp();

        $this->server = $_SERVER;
    }

    public function tearDown(): void
    {
        $_SERVER = $this->server;
        $_GET = [];

        parent::tearDown();
    }

    public function testControllerBoundRuleThrottlesSeoUrl(): void
    {
        $this->configureLimiter(
            $this->givenCategorySeoUrlPath(),
            [['id' => 'cat', 'cl' => 'alist', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute']]
        );
        $request = clone $this->get(Request::class);
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response());
        $kernel = $this->get(HttpKernelInterface::class);

        $accepted = $kernel->handle($request);
        $rejected = $kernel->handle($request);

        $this->assertSame(200, $accepted->getStatusCode());
        $this->assertSame(429, $rejected->getStatusCode());
    }

    private function givenCategorySeoUrlPath(): string
    {
        $category = oxNew(Category::class);
        $category->setId('ratelimit-test-category');
        $category->assign(['oxtitle' => 'RateLimitCategory', 'oxactive' => 1, 'oxparentid' => 'oxrootid']);
        $category->save();

        $storedCategory = oxNew(Category::class);
        $storedCategory->load('ratelimit-test-category');

        $categoryUrl = oxNew(SeoEncoderCategory::class)->getCategoryUrl($storedCategory);

        return parse_url($categoryUrl, PHP_URL_PATH) ?: '/';
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     */
    private function configureLimiter(string $requestPath, array $rules): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.10';
        $_SERVER['REQUEST_URI'] = $requestPath;
        $_SERVER['SCRIPT_NAME'] = '/oxseo.php';
        $_POST = [];
        $_GET = [];

        $this->createContainer();
        $this->container->register('oxid_esales.rate_limiter.storefront.storage', InMemoryStorage::class);
        $this->container->findDefinition(StorefrontRateLimiterInterface::class)->setShared(false);
        $this->container->setParameter('oxid_esales.rate_limiter.storefront.rules', $rules);
        $this->container->setParameter('oxid_esales.rate_limiter.storefront.excluded_routes', []);
        $this->container->setParameter('oxid_esales.rate_limiter.storefront.excluded_ips', []);
        $this->compileContainer();
    }
}
