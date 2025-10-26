<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataMapper;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper\ViewDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

final class ViewDataMapperTest extends TestCase
{
    public function testToDataReturnsExpectedCollection(): void
    {
        $productMediaId1 = Id::generate();
        $productMediaId2 = Id::generate();
        $productId = Id::generate();
        $context = new ContextStub();
        $context->setShopBaseUrl('https://shop.local/media');

        $productMediaIcon = new ProductMedia(
            $productMediaId1,
            $productId,
            new Media(
                Id::generate(),
                new MediaPath('some/path/file.jpg'),
                new MediaType('image/jpeg'),
            ),
            new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::ICON)),
        );
        $productMediaIcon->setPosition(1);

        $productMediaDetail = new ProductMedia(
            $productMediaId2,
            $productId,
            new Media(
                Id::generate(),
                new MediaPath('some/path/file.png'),
                new MediaType('image/png'),
            ),
            new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMediaDetail->setPosition(2);
        $productMediaDetail->deactivate();

        $collection = new ArrayCollection([
            $productMediaIcon,
            $productMediaDetail,
        ]);

        $result = (new ViewDataMapper($context))->toData($collection);

        $this->assertEquals(
            [
                'id' => (string)$productMediaId1,
                'productId' => (string)$productId,
                'url' => 'https://shop.local/media/some/path/file.jpg',
                'position' => 1,
                'active' => true,
            ],
            $result['icon']
        );


        $this->assertEquals(
            [
                'id' => (string)$productMediaId2,
                'productId' => (string)$productId,
                'url' => 'https://shop.local/media/some/path/file.png',
                'position' => 2,
                'active' => false,
            ],
            $result['detailImages'][0]
        );
    }
}
