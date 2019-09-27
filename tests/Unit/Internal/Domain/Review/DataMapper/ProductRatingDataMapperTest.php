<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\ProductRatingDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;

class ProductRatingDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function testMapping()
    {
        $mapper = new ProductRatingDataMapper();

        $mappedProductRating = $this->getMappedProductRating();
        $dataForMapping = $mapper->getData($mappedProductRating);

        $productRating = new ProductRating();
        $productRatingAfterMapping = $mapper->map($productRating, $dataForMapping);

        $this->assertEquals(
            $mappedProductRating,
            $productRatingAfterMapping
        );
    }

    public function testPrimaryKeyGetter()
    {
        $mapper = new ProductRatingDataMapper();
        $mappedProductRating = $this->getMappedProductRating();

        $expectedPrimaryKey = [
            'OXID' => 'testId',
        ];

        $this->assertEquals(
            $expectedPrimaryKey,
            $mapper->getPrimaryKey($mappedProductRating)
        );
    }

    private function getMappedProductRating()
    {
        $productRating = new ProductRating();
        $productRating
            ->setProductId('testId')
            ->setRatingCount(7)
            ->setRatingAverage(6.7);

        return $productRating;
    }
}
