<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class ProductMediaDaoTest extends TestCase
{
    use DatabaseTrait;
    use ContainerTrait;

    private ProductMediaDaoInterface $productMediaDao;
    private MediaDaoInterface $mediaDao;
    private string $productId = 'prod_pmdt_01';
    private Media $testMedia;

    public function setUp(): void
    {
        parent::setUp();
        $connection = $this->get(ConnectionFactoryInterface::class)->create();
        $this->beginTransaction($connection);

        $this->productMediaDao = $this->get(ProductMediaDaoInterface::class);
        $this->mediaDao = $this->get(MediaDaoInterface::class);

        $this->createTestProduct($this->productId);
        $this->testMedia = $this->createTestMedia('media/pmdt_default.jpg', 'image/jpeg');
    }

    public function tearDown(): void
    {
        $connection = $this->get(ConnectionFactoryInterface::class)->create();
        $this->rollBackTransaction($connection);
        parent::tearDown();
    }

    private function createTestProduct(string $productId): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->insert('oxarticles')
            ->values(['OXID' => ':id', 'OXTITLE' => ':title', 'OXACTIVE' => ':active'])
            ->setParameters(['id' => $productId, 'title' => 'Test Product ' . $productId, 'active' => 1]);
        $queryBuilder->execute();
    }

    private function createTestMedia(string $path, string $type): Media
    {
        return $this->mediaDao->create(path: $path, type: $type);
    }

    public function testCreateAndGetProductMedia(): void
    {
        $position = 5;
        $active = false;

        $createdProductMedia = $this->productMediaDao->create(
            productId: $this->productId,
            media: $this->testMedia,
            position: $position,
            active: $active
        );

        $retrievedProductMedia = $this->productMediaDao->get($createdProductMedia->getId());

        $this->assertEquals($createdProductMedia, $retrievedProductMedia);
    }

    public function testGetProductMediaThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->productMediaDao->get('nonexistent_pm_id');
    }

    public function testUpdateProductMedia(): void
    {
        $initialPosition = 1;
        $initialActive = true;
        $createdProductMedia = $this->productMediaDao->create(
            productId: $this->productId,
            media: $this->testMedia,
            position: $initialPosition,
            active: $initialActive
        );
        $relationId = $createdProductMedia->getId();

        $newPosition = 10;
        $newActive = false;
        $expectedProductMediaAfterUpdate = new ProductMedia(
            id: $relationId,
            productId: $this->productId,
            media: $this->testMedia,
            position: $newPosition,
            active: $newActive
        );

        $this->productMediaDao->update(
            id: $relationId,
            position: $newPosition,
            active: $newActive
        );

        $updatedProductMedia = $this->productMediaDao->get($relationId);

        $this->assertEquals($expectedProductMediaAfterUpdate, $updatedProductMedia);
    }

    public function testUpdateProductMediaThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->productMediaDao->update(id: 'nonexistent_pm_id_update', position: 99, active: false);
    }

    public function testDeleteProductMedia(): void
    {
        $createdProductMedia = $this->productMediaDao->create(
            productId: $this->productId,
            media: $this->testMedia,
            position: 0,
            active: true
        );
        $relationId = $createdProductMedia->getId();

        $this->productMediaDao->delete($relationId);

        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->productMediaDao->get($relationId);
    }

    public function testDeleteProductMediaThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->productMediaDao->delete('nonexistent_pm_id_delete');
    }

    public function testGetAllProductMediaListReturnsCorrectlyOrderedAndCompleteList(): void
    {
        $media2 = $this->createTestMedia('media/pmdt_other.gif', 'image/gif');
        $pm1 = $this->productMediaDao->create(productId: $this->productId, media: $this->testMedia, position: 2, active: true);
        $pm2 = $this->productMediaDao->create(productId: $this->productId, media: $media2, position: 1, active: true);
        $pm3 = $this->productMediaDao->create(productId: $this->productId, media: $this->testMedia, position: 3, active: false);

        $list = $this->productMediaDao->getAllProductMediaList(productId: $this->productId);

        $this->assertEquals($pm2, $list->get(0));
        $this->assertEquals($pm1, $list->get(1));
        $this->assertEquals($pm3, $list->get(2));
        $this->assertNull($list->get(3));
    }

    public function testGetActiveProductMediaListReturnsCorrectlyOrderedAndFilteredList(): void
    {
        $media2 = $this->createTestMedia('media/pmdt_active.png', 'image/png');
        $pm1 = $this->productMediaDao->create(productId: $this->productId, media: $this->testMedia, position: 2, active: true);
        $pm2 = $this->productMediaDao->create(productId: $this->productId, media: $media2, position: 1, active: true);
        $this->productMediaDao->create(productId: $this->productId, media: $this->testMedia, position: 3, active: false);

        $list = $this->productMediaDao->getActiveProductMediaList(productId: $this->productId);

        $this->assertEquals($pm2, $list->get(0));
        $this->assertEquals($pm1, $list->get(1));
        $this->assertNull($list->get(2));
    }

    public function testGetProductMediaListsReturnEmptyCollectionForNonExistentProduct(): void
    {
        $nonExistentProductId = 'non_existent_product_id';

        $listAll = $this->productMediaDao->getAllProductMediaList($nonExistentProductId);
        $listActive = $this->productMediaDao->getActiveProductMediaList($nonExistentProductId);

        $this->assertTrue($listAll->isEmpty());
        $this->assertTrue($listActive->isEmpty());
    }
}
