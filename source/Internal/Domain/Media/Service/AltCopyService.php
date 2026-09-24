<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use Doctrine\DBAL\ArrayParameterType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

readonly class AltCopyService implements AltCopyServiceInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private string $attributesTable = 'oxmedia_attributes',
        private string $productMediaTable = 'oxproduct_media',
    ) {
    }

    public function copyForProduct(Id $productId, int $ownerShopId, int $targetShopId): void
    {
        if ($ownerShopId === $targetShopId) {
            return;
        }

        $mediaIds = $this->mediaIdsForProduct($productId);
        if ($mediaIds === []) {
            return;
        }

        $ownerRows = $this->queryBuilderFactory
            ->create()
            ->select('media_id', 'locale_code', 'name', 'value')
            ->from($this->attributesTable)
            ->where('media_id IN (:media_ids)')
            ->andWhere('shop_id = :shop_id')
            ->setParameter('media_ids', $mediaIds, ArrayParameterType::STRING)
            ->setParameter('shop_id', $ownerShopId)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($ownerRows as $row) {
            if ($this->exists($row['media_id'], $row['locale_code'], $row['name'], $targetShopId)) {
                continue;
            }

            $this->queryBuilderFactory
                ->create()
                ->insert($this->attributesTable)
                ->values([
                    'id'          => ':id',
                    'media_id'    => ':media_id',
                    'locale_code' => ':locale_code',
                    'shop_id'     => ':shop_id',
                    'name'        => ':name',
                    'value'       => ':value',
                ])
                ->setParameters([
                    'id'          => (string) Id::generate(),
                    'media_id'    => $row['media_id'],
                    'locale_code' => $row['locale_code'],
                    'shop_id'     => $targetShopId,
                    'name'        => $row['name'],
                    'value'       => $row['value'],
                ])
                ->executeStatement();
        }
    }

    public function removeForProduct(Id $productId, int $targetShopId): void
    {
        $mediaIds = $this->mediaIdsForProduct($productId);
        if ($mediaIds === []) {
            return;
        }

        $this->queryBuilderFactory
            ->create()
            ->delete($this->attributesTable)
            ->where('media_id IN (:media_ids)')
            ->andWhere('shop_id = :shop_id')
            ->setParameter('media_ids', $mediaIds, ArrayParameterType::STRING)
            ->setParameter('shop_id', $targetShopId)
            ->executeStatement();
    }

    private function mediaIdsForProduct(Id $productId): array
    {
        return $this->queryBuilderFactory
            ->create()
            ->select('media_id')
            ->from($this->productMediaTable)
            ->where('product_id = :product_id')
            ->setParameter('product_id', (string) $productId)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    private function exists(string $mediaId, string $localeCode, string $name, int $shopId): bool
    {
        return (bool) $this->queryBuilderFactory
            ->create()
            ->select('1')
            ->from($this->attributesTable)
            ->where('media_id = :media_id')
            ->andWhere('locale_code = :locale_code')
            ->andWhere('name = :name')
            ->andWhere('shop_id = :shop_id')
            ->setMaxResults(1)
            ->setParameter('media_id', $mediaId)
            ->setParameter('locale_code', $localeCode)
            ->setParameter('name', $name)
            ->setParameter('shop_id', $shopId)
            ->executeQuery()
            ->fetchOne();
    }
}
