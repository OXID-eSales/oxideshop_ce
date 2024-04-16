<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Dao;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\ProductRatingDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\InvalidObjectIdDaoException;

final class ProductRatingDaoTest extends TestCase
{
    #[DataProvider('invalidProductIdsProvider')]
    public function testGetProductByIdWithInvalidId(bool|int|string|null $invalidProductId): void
    {
        $this->expectException(InvalidObjectIdDaoException::class);
        $queryBuilderFactory = $this->getMockBuilder(QueryBuilderFactoryInterface::class)->getMock();
        $mapper = $this->getMockBuilder(ProductRatingDataMapperInterface::class)->getMock();

        $productRatingDao = new ProductRatingDao(
            $queryBuilderFactory,
            $mapper
        );

        $this->expectException(InvalidObjectIdDaoException::class);

        $productRatingDao->getProductRatingById($invalidProductId);
    }

    public static function invalidProductIdsProvider(): array
    {
        return [
            [null],
            [false],
            [true],
            [5],
            [''],
        ];
    }
}
