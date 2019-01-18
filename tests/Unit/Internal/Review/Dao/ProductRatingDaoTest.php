<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ProductRatingDao;

class ProductRatingDaoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider invalidProductIdsProvider
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdDaoException
     */
    public function testGetProductByIdWithInvalidId($invalidProductId)
    {
        $queryBuilderFactory = $this->getMockBuilder(QueryBuilderFactoryInterface::class)->getMock();
        $mapper = $this->getMockBuilder(EntityMapperInterface::class)->getMock();

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
