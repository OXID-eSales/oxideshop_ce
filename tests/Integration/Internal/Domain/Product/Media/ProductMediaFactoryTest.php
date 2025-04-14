<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ProductMediaFactoryTest extends IntegrationTestCase
{
    public function testCreate(): void
    {
        $productId = Id::generate();
        $mediaPath = new MediaPath('aaa/bbb/ccc/test.jpg');
        $mediaType = new MediaType('image/jpeg');

        $productMedia = new ProductMedia(
            Id::generate(),
            $productId,
            new Media(
                Id::generate(),
                $mediaPath,
                $mediaType
            ),
            new ProductMediaRoleSet(
                ProductMediaRole::from(SystemProductMediaRole::Detail->value)
            ),
        );
        $this
            ->get(ProductMediaDaoInterface::class)
            ->add($productMedia);

        $productMedia = $this
            ->get(ProductMediaFactoryInterface::class)
            ->create(
                $productId,
                $mediaPath,
                $mediaType
            );

        $this->assertEquals(
            $productId,
            $productMedia->getProductId()
        );
        $this->assertEquals(
            $mediaPath,
            $productMedia
                ->getMedia()
                ->getMediaPath()
        );
        $this->assertEquals(
            $mediaType,
            $productMedia
                ->getMedia()
                ->getMediaType()
        );
    }
}
