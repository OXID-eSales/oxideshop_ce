<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductVariantMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ProductVariantMediaServiceTest extends IntegrationTestCase
{
    private Id $parentId;
    private Id $variantId;

    public function setUp(): void
    {
        parent::setUp();
        $this->parentId = Id::generate();
        $this->variantId = Id::generate();
    }

    public function testCopyMediaFromParentToVariant(): void
    {
        $parentMedia = $this->createProductMedia($this->parentId);
        $this->get(ProductMediaServiceInterface::class)->add($parentMedia);

        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $variantMediaCollection = $this->get(ProductMediaDaoInterface::class)->getAll($this->variantId);

        $this->assertCount(1, $variantMediaCollection);
        $this->assertEquals(
            $parentMedia->getMedia()->getId(),
            $variantMediaCollection->first()->getMedia()->getId()
        );
    }

    public function testCopyMediaPreservesRoles(): void
    {
        $parentMedia = $this->createProductMedia(
            $this->parentId,
            ProductMediaRole::from(ProductMediaRole::ICON),
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)
        );
        $this->get(ProductMediaServiceInterface::class)->add($parentMedia);

        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $variantMedia = $this->get(ProductMediaDaoInterface::class)->getAll($this->variantId)->first();

        $this->assertTrue($variantMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($variantMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
    }

    public function testCopyMediaPreservesPosition(): void
    {
        $parentMedia = $this->createProductMedia($this->parentId);
        $parentMedia->setPosition(5);
        $this->get(ProductMediaServiceInterface::class)->add($parentMedia);

        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $this->assertEquals(5, $this->get(ProductMediaDaoInterface::class)->getAll($this->variantId)->first()->getPosition());
    }

    public function testCopyMediaPreservesInactiveStatus(): void
    {
        $parentMedia = $this->createProductMedia($this->parentId);
        $parentMedia->deactivate();
        $this->get(ProductMediaServiceInterface::class)->add($parentMedia);

        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $this->assertFalse($this->get(ProductMediaDaoInterface::class)->getAll($this->variantId)->first()->isActive());
    }

    public function testCopyMultipleMedia(): void
    {
        $this->get(ProductMediaServiceInterface::class)->add($this->createProductMedia($this->parentId));
        $this->get(ProductMediaServiceInterface::class)->add($this->createProductMedia($this->parentId));
        $this->get(ProductMediaServiceInterface::class)->add($this->createProductMedia($this->parentId));

        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $this->assertCount(3, $this->get(ProductMediaDaoInterface::class)->getAll($this->variantId));
    }

    public function testCopyFromParentWithNoMedia(): void
    {
        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $this->assertCount(0, $this->get(ProductMediaDaoInterface::class)->getAll($this->variantId));
    }

    public function testVariantMediaHasNewId(): void
    {
        $parentMedia = $this->createProductMedia($this->parentId);
        $this->get(ProductMediaServiceInterface::class)->add($parentMedia);

        $this->get(ProductVariantMediaServiceInterface::class)->assignFromParentToVariant($this->parentId, $this->variantId);

        $this->assertNotEquals(
            (string) $parentMedia->getId(),
            (string) $this->get(ProductMediaDaoInterface::class)->getAll($this->variantId)->first()->getId()
        );
    }

    private function createProductMedia(Id $productId, ProductMediaRole ...$roles): ProductMedia
    {
        if (empty($roles)) {
            $roles = [ProductMediaRole::from(ProductMediaRole::DETAIL)];
        }

        return new ProductMedia(
            Id::generate(),
            $productId,
            new Media(
                Id::generate(),
                new MediaPath(uniqid('img-', true) . '.jpg'),
                new MediaType('image/jpeg')
            ),
            new ProductMediaRoleSet(...$roles),
        );
    }
}
