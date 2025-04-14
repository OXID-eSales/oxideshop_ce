<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataMapper\DataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

use function sprintf;

readonly class MediaDao implements MediaDaoInterface
{
    private const MEDIA_TABLE = 'oxmedia';

    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private DataMapperInterface $dataMapper,
    ) {
    }

    public function add(Media $media): void
    {
        $this->queryBuilderFactory
            ->create()
            ->insert(self::MEDIA_TABLE)
            ->values([
                'id' => ':id',
                'path' => ':path',
                'type' => ':type'
            ])
            ->setParameters(
                $this->dataMapper->toData($media)
            )
            ->executeStatement();
    }

    public function get(Id $id): Media
    {
        $result = $this->queryBuilderFactory
            ->create()
            ->select('id', 'path', 'type')
            ->from(self::MEDIA_TABLE)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
        if (!$result) {
            throw new EntryDoesNotExistDaoException(
                sprintf('Media entry with ID "%s" does not exist.', $id)
            );
        }

        return $this->dataMapper->fromData($result);
    }

    public function delete(Id $id): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete(self::MEDIA_TABLE)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeStatement();
    }
}
