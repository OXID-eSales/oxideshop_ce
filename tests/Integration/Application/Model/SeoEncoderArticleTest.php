<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Object2Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\SeoDecoder;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SeoEncoderArticleTest extends IntegrationTestCase
{
    private int $shopId;

    public function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->init();
        Registry::getConfig()->setAdminMode(true);
        Registry::getConfig()->setConfigParam('blEnableSeoCache', false);
        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setAdminMode(false);

        parent::tearDown();
    }

    public function testRemoveFromCategoriesCreatesFallbackSeoUrlForArticleWithoutCategories(): void
    {
        $categoryId = (string) Id::generate();
        $articleId = (string) Id::generate();
        $this->createCategory($categoryId, 'Test Category');
        $this->createArticle($articleId, 'Test Article', '1001');
        $this->assignArticleToCategory($articleId, $categoryId, 1);

        $article = oxNew(Article::class);
        $article->load($articleId);
        $seoEncoder = oxNew(SeoEncoderArticle::class);
        $oldSeoUrl = $seoEncoder->getArticleMainUri($article, 0);

        $this->removeCategoryAssignment($articleId, $categoryId);
        $seoEncoder->removeFromCategories([$articleId], [$categoryId]);

        $currentSeo = $this->getSeoEntry($articleId);

        $this->assertSame(0, $this->getRemovedSeoCount($articleId, $categoryId));
        $this->assertSame(1, $this->getSeoHistoryCount($articleId, $oldSeoUrl));
        $this->assertFalse(oxNew(SeoDecoder::class)->decodeUrl($oldSeoUrl));
        $this->assertSame('Test-Article.html', $currentSeo['oxseourl']);
        $this->assertEmpty($currentSeo['oxparams']);
    }

    public function testRemovedCategorySeoUrlRedirectsPermanentlyToCurrentSeoUrl(): void
    {
        $categoryId = (string) Id::generate();
        $articleId = (string) Id::generate();
        $this->createCategory($categoryId, 'Test Category');
        $this->createArticle($articleId, 'Test Article', '1004');
        $this->assignArticleToCategory($articleId, $categoryId, 1);

        $article = oxNew(Article::class);
        $article->load($articleId);
        $seoEncoder = oxNew(SeoEncoderArticle::class);
        $oldSeoUrl = $seoEncoder->getArticleMainUri($article, 0);

        $this->removeCategoryAssignment($articleId, $categoryId);
        $seoEncoder->removeFromCategories([$articleId], [$categoryId]);

        $currentSeo = $this->getSeoEntry($articleId);

        $utils = $this->getMockBuilder(Utils::class)
            ->onlyMethods(['redirect'])
            ->getMock();
        $utils->expects($this->once())
            ->method('redirect')
            ->with(
                Registry::getConfig()->getShopURL() . $currentSeo['oxseourl'],
                false,
                301
            );
        Registry::set(Utils::class, $utils);

        oxNew(SeoDecoder::class)->processSeoCall($oldSeoUrl, '');

        $this->assertSame(1, $this->getSeoHistoryHits($articleId, $oldSeoUrl));
    }

    public function testRemoveFromCategoriesCreatesSeoUrlForRemainingMainCategory(): void
    {
        $oldCategoryId = (string) Id::generate();
        $newCategoryId = (string) Id::generate();
        $articleId = (string) Id::generate();
        $this->createCategory($oldCategoryId, 'Old Category');
        $this->createCategory($newCategoryId, 'New Category');
        $this->createArticle($articleId, 'Test Article', '1002');
        $this->assignArticleToCategory($articleId, $oldCategoryId, 1);
        $this->assignArticleToCategory($articleId, $newCategoryId, 2);

        $article = oxNew(Article::class);
        $article->load($articleId);
        $seoEncoder = oxNew(SeoEncoderArticle::class);
        $oldSeoUrl = $seoEncoder->getArticleMainUri($article, 0);

        $this->removeCategoryAssignment($articleId, $oldCategoryId);
        $seoEncoder->removeFromCategories([$articleId], [$oldCategoryId]);

        $currentSeo = $this->getSeoEntry($articleId, $newCategoryId);

        $this->assertSame(0, $this->getRemovedSeoCount($articleId, $oldCategoryId));
        $this->assertSame(1, $this->getSeoHistoryCount($articleId, $oldSeoUrl));
        $this->assertFalse(oxNew(SeoDecoder::class)->decodeUrl($oldSeoUrl));
        $this->assertSame('New-Category/Test-Article.html', $currentSeo['oxseourl']);
        $this->assertSame($newCategoryId, $currentSeo['oxparams']);
    }

    public function testRemoveFromCategoriesAlsoArchivesVariantSeoUrlsWhenParentCategoryIsRemoved(): void
    {
        $categoryId = (string) Id::generate();
        $parentArticleId = (string) Id::generate();
        $variantArticleId = (string) Id::generate();
        $this->createCategory($categoryId, 'Test Category');
        $this->createArticle($parentArticleId, 'Test Article', '1003');
        $this->createVariantArticle($variantArticleId, $parentArticleId, 'Test Variant', '1003-1');
        $this->assignArticleToCategory($parentArticleId, $categoryId, 1);

        $parentArticle = oxNew(Article::class);
        $parentArticle->load($parentArticleId);
        $variantArticle = oxNew(Article::class);
        $variantArticle->load($variantArticleId);
        $seoEncoder = oxNew(SeoEncoderArticle::class);
        $parentOldSeoUrl = $seoEncoder->getArticleMainUri($parentArticle, 0);
        $variantOldSeoUrl = $seoEncoder->getArticleMainUri($variantArticle, 0);

        $this->removeCategoryAssignment($parentArticleId, $categoryId);
        $seoEncoder->removeFromCategories([$parentArticleId], [$categoryId]);

        $currentVariantSeo = $this->getSeoEntry($variantArticleId);

        $this->assertSame(0, $this->getRemovedSeoCount($parentArticleId, $categoryId));
        $this->assertSame(1, $this->getSeoHistoryCount($parentArticleId, $parentOldSeoUrl));
        $this->assertFalse(oxNew(SeoDecoder::class)->decodeUrl($parentOldSeoUrl));
        $this->assertSame(0, $this->getRemovedSeoCount($variantArticleId, $categoryId));
        $this->assertSame(1, $this->getSeoHistoryCount($variantArticleId, $variantOldSeoUrl));
        $this->assertFalse(oxNew(SeoDecoder::class)->decodeUrl($variantOldSeoUrl));
        $this->assertEmpty($currentVariantSeo['oxparams']);
    }

    private function createCategory(string $id, string $title): void
    {
        $category = oxNew(Category::class);
        $category->setId($id);
        $category->oxcategories__oxtitle = new Field($title);
        $category->oxcategories__oxactive = new Field(1);
        $category->oxcategories__oxparentid = new Field('oxrootid');
        $category->oxcategories__oxrootid = new Field($id);
        $category->save();
    }

    private function createArticle(string $id, string $title, string $articleNumber): void
    {
        $article = oxNew(Article::class);
        $article->setId($id);
        $article->oxarticles__oxtitle = new Field($title);
        $article->oxarticles__oxartnum = new Field($articleNumber);
        $article->oxarticles__oxactive = new Field(1);
        $article->save();
    }

    private function createVariantArticle(
        string $id,
        string $parentArticleId,
        string $title,
        string $articleNumber
    ): void {
        $variant = oxNew(Article::class);
        $variant->setId($id);
        $variant->oxarticles__oxparentid = new Field($parentArticleId);
        $variant->oxarticles__oxtitle = new Field($title);
        $variant->oxarticles__oxartnum = new Field($articleNumber);
        $variant->oxarticles__oxactive = new Field(1);
        $variant->save();
    }

    private function assignArticleToCategory(string $articleId, string $categoryId, int $time): void
    {
        $relation = oxNew(Object2Category::class);
        $relation->setId((string) Id::generate());
        $relation->oxobject2category__oxobjectid = new Field($articleId);
        $relation->oxobject2category__oxcatnid = new Field($categoryId);
        $relation->oxobject2category__oxshopid = new Field($this->shopId);
        $relation->oxobject2category__oxtime = new Field($time);
        $relation->save();
    }

    private function removeCategoryAssignment(string $articleId, string $categoryId): void
    {
        $this->get(QueryBuilderFactoryInterface::class)->create()
            ->delete('oxobject2category')
            ->where('oxobjectid = :articleId')
            ->andWhere('oxcatnid = :categoryId')
            ->setParameters([
                'articleId' => $articleId,
                'categoryId' => $categoryId,
            ])
            ->execute();
    }

    private function getSeoEntry(string $articleId, ?string $categoryId = null): array
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('oxseourl', 'oxparams')
            ->from('oxseo')
            ->where('oxobjectid = :articleId')
            ->setParameter('articleId', $articleId)
            ->setMaxResults(1);

        if ($categoryId !== null) {
            $queryBuilder->andWhere('oxparams = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $queryBuilder->execute()->fetchAssociative();
    }

    private function getRemovedSeoCount(string $articleId, string $categoryId): int
    {
        return (int) $this->get(QueryBuilderFactoryInterface::class)->create()
            ->select('count(*)')
            ->from('oxseo')
            ->where('oxobjectid = :articleId')
            ->andWhere('oxparams = :categoryId')
            ->setParameters([
                'articleId' => $articleId,
                'categoryId' => $categoryId,
            ])
            ->execute()
            ->fetchOne();
    }

    private function getSeoHistoryCount(string $articleId, string $seoUrl): int
    {
        return (int) $this->get(QueryBuilderFactoryInterface::class)->create()
            ->select('count(*)')
            ->from('oxseohistory')
            ->where('oxobjectid = :articleId')
            ->andWhere('oxident = :ident')
            ->andWhere('oxshopid = :shopId')
            ->setParameters([
                'articleId' => $articleId,
                'ident' => md5(strtolower($seoUrl)),
                'shopId' => $this->shopId,
            ])
            ->execute()
            ->fetchOne();
    }

    private function getSeoHistoryHits(string $articleId, string $seoUrl): int
    {
        return (int) $this->get(QueryBuilderFactoryInterface::class)->create()
            ->select('oxhits')
            ->from('oxseohistory')
            ->where('oxobjectid = :articleId')
            ->andWhere('oxident = :ident')
            ->andWhere('oxshopid = :shopId')
            ->setParameters([
                'articleId' => $articleId,
                'ident' => md5(strtolower($seoUrl)),
                'shopId' => $this->shopId,
            ])
            ->execute()
            ->fetchOne();
    }
}
