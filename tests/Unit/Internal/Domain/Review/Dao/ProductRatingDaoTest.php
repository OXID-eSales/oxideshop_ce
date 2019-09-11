<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\ProductRatingDataMapperInterface;

class ProductRatingDaoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider invalidProductIdsProvider
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Dao\InvalidObjectIdDaoException
     */
    public function testGetProductByIdWithInvalidId($invalidProductId)
    {
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
