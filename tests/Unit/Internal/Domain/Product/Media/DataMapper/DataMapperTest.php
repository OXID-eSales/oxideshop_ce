<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataMapper\DataMapper as MediaDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper\DataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class DataMapperTest extends TestCase
{
    public function testToData(): void
    {
        $productMedia = new ProductMedia(
            Id::fromString('pm789'),
            Id::fromString('product456'),
            new Media(
                Id::fromString('media123'),
                new MediaPath('path/to/file.jpg'),
                new MediaType('image/jpeg')
            ),
            new ProductMediaRoleSet(
                ProductMediaRole::from(ProductMediaRole::ICON),
                ProductMediaRole::from('custom')
            ),
        );
        $productMedia->setPosition(5);
        $data = (new DataMapper(new MediaDataMapper()))->toData($productMedia);

        $this->assertEquals(
            'pm789',
            $data['id']
        );
        $this->assertEquals(
            'product456',
            $data['product_id']
        );
        $this->assertEquals(
            'media123',
            $data['media_id']
        );
        $this->assertEquals(
            5,
            $data['position']
        );
        $this->assertEquals(
            [
                'icon',
                'custom'
            ],
            $data['roles']
        );
        $this->assertTrue($data['active']);
    }

    public function testFromData(): void
    {
        $result = (new DataMapper(new MediaDataMapper()))->fromData([
            'id' => 'pm789',
            'product_id' => 'product456',
            'media_id' => 'media123',
            'media_path' => 'path/to/file.jpg',
            'media_mime_type' => 'image/jpeg',
            'position' => 5,
            'roles' => 'icon,custom,thumbnail,detail',
            'active' => true,
        ]);

        $this->assertEquals(
            'pm789',
            (string)$result->getId()
        );
        $this->assertEquals(
            'product456',
            (string)$result->getProductId()
        );
        $this->assertEquals(
            5,
            $result->getPosition()
        );
        $this->assertTrue($result->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($result->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
        $this->assertTrue($result->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::DETAIL)));
        $this->assertTrue($result->isActive());
        $this->assertEquals(
            'media123',
            (string)$result
                ->getMedia()
                ->getId()
        );
        $this->assertEquals(
            'path/to/file.jpg',
            (string) $result
                ->getMedia()
                ->getMediaPath()
        );
        $this->assertEquals(
            'image/jpeg',
            (string) $result
                ->getMedia()
                ->getMediaType()
        );
    }
}
