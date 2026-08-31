<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper\DataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaSorting;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
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

    public function add(ProductMedia $productMedia): void
    {
        if (!$productMedia->hasPosition()) {
            $productMedia->setPosition(
                $this->getNextPosition($productMedia->getProductId())
            );
        }
        $data = $this->productMediaDataMapper->toData($productMedia);

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
                $data
            )
            ->executeStatement();

        $this->addRoles($productMedia->getId(), $data['roles']);
    }

    public function update(ProductMedia $productMedia): void
    {
        $this->get($productMedia->getId());
        $data = $this->productMediaDataMapper->toData($productMedia);

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
                $data
            )
            ->executeStatement();

        $this->replaceRoles($productMedia->getId(), $data['roles']);
    }

    public function delete(Id $id): void
    {
        $this->removeRoles($id);

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
        $parameters = [];
        $inClausePlaceholders = [];

        foreach ($sorting->getSorting() as $position => $id) {
            $idParamName = 'id_' . $position;
            $positionParamName = 'position_' . $position;

            $caseClauses .= sprintf(
                " WHEN :%s THEN :%s ",
                $idParamName,
                $positionParamName
            );

            $parameters[$idParamName] = (string) $id;
            $parameters[$positionParamName] = $position;
            $inClausePlaceholders[] = ':' . $idParamName;
        }

        $parameters['productId'] = (string) $sorting->getProductId();

        $query = sprintf(
            'UPDATE `%s` SET `position` = CASE `id` %s END WHERE `product_id` = :productId AND `id` IN (%s)',
            self::PRODUCT_MEDIA_TABLE,
            $caseClauses,
            implode(', ', $inClausePlaceholders)
        );

        $this->connectionFactory
            ->create()
            ->executeStatement($query, $parameters);
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

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAll(Id $productId): ArrayCollection
    {
        return $this->getAllByActive($productId, false);
    }

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAllActive(Id $productId): ArrayCollection
    {
        return $this->getAllByActive($productId, true);
    }

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): ArrayCollection
    {
        return $this->getAllByRoleAndActive($productId, $role, false);
    }

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAllActiveByRole(Id $productId, ProductMediaRole $role): ArrayCollection
    {
        return $this->getAllByRoleAndActive($productId, $role, true);
    }

    public function getByRole(Id $productId, ProductMediaRole $role): ?ProductMedia
    {
        return $this->getByRoleAndActive($productId, $role, false);
    }

    public function getActiveByRole(Id $productId, ProductMediaRole $role): ?ProductMedia
    {
        return $this->getByRoleAndActive($productId, $role, true);
    }

    public function getActiveByPosition(Id $productId, int $position): ?ProductMedia
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

    /** @return Id[] */
    public function getProductIdsByMedia(Id $mediaId): array
    {
        $productIds = $this->queryBuilderFactory
            ->create()
            ->select('DISTINCT pm.product_id as product_id')
            ->from(self::PRODUCT_MEDIA_TABLE, 'pm')
            ->where('pm.media_id = :mediaId')
            ->setParameter('mediaId', $mediaId)
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map(
            static fn(string $productId): Id => Id::fromString($productId),
            $productIds
        );
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

    /** @return ArrayCollection<int, ProductMedia> */
    private function getAllByActive(Id $productId, bool $filterActive): ArrayCollection
    {
        $collection = new ArrayCollection();

        $queryBuilder = $this
            ->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('pm.position', 'ASC');

        if ($filterActive) {
            $queryBuilder
                ->andWhere('pm.active = :active')
                ->setParameter('active', 1);
        }

        $rows = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $collection->add(
                $this->productMediaDataMapper->fromData($row)
            );
        }

        return $collection;
    }

    /** @return ArrayCollection<int, ProductMedia> */
    private function getAllByRoleAndActive(Id $productId, ProductMediaRole $role, bool $onlyActive): ArrayCollection
    {
        $collection = new ArrayCollection();

        $queryBuilder = $this
            ->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->andWhere('pmr.role = :role')
            ->setParameter('productId', $productId)
            ->setParameter('role', $role->value())
            ->orderBy('pm.position', 'ASC');

        if ($onlyActive) {
            $queryBuilder
                ->andWhere('pm.active = :active')
                ->setParameter('active', 1);
        }

        $rows = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $collection->add(
                $this->productMediaDataMapper->fromData($row)
            );
        }

        return $collection;
    }

    private function getByRoleAndActive(Id $productId, ProductMediaRole $role, bool $onlyActive): ?ProductMedia
    {
        $queryBuilder = $this
            ->prepareSelectWithJoin()
            ->where('pm.product_id = :productId')
            ->andWhere('pmr.role = :role')
            ->setParameter('productId', $productId)
            ->setParameter('role', $role->value())
            ->orderBy('pm.position', 'ASC')
            ->setMaxResults(1);

        if ($onlyActive) {
            $queryBuilder
                ->andWhere('pm.active = :active')
                ->setParameter('active', 1);
        }

        $row = $queryBuilder
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->productMediaDataMapper->fromData($row) : null;
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

    private function removeRoles(Id $productMediaId): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete(self::PRODUCT_MEDIA_ROLES_TABLE)
            ->where('product_media_id = :id')
            ->setParameter('id', $productMediaId)
            ->executeStatement();
    }

    private function addRoles(Id $productMediaId, array $roles): void
    {
        if (empty($roles)) {
            return;
        }

        $insertQuery = $this->queryBuilderFactory
            ->create()
            ->insert(self::PRODUCT_MEDIA_ROLES_TABLE)
            ->values([
                'product_media_id' => ':product_media_id',
                'role' => ':role'
            ]);

        foreach ($roles as $role) {
            $insertQuery
                ->setParameters([
                    'product_media_id' => $productMediaId,
                    'role' => $role
                ])
                ->executeStatement();
        }
    }

    private function replaceRoles(Id $productMediaId, array $roles): void
    {
        $this->removeRoles($productMediaId);
        $this->addRoles($productMediaId, $roles);
    }
}
