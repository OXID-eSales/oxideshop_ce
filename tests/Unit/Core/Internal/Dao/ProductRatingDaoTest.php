<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Internal\Dao;

use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Internal\Dao\ProductRatingDao;

class ProductRatingDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidProductIdsProvider
     * @expectedException OxidEsales\EshopCommunity\Internal\Exception\InvalidObjectIdDaoException
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
