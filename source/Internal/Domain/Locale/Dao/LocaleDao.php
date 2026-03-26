<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataMapper\LocaleDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

use function sprintf;

readonly class LocaleDao implements LocaleDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private LocaleDataMapperInterface $dataMapper,
    ) {
    }

    public function getByCode(string $code): Locale
    {
        $result = $this->queryBuilderFactory
            ->create()
            ->select('code', 'name', 'fallback')
            ->from('oxlocales')
            ->where('code = :code')
            ->setParameter('code', $code)
            ->executeQuery()
            ->fetchAssociative();

        if (!$result) {
            throw new LocaleNotFoundException(sprintf('Locale with code "%s" does not exist.', $code));
        }

        return $this->dataMapper->fromData($result);
    }

    public function getAll(): array
    {
        return $this->mapRows(
            $this->queryBuilderFactory
                ->create()
                ->select('code', 'name', 'fallback')
                ->from('oxlocales')
                ->orderBy('code')
                ->executeQuery()
                ->fetchAllAssociative()
        );
    }

    public function getByShopId(int $shopId): array
    {
        return $this->mapRows(
            $this->queryBuilderFactory
                ->create()
                ->select('l.code', 'l.name', 'l.fallback')
                ->from('oxlocales', 'l')
                ->innerJoin('l', 'oxshop_locales', 's', 'l.code = s.code')
                ->where('s.shop_id = :shopId')
                ->setParameter('shopId', $shopId)
                ->orderBy('l.code')
                ->executeQuery()
                ->fetchAllAssociative()
        );
    }

    public function add(Locale $locale): void
    {
        $this->queryBuilderFactory
            ->create()
            ->insert('oxlocales')
            ->values([
                'code' => ':code',
                'name' => ':name',
                'fallback'   => ':fallback',
            ])
            ->setParameters($this->dataMapper->toData($locale))
            ->executeStatement();
    }

    public function update(Locale $locale): void
    {
        $this->queryBuilderFactory
            ->create()
            ->update('oxlocales')
            ->set('name', ':name')
            ->set('fallback', ':fallback')
            ->where('code = :code')
            ->setParameters($this->dataMapper->toData($locale))
            ->executeStatement();
    }

    public function delete(string $code): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete('oxlocales')
            ->where('code = :code')
            ->setParameter('code', $code)
            ->executeStatement();
    }

    public function addToShop(string $localeCode, int $shopId): void
    {
        $this->queryBuilderFactory
            ->create()
            ->insert('oxshop_locales')
            ->values([
                'shop_id'     => ':shopId',
                'code' => ':code',
            ])
            ->setParameters([
                'shopId'     => $shopId,
                'code' => $localeCode,
            ])
            ->executeStatement();
    }

    public function removeFromShop(string $localeCode, int $shopId): void
    {
        $this->queryBuilderFactory
            ->create()
            ->delete('oxshop_locales')
            ->where('code = :code')
            ->andWhere('shop_id = :shopId')
            ->setParameters([
                'code' => $localeCode,
                'shopId'     => $shopId,
            ])
            ->executeStatement();
    }

    /** @return Locale[] */
    private function mapRows(array $rows): array
    {
        return array_map(fn(array $row) => $this->dataMapper->fromData($row), $rows);
    }
}
