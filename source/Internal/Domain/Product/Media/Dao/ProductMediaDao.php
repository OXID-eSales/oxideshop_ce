<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper\DataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaSorting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

use function sprintf;

readonly class ProductMediaDao implements ProductMediaDaoInterface
{
    private const MEDIA_TABLE = 'oxmedia';
    private const PRODUCT_MEDIA_TABLE = 'oxproduct_media';
    private const PRODUCT_MEDIA_ROLES_TABLE = 'oxproduct_media_roles';

    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private ConnectionFactoryInterface $connectionFactory,
        private DataMapperInterface $productMediaDataMapper
    ) {
    }

    public function get(Id $id): ProductMedia
    {
        $row = $this
            ->prepareSelectWithJoin()
            ->where('pm.id = :id')
            ->setParameter(
                'id',
                $id
            )
            ->executeQuery()
            ->fetchAssociative();
        if (!isset($row['id'])) {
            throw new EntryDoesNotExistDaoException(
                sprintf(
                    'Product media with ID %s was not found.',
                    $id
                )
            );
        }

        return $this->productMediaDataMapper->fromData($row);
    }

    public function getAllProductMedia(Id $productId): ArrayCollection
    {
        $collection = new ArrayCollection();
        $rows = $this
            ->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->setParameter(
                'productId',
                $productId
            )
            ->orderBy(
                'pm.position',
                'ASC'
            )
            ->executeQuery()
            ->fetchAllAssociative();
        foreach ($rows as $row) {
            $collection->add(
                $this->productMediaDataMapper-> fromData($row)
            );
        }

        return $collection;
    }

    public function add(ProductMedia $productMedia): void
    {
        if (!$productMedia->hasPosition()) {
            $productMedia->setPosition(
                $this->getNextPosition($productMedia->getProductId())
            );
        }
        $this->queryBuilderFactory
            ->create()
            ->insert(self::PRODUCT_MEDIA_TABLE)
            ->values([
                'id' => ':id',
                'product_id' => ':product_id',
                'media_id' => ':media_id',
                'position' => ':position',
                'active' => ':active'
            ])
            ->setParameters(
                $this->productMediaDataMapper->toData($productMedia)
            )
            ->executeStatement();

        $this->updateRoles($productMedia);
    }

    public function delete(Id $id): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete(self::PRODUCT_MEDIA_TABLE)
            ->where('id = :id')
            ->setParameter(
                'id',
                $id
            )
            ->executeStatement();
    }

    public function sort(ProductMediaSorting $sorting): void
    {
        $caseClauses = '';
        foreach ($sorting->getSorting() as $position => $id) {
            $caseClauses .= sprintf(
                " WHEN '%s' THEN %d ",
                $id,
                $position
            );
        }
        $this->connectionFactory
            ->create()
            ->prepare(
                sprintf(
                    'UPDATE `%s` SET `position` = CASE `id` %s END WHERE `id` in (%s)',
                    self::PRODUCT_MEDIA_TABLE,
                    $caseClauses,
                    $sorting
                )
            )
            ->executeQuery();
    }

    public function update(ProductMedia $productMedia): void
    {
        if (!$this->get($productMedia->getId())) {
            throw new EntryDoesNotExistDaoException();
        }
        $this->queryBuilderFactory
            ->create()
            ->update(self::PRODUCT_MEDIA_TABLE)
            ->set(
                'product_id',
                ':product_id'
            )
            ->set(
                'media_id',
                ':media_id'
            )
            ->set(
                'position',
                ':position'
            )
            ->set(
                'active',
                ':active'
            )
            ->where('id = :id')
            ->setParameters(
                $this->productMediaDataMapper->toData($productMedia)
            )
            ->executeStatement();

        $this->updateRoles($productMedia);
    }

    private function prepareSelectWithJoin(): QueryBuilder
    {
        return $this->queryBuilderFactory
            ->create()
            ->select(
                'pm.id as id',
                'pm.product_id as product_id',
                'pm.position as position',
                'pm.active as active',
                'm.id as media_id',
                'm.path as media_path',
                'm.type as media_mime_type',
                'GROUP_CONCAT(pmr.role) as roles',
            )
            ->from(
                self::PRODUCT_MEDIA_TABLE,
                'pm'
            )
            ->join(
                'pm',
                self::MEDIA_TABLE,
                'm',
                'pm.media_id = m.id'
            )
            ->leftJoin(
                'pm',
                self::PRODUCT_MEDIA_ROLES_TABLE,
                'pmr',
                'pm.id = pmr.product_media_id'
            )
            ->groupBy('pm.id');
    }

    private function getNextPosition(Id $productId): int
    {
        $maxPosition = $this->queryBuilderFactory
            ->create()
            ->select('MAX(pm.position) as maxPosition')
            ->from(
                self::PRODUCT_MEDIA_TABLE,
                'pm'
            )
            ->where('pm.product_id = :productId')
            ->setParameter(
                'productId',
                $productId
            )
            ->executeQuery()
            ->fetchOne();

        return $maxPosition === null ? 0 : ++$maxPosition;
    }

    private function updateRoles(ProductMedia $productMedia): void
    {
        $this->removePreviousRecords($productMedia);
        $this->removeRecordsIfUnique($productMedia);

        $insertQuery = $this->queryBuilderFactory
            ->create()
            ->insert(self::PRODUCT_MEDIA_ROLES_TABLE)
            ->values([
                'product_media_id' => ':product_media_id',
                'role' => ':role'
            ]);

        foreach ($productMedia->getRoleSet()->getRoleIterator() as $role) {
            $query = clone $insertQuery;
            $query
                ->setParameters([
                    'product_media_id' => $productMedia->getId(),
                    'role' => $role->value()
                ])
                ->executeStatement();
        }
    }

    private function removePreviousRecords(ProductMedia $productMedia): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete(self::PRODUCT_MEDIA_ROLES_TABLE)
            ->where('product_media_id = :product_media_id')
            ->setParameter(
                'product_media_id',
                $productMedia->getId()
            )
            ->executeStatement();
    }

    private function removeRecordsIfUnique(ProductMedia $productMedia): void
    {
        $relatedProductMediaIds = $this->queryBuilderFactory
            ->create()
            ->select('id')
            ->from(self::PRODUCT_MEDIA_TABLE)
            ->where('product_id = :product_id')
            ->setParameter('product_id', $productMedia->getProductId())
            ->executeQuery()
            ->fetchFirstColumn();

        if (empty($relatedProductMediaIds)) {
            return;
        }
        /** @var ProductMediaRole $role */
        foreach ($productMedia->getRoleSet()->getRoleIterator() as $role) {
            if ($role->isSingleAssignmentRole()) {
                $this->queryBuilderFactory
                    ->create()
                    ->delete(self::PRODUCT_MEDIA_ROLES_TABLE)
                    ->where('role = :role')
                    ->andWhere('product_media_id IN (:media_ids)')
                    ->setParameter('role', $role->value())
                    ->setParameter('media_ids', $relatedProductMediaIds, ArrayParameterType::STRING)
                    ->executeStatement();
            }
        }
    }

    public function getActiveByProductId(Id $productId): ArrayCollection
    {
        $collection = new ArrayCollection();
        $rows = $this->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->andWhere('pm.active = 1')
            ->setParameter('productId', $productId)
            ->orderBy('pm.position', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $collection->add(
                $this->productMediaDataMapper->fromData($row)
            );
        }

        return $collection;
    }

    public function getByRole(Id $productId, string $role): ?ProductMedia
    {
        $row = $this->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->andWhere('pm.active = 1')
            ->andWhere('pmr.role = :role')
            ->setParameter('productId', $productId)
            ->setParameter('role', $role)
            ->orderBy('pm.position', 'ASC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->productMediaDataMapper->fromData($row) : null;
    }

    public function getFirstActive(Id $productId): ?ProductMedia
    {
        $row = $this->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->andWhere('pm.active = 1')
            ->setParameter('productId', $productId)
            ->orderBy('pm.position', 'ASC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->productMediaDataMapper->fromData($row) : null;
    }

    public function getByPosition(Id $productId, int $position): ?ProductMedia
    {
        $row = $this->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->andWhere('pm.position = :position')
            ->andWhere('pm.active = 1')
            ->setParameter('productId', $productId)
            ->setParameter('position', $position)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->productMediaDataMapper->fromData($row) : null;
    }
}
