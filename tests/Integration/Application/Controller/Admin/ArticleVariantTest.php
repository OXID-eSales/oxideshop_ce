<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ArticleVariant;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ArticleVariantTest extends IntegrationTestCase
{
    public function testSaveNewVariantCopiesMediaFromParent(): void
    {
        $parentId = $this->createParentArticle();
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::DETAIL);

        $controller = oxNew(ArticleVariant::class);
        $controller->setEditObjectId($parentId);
        $controller->savevariant('-1', ['oxarticles__oxvarselect' => 'Test Variant']);

        $variantId = $this->getVariantId($parentId);
        $this->assertCount(1, $this->get(ProductMediaDaoInterface::class)->getAll(Id::fromUid($variantId)));
    }

    public function testSaveNewVariantCopiesMultipleMedia(): void
    {
        $parentId = $this->createParentArticle();
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::ICON);
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::THUMBNAIL);
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::DETAIL);

        $controller = oxNew(ArticleVariant::class);
        $controller->setEditObjectId($parentId);
        $controller->savevariant('-1', ['oxarticles__oxvarselect' => 'Test Variant']);

        $variantId = $this->getVariantId($parentId);
        $this->assertCount(3, $this->get(ProductMediaDaoInterface::class)->getAll(Id::fromUid($variantId)));
    }

    public function testSaveNewVariantPreservesMediaRoles(): void
    {
        $parentId = $this->createParentArticle();
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::ICON);

        $controller = oxNew(ArticleVariant::class);
        $controller->setEditObjectId($parentId);
        $controller->savevariant('-1', ['oxarticles__oxvarselect' => 'Test Variant']);

        $variantId = $this->getVariantId($parentId);
        $variantMedia = $this->get(ProductMediaDaoInterface::class)->getAll(Id::fromUid($variantId))->first();
        $this->assertTrue($variantMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
    }

    public function testSaveExistingVariantDoesNotCopyMedia(): void
    {
        $parentId = $this->createParentArticle();
        $variantId = $this->createVariantArticle($parentId);
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::DETAIL);

        $controller = oxNew(ArticleVariant::class);
        $controller->setEditObjectId($parentId);
        $controller->savevariant($variantId, ['oxarticles__oxvarselect' => 'Updated Variant']);

        $this->assertCount(0, $this->get(ProductMediaDaoInterface::class)->getAll(Id::fromUid($variantId)));
    }

    private function createParentArticle(): string
    {
        $article = oxNew(Article::class);
        $article->oxarticles__oxshopid = new Field(1);
        $article->oxarticles__oxactive = new Field(1);
        $article->oxarticles__oxtitle = new Field('Parent Article');
        $article->oxarticles__oxprice = new Field(10.0);
        $article->save();

        return $article->getId();
    }

    private function createVariantArticle(string $parentId): string
    {
        $article = oxNew(Article::class);
        $article->oxarticles__oxshopid = new Field(1);
        $article->oxarticles__oxparentid = new Field($parentId);
        $article->oxarticles__oxvarselect = new Field('Existing Variant');
        $article->save();

        return $article->getId();
    }

    private function getVariantId(string $parentId): string
    {
        $parent = oxNew(Article::class);
        $parent->load($parentId);

        return $parent->getAdminVariants()->current()->getId();
    }

    private function addProductMedia(Id $productId, string $role): void
    {
        $productMedia = new ProductMedia(
            Id::generate(),
            $productId,
            new Media(Id::generate(), new MediaPath('out/pictures/media/test.jpg'), new MediaType('image/jpeg')),
            new ProductMediaRoleSet(ProductMediaRole::from($role)),
        );
        $this->get(ProductMediaServiceInterface::class)->add($productMedia);
    }
}
