<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Dao;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\ProductRating;
use OxidEsales\EshopCommunity\Internal\Review\ServiceFactory\ReviewServiceFactory;
use OxidEsales\TestingLibrary\UnitTestCase;

class ProductRatingDaoTest extends UnitTestCase
{
    public function testUpdateProductRating()
    {
        $this->createTestProduct();

        $productRating = new ProductRating();
        $productRating
            ->setProductId('testProduct')
            ->setRatingCount(66)
            ->setRatingAverage(3.7);

        $productRatingDao = $this->getProductRatingDao();
        $productRatingDao->update($productRating);

        $this->assertEquals(
            $productRating,
            $productRatingDao->getProductRatingById('testProduct')
        );
    }

    private function createTestProduct()
    {
        $product = oxNew(Article::class);
        $product->setId('testProduct');
        $product->save();
    }

    private function getProductRatingDao()
    {
        $reviewServiceFactory = new ReviewServiceFactory();

        return $reviewServiceFactory->getProductRatingDao();
    }
}
