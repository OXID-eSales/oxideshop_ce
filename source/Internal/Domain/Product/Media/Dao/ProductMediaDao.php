<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

final class ProductMediaDao implements ProductMediaDaoInterface
{
    private readonly string $productMediaTableName;
    private readonly string $mediaReferenceTableName;

    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
        private readonly ShopAdapterInterface $shopAdapter
    ) {
        $this->productMediaTableName = 'oxarticle_media';
        $this->mediaReferenceTableName = 'oxmedia';
    }

    public function create(string $productId, Media $media, int $position, bool $active): ProductMedia
    {
        $id = $this->shopAdapter->generateUniqueId();

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert($this->productMediaTableName)
            ->values([
                'id' => ':id',
                'articleid' => ':productId',
                'mediaid' => ':mediaId',
                'position' => ':position',
                'active' => ':active'
            ])
            ->setParameters([
                'id' => $id,
                'productId' => $productId,
                'mediaId' => $media->getId(),
                'position' => $position,
                'active' => $active
            ]);

        $queryBuilder->execute();

        return new ProductMedia($id, $productId, $media, $position, $active);
    }

    public function update(string $id, int $position, bool $active): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->update($this->productMediaTableName)
            ->set('position', ':position')
            ->set('active', ':active')
            ->where('id = :id')
            ->setParameters([
                'position' => $position,
                'active' => $active,
                'id' => $id
            ]);

        $affectedRows = $queryBuilder->execute();

        if ($affectedRows === 0) {
            throw new EntryDoesNotExistDaoException(
                sprintf('Product media relation with ID "%s" not found for update.', $id)
            );
        }
    }

    public function delete(string $id): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete($this->productMediaTableName)
            ->where('id = :id')
            ->setParameter('id', $id);

        $affectedRows = $queryBuilder->execute();

        if ($affectedRows === 0) {
            throw new EntryDoesNotExistDaoException(
                sprintf('Product media relation with ID "%s" not found for deletion.', $id)
            );
        }
    }

    public function get(string $id): ProductMedia
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select(
                'pm.id as id',
                'pm.articleid as productId',
                'pm.position as position',
                'pm.active as active',
                'm.id as mediaId',
                'm.path as path',
                'm.type as type'
            )
            ->from($this->productMediaTableName, 'pm')
            ->join('pm', $this->mediaReferenceTableName, 'm', 'pm.mediaid = m.id')
            ->where('pm.id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->execute()->fetchAssociative();

        if ($result === false) {
            throw new EntryDoesNotExistDaoException(
                sprintf('Product media relation with ID "%s" not found.', $id)
            );
        }

        return $this->mapArrayToProductMedia($result);
    }

    public function getActiveProductMediaList(string $productId): ArrayCollection
    {
        return $this->getProductMediaList($productId, true);
    }

    public function getAllProductMediaList(string $productId): ArrayCollection
    {
        return $this->getProductMediaList($productId, false);
    }

    private function getProductMediaList(string $productId, bool $onlyActive): ArrayCollection
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select(
                'pm.id as id',
                'pm.articleid as productId',
                'pm.position as position',
                'pm.active as active',
                'm.id as mediaId',
                'm.path as path',
                'm.type as type'
            )
            ->from($this->productMediaTableName, 'pm')
            ->join('pm', $this->mediaReferenceTableName, 'm', 'pm.mediaid = m.id')
            ->where('pm.articleid = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('pm.position', 'ASC');

        if ($onlyActive) {
            $queryBuilder->andWhere('pm.active = 1');
        }

        $results = $queryBuilder->execute()->fetchAllAssociative();

        $collection = new ArrayCollection();
        foreach ($results as $row) {
            $collection->add($this->mapArrayToProductMedia($row));
        }
        return $collection;
    }

    private function mapArrayToProductMedia(array $data): ProductMedia
    {
        $media = new Media(
            id: $data['mediaId'],
            path: $data['path'],
            type: $data['type']
        );

        return new ProductMedia(
            id: $data['id'],
            productId: $data['productId'],
            media: $media,
            position: (int)$data['position'],
            active: (bool)$data['active']
        );
    }
}
