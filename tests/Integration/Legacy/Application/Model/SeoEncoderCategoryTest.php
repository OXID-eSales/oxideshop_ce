<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Model;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\EshopCommunity\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SeoEncoderCategoryTest extends IntegrationTestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    private int $subcategoryPerCategory = 2;

    private int $productPerCategory = 3;

    private array $categoryLanguages = [
        'de' => 0,
        'en' => 1,
    ];

    public function setUp(): void
    {
        $this->replaceContainerInstance();
        $this->resetDatabaseProvider();

        parent::setUp();
    }

    public function testOnDeleteCategoryWillSetDependantRecordsToExpired(): void
    {
        $seoEncoderCategory = oxNew(SeoEncoderCategory::class);
        $category = $this->createTestCategoryWithSeoLinks();
        $expectedRowCount =
            ($this->productPerCategory + $this->subcategoryPerCategory) * count($this->categoryLanguages);

        $seoEncoderCategory->onDeleteCategory($category);

        $expiredRowCount = $this->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection()
            ->fetchOne(
                'SELECT COUNT(*) FROM `oxseo` WHERE `OXSEOURL` like "%www-some-shop/category-%" AND `OXEXPIRED` = "1";'
            );
        $this->assertSame($expectedRowCount, $expiredRowCount);
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
