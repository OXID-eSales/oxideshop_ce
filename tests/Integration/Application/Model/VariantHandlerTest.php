<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\SelectList;
use OxidEsales\Eshop\Application\Model\VariantHandler;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class VariantHandlerTest extends IntegrationTestCase
{
    public function testGenVariantFromSellCopiesMediaFromParent(): void
    {
        $parentId = $this->createParentArticle();
        $selectListId = $this->createSelectList();
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::DETAIL);

        $article = oxNew(Article::class);
        $article->load($parentId);

        $variantHandler = oxNew(VariantHandler::class);
        $variantHandler->genVariantFromSell([$selectListId], $article);

        $variantId = $this->getVariantId($parentId);
        $this->assertCount(1, $this->get(ProductMediaDaoInterface::class)->getAll(Id::fromUid($variantId)));
    }

    public function testGenVariantFromSellCopiesMultipleMedia(): void
    {
        $parentId = $this->createParentArticle();
        $selectListId = $this->createSelectList();
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::ICON);
        $this->addProductMedia(Id::fromUid($parentId), ProductMediaRole::THUMBNAIL);

        $article = oxNew(Article::class);
        $article->load($parentId);

        $variantHandler = oxNew(VariantHandler::class);
        $variantHandler->genVariantFromSell([$selectListId], $article);

        $variantId = $this->getVariantId($parentId);
        $this->assertCount(2, $this->get(ProductMediaDaoInterface::class)->getAll(Id::fromUid($variantId)));
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

    private function createSelectList(): string
    {
        $selectList = oxNew(SelectList::class);
        $selectList->setEnableMultilang(false);
        $selectList->oxselectlist__oxshopid = new Field(1);
        $selectList->oxselectlist__oxtitle = new Field('Size');
        $selectList->oxselectlist__oxtitle_1 = new Field('Size');
        $selectList->oxselectlist__oxvaldesc = new Field('S__@@M__@@L__@@');
        $selectList->oxselectlist__oxvaldesc_1 = new Field('S__@@M__@@L__@@');
        $selectList->save();

        return $selectList->getId();
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
