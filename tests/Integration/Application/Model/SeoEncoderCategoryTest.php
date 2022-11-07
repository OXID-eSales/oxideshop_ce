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

    private int $subcategoryPerCategory = 2;
    private int $productPerCategory = 3;
    private array $categoryLanguages = ['de' => 0, 'en' => 1];

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
        $expiredRowsCount = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM `oxseo` WHERE `OXSEOURL` like "%www-some-shop/category-%" AND `OXEXPIRED` = "1";'
        );

        $this->assertEquals(
            ($this->productPerCategory + $this->subcategoryPerCategory) * count($this->categoryLanguages),
            $expiredRowsCount
        );
    }

    private function createTestCategoryWithSeoLinks(): Category
    {
        $baseUrl = uniqid('www.some-shop/', true);
        $seoEncoder = oxNew(SeoEncoder::class);

        $mainCategory = oxNew(Category::class);
        $mainCategory->setId(UtilsObject::getInstance()->generateUId());
        $mainCategory->save();

        foreach ($this->categoryLanguages as $languageCode) {
            $mainCategoryUrl = uniqid('www.some-shop/category-', true);
            /** Add entry for main category */
            $seoEncoder->addSeoEntry(
                $mainCategory->getId(),
                1,
                $languageCode,
                $baseUrl,
                $mainCategoryUrl,
                'oxcategory'
            );
            /** Add entries for main sub-categories */
            for ($i = 0; $i < $this->subcategoryPerCategory; $i++) {
                $subCategoryId = UtilsObject::getInstance()->generateUId();
                $this->createSubCategory($mainCategory, $subCategoryId);
                $seoEncoder->addSeoEntry(
                    $subCategoryId,
                    1,
                    $languageCode,
                    $mainCategoryUrl,
                    $mainCategoryUrl . uniqid('/subcategory-', true),
                    'oxcategory'
                );
            }
            /** Add entries for main category products */
            for ($i = 1; $i <= $this->productPerCategory; $i++) {
                $seoEncoder->addSeoEntry(
                    UtilsObject::getInstance()->generateUId(),
                    1,
                    $languageCode,
                    $mainCategoryUrl,
                    $mainCategoryUrl . uniqid('/product-', true),
                    'oxarticle'
                );
            }
        }

        return $mainCategory;
    }

    private function createSubCategory(Category $mainCategory, string $subCategoryId): void
    {
        $subCategory = oxNew(Category::class);
        $subCategory->setId($subCategoryId);
        $subCategory->setParentCategory($mainCategory);
        $subCategory->save();
    }
}
