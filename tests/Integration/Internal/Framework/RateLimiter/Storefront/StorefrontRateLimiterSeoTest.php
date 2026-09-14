<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Core\SeoDecoder;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimiterInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class StorefrontRateLimiterSeoTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testControllerBoundRuleThrottlesSeoUrl(): void
    {
        $seoUrlPath = $this->givenCategorySeoUrlPath();
        $this->configureLimiter([
            ['id' => 'cat', 'cl' => 'alist', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute'],
        ]);

        $limiter = $this->limiter();

        oxNew(SeoDecoder::class)->processSeoCall($seoUrlPath, '/');

        $limiter->enforce();

        $this->expectException(TooManyRequestsException::class);

        $this->limiter()->enforce();
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
    private function configureLimiter(array $rules): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.10';
        $_SERVER['REQUEST_URI'] = '/';
        $_POST = [];
        $_GET = [];

        $this->createContainer();
        $this->container->register('oxid_esales.rate_limiter.storefront.storage', InMemoryStorage::class);
        $this->container->findDefinition(StorefrontRateLimiterInterface::class)->setShared(false);
        $this->setParameter('oxid_esales.rate_limiter.storefront.rules', $rules);
        $this->setParameter('oxid_esales.rate_limiter.storefront.excluded_routes', []);
        $this->setParameter('oxid_esales.rate_limiter.storefront.excluded_ips', []);
        $this->compileContainer();
    }

    private function limiter(): StorefrontRateLimiterInterface
    {
        return $this->get(StorefrontRateLimiterInterface::class);
    }
}
