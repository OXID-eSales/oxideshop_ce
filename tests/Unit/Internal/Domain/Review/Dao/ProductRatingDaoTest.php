<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\ProductRatingDataMapperInterface;
use \OxidEsales\EshopCommunity\Internal\Framework\Dao\InvalidObjectIdDaoException;

class ProductRatingDaoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider invalidProductIdsProvider
     */
    public function testGetProductByIdWithInvalidId($invalidProductId)
    {
        $this->expectException(InvalidObjectIdDaoException::class);
        $queryBuilderFactory = $this->getMockBuilder(QueryBuilderFactoryInterface::class)->getMock();
        $mapper = $this->getMockBuilder(ProductRatingDataMapperInterface::class)->getMock();

        $productRatingDao = new ProductRatingDao(
            $queryBuilderFactory,
            $mapper
        );

        $productRatingDao->getProductRatingById($invalidProductId);
    }

    public function invalidProductIdsProvider()
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
