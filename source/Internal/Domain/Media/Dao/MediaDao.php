<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

final class MediaDao implements MediaDaoInterface
{
    private readonly string $mediaTableName;

    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
        private readonly ShopAdapterInterface $shopAdapter
    ) {
        $this->mediaTableName = 'oxmedia';
    }

    public function create(string $path, string $type): Media
    {
        $id = $this->shopAdapter->generateUniqueId();

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert($this->mediaTableName)
            ->values([
                'id' => ':id',
                'path' => ':path',
                'type' => ':type'
            ])
            ->setParameters([
                'id' => $id,
                'path' => $path,
                'type' => $type
            ]);

        $queryBuilder->execute();

        return new Media($id, $path, $type);
    }

    public function get(string $id): Media
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('id', 'path', 'type')
            ->from($this->mediaTableName)
            ->where('id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->execute()->fetchAssociative();

        if ($result === false) {
            throw new EntryDoesNotExistDaoException(
                sprintf('Media entry with ID "%s" does not exist.', $id)
            );
        }

        return $this->mapArrayToMedia($result);
    }

    public function delete(string $id): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete($this->mediaTableName)
            ->where('id = :id')
            ->setParameter('id', $id);

        $affectedRows = $queryBuilder->execute();

        if ($affectedRows === 0) {
            throw new EntryDoesNotExistDaoException(
                sprintf('Media entry with ID "%s" not found for deletion.', $id)
            );
        }
    }

    private function mapArrayToMedia(array $data): Media
    {
        return new Media(
            id: $data['id'],
            path: $data['path'],
            type: $data['type']
        );
    }
}
