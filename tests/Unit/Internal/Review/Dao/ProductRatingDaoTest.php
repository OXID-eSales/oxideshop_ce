<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Review\Dao;

use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ProductRatingDao;

class ProductRatingDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidProductIdsProvider
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdDaoException
     */
    public function testGetProductByIdWithInvalidId($invalidProductId)
    {
        $database = $this->getMockBuilder(DatabaseInterface::class)->getMock();
        $productRatingDao = new ProductRatingDao($database);

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
