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
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\SeoDecoder;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SeoEncoderCategoryTest extends IntegrationTestCase
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

    public function testOnDeleteCategoryArchivesArticleSeoUrlsAndCreatesFallbackSeoUrl(): void
    {
        $categoryId = (string) Id::generate();
        $articleId = (string) Id::generate();
        $variantId = (string) Id::generate();
        $this->createCategory($categoryId, 'Test Category');
        $this->createArticle($articleId, 'Test Article', '1001');
        $this->createVariantArticle($variantId, $articleId, 'Test Variant', '1001-1');
        $this->assignArticleToCategory($articleId, $categoryId, 1);

        $category = oxNew(Category::class);
        $category->load($categoryId);
        $seoEncoderCategory = oxNew(SeoEncoderCategory::class);
        $seoEncoderCategory->getCategoryUri($category, 0);

        $article = oxNew(Article::class);
        $article->load($articleId);
        $variantArticle = oxNew(Article::class);
        $variantArticle->load($variantId);
        $seoEncoderArticle = oxNew(SeoEncoderArticle::class);
        $oldSeoUrl = $seoEncoderArticle->getArticleMainUri($article, 0);
        $oldVariantSeoUrl = $seoEncoderArticle->getArticleMainUri($variantArticle, 0);

        $this->removeCategoryAssignment($articleId, $categoryId);
        $seoEncoderCategory->onDeleteCategory($category);

        $currentSeo = $this->getSeoEntry($articleId);
        $currentVariantSeo = $this->getSeoEntry($variantId);

        $this->assertSame(0, $this->getRemovedSeoCount($articleId, $categoryId));
        $this->assertSame(1, $this->getSeoHistoryCount($articleId, $oldSeoUrl));
        $this->assertFalse(oxNew(SeoDecoder::class)->decodeUrl($oldSeoUrl));
        $this->assertSame(0, $this->getRemovedSeoCount($variantId, $categoryId));
        $this->assertSame(1, $this->getSeoHistoryCount($variantId, $oldVariantSeoUrl));
        $this->assertFalse(oxNew(SeoDecoder::class)->decodeUrl($oldVariantSeoUrl));
        $this->assertEmpty($currentSeo['oxparams']);
        $this->assertEmpty($currentVariantSeo['oxparams']);
        $this->assertSame(0, $this->getSeoCount($categoryId, 'oxcategory'));
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

    private function getSeoEntry(string $articleId): array
    {
        return $this->get(QueryBuilderFactoryInterface::class)->create()
            ->select('oxseourl', 'oxparams')
            ->from('oxseo')
            ->where('oxobjectid = :articleId')
            ->setParameter('articleId', $articleId)
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();
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

    private function getSeoCount(string $objectId, string $type): int
    {
        return (int) $this->get(QueryBuilderFactoryInterface::class)->create()
            ->select('count(*)')
            ->from('oxseo')
            ->where('oxobjectid = :objectId')
            ->andWhere('oxtype = :type')
            ->setParameters([
                'objectId' => $objectId,
                'type' => $type,
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
}
