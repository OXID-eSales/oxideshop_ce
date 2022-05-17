<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\EshopCommunity\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\UnitTestCase;

final class SeoEncoderCategoryTest extends UnitTestCase
{
    use ContainerTrait;

    public function testOnDeleteCategoryWillSetDependantRecordsToExpired(): void
    {
        $seoEncoderCategory = oxNew(SeoEncoderCategory::class);
        $category = $this->createTestCategoryWithSeoLinks();

        $seoEncoderCategory->onDeleteCategory($category);

        $this->assertSeoUrlsAreExpired();
    }

    private function assertSeoUrlsAreExpired(): void
    {
        $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
        $expiredRowsCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM `oxseo` WHERE `OXSEOURL` like "www-some-shop/category-%" AND `OXEXPIRED` = "1";');

        $this->assertEquals($this->getCountOfProductsPerCategory(), $expiredRowsCount);
    }

    private function createTestCategoryWithSeoLinks(): Category
    {
        $baseUrl = uniqid('www.some-shop/', true);
        $seoEncoder = oxNew(SeoEncoder::class);
        $utils = new UtilsObject();

        $category = oxNew(Category::class);
        $category->setId($utils->generateUId());
        $category->save();

        $categoryUrl = uniqid('www.some-shop/category-', true);
        $seoEncoder->addSeoEntry(
            $category->getId(),
            1,
            0,
            $baseUrl,
            $categoryUrl,
            'oxcategory'
        );

        $productCount = $this->getCountOfProductsPerCategory();
        while ($productCount > 0) {
            $seoEncoder->addSeoEntry(
                $utils->generateUId(),
                1,
                0,
                $categoryUrl,
                $categoryUrl . uniqid('/product-', true),
                'oxarticle'
            );
            $productCount--;
        }

        return $category;
    }

    private function getCountOfProductsPerCategory(): int
    {
        return 2;
    }
}
