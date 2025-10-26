<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaSorting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class ProductMediaDaoTest extends TestCase
{
    use DatabaseTrait;
    use ContainerTrait;

    private ProductMediaDaoInterface $productMediaDao;
    private readonly ID $productId;
    private readonly Media $media1;
    private readonly Media $media2;

    public function setUp(): void
    {
        parent::setUp();
        $connection = $this
            ->get(ConnectionFactoryInterface::class)
            ->create();
        $this->beginTransaction($connection);

        $this->productMediaDao = $this->get(ProductMediaDaoInterface::class);
        $this->createTestProduct();
        $this->media1 = new Media(
            Id::generate(),
            new MediaPath('media/pmdt_default.jpg'),
            new MediaType('image/jpeg')
        );
        $this->media2 = new Media(
            Id::generate(),
            new MediaPath('media/pmdt_default2.jpg'),
            new MediaType('image/png')
        );
        $this
            ->get(MediaDaoInterface::class)
            ->add($this->media1);
        $this
            ->get(MediaDaoInterface::class)
            ->add($this->media2);
    }

    public function tearDown(): void
    {
        $connection = $this
            ->get(ConnectionFactoryInterface::class)
            ->create();
        $this->rollBackTransaction($connection);
        parent::tearDown();
    }

    public function testAddAndGet(): void
    {
        $productMedia = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(
                ProductMediaRole::from(ProductMediaRole::THUMBNAIL),
                ProductMediaRole::from(ProductMediaRole::ICON),
            ),
        );
        $productMedia->setPosition(123);

        $this->productMediaDao->add($productMedia);

        $fetched = $this->productMediaDao->get($productMedia->getId());
        $this->assertEquals(
            $productMedia->getId(),
            $fetched->getId()
        );
        $this->assertEquals(
            $productMedia->getProductId(),
            $fetched->getProductId()
        );
        $this->assertEquals(
            $productMedia->getMedia(),
            $fetched->getMedia()
        );
        $this->assertEquals(
            $productMedia->getPosition(),
            $fetched->getPosition()
        );
        $this->assertEquals(
            $productMedia->isActive(),
            $fetched->isActive()
        );
        $this->assertTrue($fetched->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
        $this->assertTrue($fetched->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
    }

    public function testGetWithNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->productMediaDao->get(Id::generate());
    }

    public function testUpdate(): void
    {
        $productMedia = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia->setPosition(123);
        $this->productMediaDao->add($productMedia);

        $productMedia->getRoleSet()->addRole(ProductMediaRole::from(ProductMediaRole::ICON));
        $productMedia->getRoleSet()->addRole(ProductMediaRole::from(ProductMediaRole::THUMBNAIL));
        $productMedia->getRoleSet()->removeRole(ProductMediaRole::from(ProductMediaRole::DETAIL));
        $productMedia->deactivate();
        $this->productMediaDao->update($productMedia);

        $fetched = $this->productMediaDao->get($productMedia->getId());
        $this->assertTrue($fetched->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($fetched->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
        $this->assertFalse($fetched->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::DETAIL)));
        $this->assertFalse($fetched->isActive());
    }

    public function testUpdateWithNonExistent(): void
    {
        $productMedia = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia->setPosition(123);

        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->productMediaDao->update($productMedia);
    }

    public function testDelete(): void
    {
        $productMedia = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia->setPosition(123);
        $productMedia->deactivate();
        $this->productMediaDao->add($productMedia);

        $this->productMediaDao->delete($productMedia->getId());

        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->productMediaDao->get($productMedia->getId());
    }

    public function testGetAllProductMediaWillReturnMultipleRecords(): void
    {
        $productMedia1 = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(
                ProductMediaRole::from(ProductMediaRole::ICON),
                ProductMediaRole::from(ProductMediaRole::THUMBNAIL),
            ),
        );
        $productMedia2 = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media2,
            roleSet: new ProductMediaRoleSet(
                ProductMediaRole::from(ProductMediaRole::DETAIL),
                ProductMediaRole::from('whatever')
            ),
        );
        $productMedia2->deactivate();
        $this->productMediaDao->add($productMedia1);
        $this->productMediaDao->add($productMedia2);

        $fetchedList = $this->productMediaDao->getAllProductMedia(productId: $this->productId);

        $this->assertCount(
            2,
            $fetchedList
        );
        $this->assertEquals(
            $productMedia1->getId(),
            $fetchedList
                ->get(0)
                ->getId()
        );
        $this->assertEquals(
            $this->media1->getId(),
            $fetchedList
                ->get(0)
                ->getMedia()
                ->getId()
        );
        $this->assertTrue(
            $fetchedList
                ->get(0)
                ->isActive()
        );
        $this->assertTrue(
            $fetchedList
                ->get(0)
                ->getRoleSet()
                ->has(ProductMediaRole::from(ProductMediaRole::ICON))
        );
        $this->assertTrue(
            $fetchedList
                ->get(0)
                ->getRoleSet()
                ->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL))
        );
        $this->assertEquals(
            $productMedia2->getProductId(),
            $fetchedList
                ->get(0)
                ->getProductId()
        );
        $this->assertEquals(
            $productMedia2->getId(),
            $fetchedList
                ->get(1)
                ->getId()
        );
        $this->assertFalse(
            $fetchedList
                ->get(1)
                ->isActive()
        );
        $this->assertTrue($fetchedList->get(1)->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::DETAIL)));
        $this->assertTrue(
            $fetchedList
                ->get(1)
                ->getRoleSet()
                ->has(ProductMediaRole::from('whatever'))
        );
    }

    public function testAddWillSetNextPositionsAutomatically(): void
    {
        $productMedia1 = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia1->setPosition(123);
        $productMedia2 = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia3 = new ProductMedia(
            id: Id::generate(),
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $this->productMediaDao->add($productMedia1);
        $this->productMediaDao->add($productMedia2);
        $this->productMediaDao->add($productMedia3);

        $list = $this->productMediaDao->getAllProductMedia(productId: $this->productId);

        $this->assertEquals(
            123,
            $list
                ->get(0)
                ->getPosition()
        );
        $this->assertEquals(
            124,
            $list
                ->get(1)
                ->getPosition()
        );
        $this->assertEquals(
            125,
            $list
                ->get(2)
                ->getPosition()
        );
    }

    public function testSortWillResetAndUpdatePositions(): void
    {
        $id1 = Id::generate();
        $id2 = Id::generate();
        $id3 = Id::generate();

        $productMedia1 = new ProductMedia(
            id: $id1,
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia1->setPosition(123);
        $productMedia2 = new ProductMedia(
            id: $id2,
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia2->setPosition(456);
        $productMedia3 = new ProductMedia(
            id: $id3,
            productId: $this->productId,
            media: $this->media1,
            roleSet: new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL)),
        );
        $productMedia3->setPosition(789);
        $this->productMediaDao->add($productMedia1);
        $this->productMediaDao->add($productMedia2);
        $this->productMediaDao->add($productMedia3);

        $this->productMediaDao->sort(
            new ProductMediaSorting(
                array_map(
                    'strval',
                    [
                        $id2,
                        $id1,
                        $id3
                    ]
                )
            )
        );

        $list = $this->productMediaDao->getAllProductMedia(productId: $this->productId);

        $this->assertEquals(
            $id2,
            $list
                ->get(0)
                ->getId()
        );
        $this->assertEquals(
            $id1,
            $list
                ->get(1)
                ->getId()
        );
        $this->assertEquals(
            $id3,
            $list
                ->get(2)
                ->getId()
        );
        $this->assertEquals(
            0,
            $list
                ->get(0)
                ->getPosition()
        );
        $this->assertEquals(
            1,
            $list
                ->get(1)
                ->getPosition()
        );
        $this->assertEquals(
            2,
            $list
                ->get(2)
                ->getPosition()
        );
    }

    public function testGetByProductIdWithEmptyCollection(): void
    {
        $listAll = $this->productMediaDao->getAllProductMedia(Id::generate());

        $this->assertTrue($listAll->isEmpty());
    }

    private function createTestProduct(): void
    {
        $this->productId = Id::generate();
        $this
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->insert('oxarticles')
            ->values([
                'OXID' => ':id',
                'OXTITLE' => ':title',
                'OXACTIVE' => ':active',
            ])
            ->setParameters([
                'id' => (string)$this->productId,
                'title' => 'Test Product ',
                'active' => 1,
            ])
            ->executeQuery();
    }

    public function testGetActiveByProductId(): void
    {
        $productMedia1 = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia1->setPosition(1);
        $this->productMediaDao->add($productMedia1);

        $productMedia2 = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia2->setPosition(2);
        $productMedia2->deactivate();
        $this->productMediaDao->add($productMedia2);

        $activeList = $this->productMediaDao->getActiveByProductId($this->productId);

        $this->assertCount(1, $activeList);
        $this->assertTrue($activeList->get(0)->isActive());
    }

    public function testGetActiveByProductIdWithEmptyResult(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(1);
        $productMedia->deactivate();
        $this->productMediaDao->add($productMedia);

        $activeList = $this->productMediaDao->getActiveByProductId($this->productId);

        $this->assertTrue($activeList->isEmpty());
    }

    public function testGetByRole(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)
        );
        $productMedia->setPosition(1);
        $this->productMediaDao->add($productMedia);

        $fetched = $this->productMediaDao->getByRole($this->productId, 'thumbnail');

        $this->assertEquals($productMedia->getId(), $fetched->getId());
    }

    public function testGetByRoleReturnsNullWhenNotFound(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(1);
        $this->productMediaDao->add($productMedia);

        $result = $this->productMediaDao->getByRole($this->productId, 'thumbnail');

        $this->assertNull($result);
    }

    public function testGetByRoleIgnoresInactive(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)
        );
        $productMedia->setPosition(1);
        $productMedia->deactivate();
        $this->productMediaDao->add($productMedia);

        $result = $this->productMediaDao->getByRole($this->productId, 'thumbnail');

        $this->assertNull($result);
    }

    public function testGetByRoleReturnsFirstByPosition(): void
    {
        $productMedia1 = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)
        );
        $productMedia1->setPosition(2);
        $this->productMediaDao->add($productMedia1);

        $productMediaId2 = Id::generate();
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::THUMBNAIL));
        $productMedia2 = new ProductMedia(
            id: $productMediaId2,
            productId: $this->productId,
            media: $this->media1,
            roleSet: $roleSet
        );
        $productMedia2->setPosition(1);
        $this->productMediaDao->add($productMedia2);

        $result = $this->productMediaDao->getByRole($this->productId, 'thumbnail');

        $this->assertEquals($productMediaId2, $result->getId());
    }

    public function testGetFirstActive(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(1);
        $this->productMediaDao->add($productMedia);

        $fetched = $this->productMediaDao->getFirstActive($this->productId);

        $this->assertEquals($productMedia->getId(), $fetched->getId());
    }

    public function testGetFirstActiveReturnsNullWhenNoActiveMedia(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(1);
        $productMedia->deactivate();
        $this->productMediaDao->add($productMedia);

        $result = $this->productMediaDao->getFirstActive($this->productId);

        $this->assertNull($result);
    }

    public function testGetFirstActiveReturnsNullForNonExistentProduct(): void
    {
        $result = $this->productMediaDao->getFirstActive(Id::generate());

        $this->assertNull($result);
    }

    public function testGetByPosition(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(5);
        $this->productMediaDao->add($productMedia);

        $fetched = $this->productMediaDao->getByPosition($this->productId, 5);

        $this->assertEquals($productMedia->getId(), $fetched->getId());
    }

    public function testGetByPositionReturnsNullWhenNotFound(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(1);
        $this->productMediaDao->add($productMedia);

        $result = $this->productMediaDao->getByPosition($this->productId, 99);

        $this->assertNull($result);
    }

    public function testGetByPositionReturnsCorrectMediaForMultipleProducts(): void
    {
        // Create another product
        $anotherProductId = Id::generate();
        $this->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->insert('oxarticles')
            ->values([
                'OXID' => ':id',
                'OXTITLE' => ':title',
                'OXACTIVE' => ':active',
            ])
            ->setParameters([
                'id' => (string)$anotherProductId,
                'title' => 'Another Product',
                'active' => 1,
            ])
            ->executeQuery();

        $media1 = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $media1->setPosition(1);
        $this->productMediaDao->add($media1);

        $media2 = $this->createProductMedia(
            $anotherProductId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $media2->setPosition(1);
        $this->productMediaDao->add($media2);

        $result1 = $this->productMediaDao->getByPosition($this->productId, 1);
        $result2 = $this->productMediaDao->getByPosition($anotherProductId, 1);

        $this->assertEquals($media1->getId(), $result1->getId());
        $this->assertEquals($media2->getId(), $result2->getId());
    }

    public function testGetByPositionReturnsNullForNonExistentProduct(): void
    {
        $result = $this->productMediaDao->getByPosition(Id::generate(), 1);

        $this->assertNull($result);
    }

    public function testGetByPositionSkipsInactive(): void
    {
        $productMedia = $this->createProductMedia(
            $this->productId,
            $this->media1,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $productMedia->setPosition(3);
        $productMedia->deactivate();
        $this->productMediaDao->add($productMedia);

        $result = $this->productMediaDao->getByPosition($this->productId, 3);
        $this->assertNull($result);
    }

    private function createProductMedia(Id $productId, Media $media, ProductMediaRole $role): ProductMedia
    {
        $roleSet = new ProductMediaRoleSet($role);
        return new ProductMedia(
            id: Id::generate(),
            productId: $productId,
            media: $media,
            roleSet: $roleSet
        );
    }
}
