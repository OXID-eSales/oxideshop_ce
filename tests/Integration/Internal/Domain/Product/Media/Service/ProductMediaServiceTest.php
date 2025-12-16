<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Service;

use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class ProductMediaServiceTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    private ProductMedia $productMedia;
    private readonly ProductMediaServiceInterface $service;
    private readonly MediaDaoInterface $mediaDao;
    private readonly Id $productId;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransaction(
            $this
                ->get(ConnectionFactoryInterface::class)
                ->create()
        );
        $this->service = $this->get(ProductMediaServiceInterface::class);
        $this->mediaDao = $this->get(MediaDaoInterface::class);
        $this->createTestProductMedia();
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction(
            $this
                ->get(ConnectionFactoryInterface::class)
                ->create()
        );
        parent::tearDown();
    }

    public function testGet(): void
    {
        $fetched = $this->service->get($this->productMedia->getId());

        $this->assertEquals(
            $this->productMedia->getId(),
            $fetched->getId()
        );
        $this->assertEquals(
            $this->productMedia
                ->getMedia()
                ->getId(),
            $fetched
                ->getMedia()
                ->getId()
        );
    }

    public function testRemove(): void
    {
        $this->service->remove($this->productMedia->getId());

        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->service->get($this->productMedia->getId());
    }

    public function testRemoveWillDeleteMediaRecord(): void
    {
        $this->service->remove($this->productMedia->getId());

        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->mediaDao->get(
            $this->productMedia
                ->getMedia()
                ->getId()
        );
    }

    public function testActivate(): void
    {
        $this->service->deactivate($this->productMedia);

        $this->service->activate($this->productMedia);

        $fetched = $this->service->get($this->productMedia->getId());

        $this->assertTrue($fetched->isActive());
    }

    public function testDeactivate(): void
    {
        $this->service->deactivate($this->productMedia);

        $fetched = $this->service->get($this->productMedia->getId());

        $this->assertFalse($fetched->isActive());
    }

    public function testSorting(): void
    {
        $this->createTestProductMedia();
        $media1 = $this->productMedia;
        $this->createTestProductMedia();
        $media2 = $this->productMedia;
        $this->createTestProductMedia();
        $media3 = $this->productMedia;

        $this->service->sort([
            (string)$media2->getId(),
            (string)$media1->getId(),
            (string)$media3->getId(),
        ]);

        $this->assertEquals(
            1,
            $this->service
                ->get($media1->getId())
                ->getPosition()
        );
        $this->assertEquals(
            0,
            $this->service
                ->get($media2->getId())
                ->getPosition()
        );
        $this->assertEquals(
            2,
            $this->service
                ->get($media3->getId())
                ->getPosition()
        );
    }

    public function testSortWithMaliciousValues(): void
    {
        $this->createTestProductMedia();
        $media1 = $this->productMedia;
        $this->createTestProductMedia();
        $media2 = $this->productMedia;
        $this->createTestProductMedia();
        $media3 = $this->productMedia;

        $this->expectException(InvalidArgumentException::class);

        $this->service->sort([
            $media2->getId() . "' OR '1'='1",
            $media1->getId() . "') OR SLEEP(5) --",
            $media3->getId() . "'; DROP TABLE oxproduct_media; --",
            "' OR SLEEP(5) OR '",
            "' OR 1=1 --",
            "'; DELETE FROM oxproduct_media WHERE '1'='1",
        ]);
    }

    private function createTestProductMedia(): void
    {
        $media = new Media(
            Id::fromUid(
                md5(
                    uniqid(
                        'media_',
                        true
                    )
                )
            ),
            new MediaPath(
                uniqid(
                    'img-',
                    true
                ) . '.jpg'
            ),
            new MediaType('image/jpeg')
        );
        $this->productMedia = new ProductMedia(
            Id::fromUid(
                md5(
                    uniqid(
                        'product_media_',
                        true
                    )
                )
            ),
            $this->getSameProductIdForAllMedia(),
            $media,
            new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $this->service->add($this->productMedia);
    }

    private function getSameProductIdForAllMedia(): Id
    {
        if (!isset($this->productId)) {
            $this->productId = Id::fromUid(
                md5(
                    uniqid(
                        'product_',
                        true
                    )
                )
            );
        }

        return $this->productId;
    }
}
