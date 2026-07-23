<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Core\SeoDecoder;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SeoDecoderTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $_GET = [];
    }

    public function tearDown(): void
    {
        $_GET = [];

        parent::tearDown();
    }

    public function testProcessSeoCallDecodesKnownUrlIntoRequestParameters(): void
    {
        oxNew(SeoDecoder::class)->processSeoCall($this->createCategorySeoUrlPath(), '/');

        $this->assertSame('alist', $_GET['cl']);
    }

    public function testProcessSeoCallLeavesUnknownUrlUnrouted(): void
    {
        oxNew(SeoDecoder::class)->processSeoCall('/retired-url/', '/');

        $this->assertArrayNotHasKey('cl', $_GET);
    }

    private function createCategorySeoUrlPath(): string
    {
        $category = oxNew(Category::class);
        $category->setId('seo-decoder-test-category');
        $category->assign(['oxtitle' => 'SeoDecoderCategory', 'oxactive' => 1, 'oxparentid' => 'oxrootid']);
        $category->save();
        $storedCategory = oxNew(Category::class);
        $storedCategory->load('seo-decoder-test-category');

        return parse_url(oxNew(SeoEncoderCategory::class)->getCategoryUrl($storedCategory), PHP_URL_PATH);
    }
}
