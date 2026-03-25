<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

readonly class MediaAttributeDao implements MediaAttributeDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private string $table = 'oxmedia_attributes',
    ) {
    }

    public function getAttributes(Id $mediaId, string $localeCode, int $shopId): MediaAttributes
    {
        $rows = $this->queryBuilderFactory
            ->create()
            ->select('name', 'value')
            ->from($this->table)
            ->where('media_id = :media_id')
            ->andWhere('locale_code = :locale_code')
            ->andWhere('shop_id = :shop_id')
            ->setParameter('media_id', $mediaId)
            ->setParameter('locale_code', $localeCode)
            ->setParameter('shop_id', $shopId)
            ->executeQuery()
            ->fetchAllAssociative();

        $mapped = [];
        foreach ($rows as $row) {
            $mapped[$row['name']] = $row['value'];
        }
        return new MediaAttributes($mapped);
    }

    public function save(MediaAttribute $attribute): void
    {
        $this->delete(
            $attribute->getName(),
            $attribute->getMediaId(),
            $attribute->getLocaleCode(),
            $attribute->getShopId()
        );
        $this->queryBuilderFactory
            ->create()
            ->insert($this->table)
            ->values([
                'id'          => ':id',
                'media_id'    => ':media_id',
                'locale_code' => ':locale_code',
                'shop_id'     => ':shop_id',
                'name'        => ':name',
                'value'       => ':value',
            ])
            ->setParameters([
                'id'          => (string) $attribute->getId(),
                'media_id'    => (string) $attribute->getMediaId(),
                'locale_code' => $attribute->getLocaleCode(),
                'shop_id'     => $attribute->getShopId(),
                'name'        => $attribute->getName(),
                'value'       => $attribute->getValue(),
            ])
            ->executeStatement();
    }

    public function delete(string $name, Id $mediaId, string $localeCode, int $shopId): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete($this->table)
            ->where('media_id = :media_id')
            ->andWhere('locale_code = :locale_code')
            ->andWhere('shop_id = :shop_id')
            ->andWhere('name = :name')
            ->setParameter('media_id', $mediaId)
            ->setParameter('locale_code', $localeCode)
            ->setParameter('shop_id', $shopId)
            ->setParameter('name', $name)
            ->executeStatement();
    }
}
