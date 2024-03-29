<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Bridge;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ProductRatingService;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class ProductRatingBridgeTest extends IntegrationTestCase
{
    public function testUpdateProductRating()
    {
        $this->createTestProduct();
        $this->createTestRatings();

        $productRatingBridge = $this->get(ProductRatingBridgeInterface::class);
        $productRatingBridge->updateProductRating('testProduct');

        $productRatingDao = $this->getProductRatingDao();
        $productRating = $productRatingDao->getProductRatingById('testProduct');

        $this->assertEquals(
            4,
            $productRating->getRatingAverage()
        );

        $this->assertEquals(
            3,
            $productRating->getRatingCount()
        );
    }

    private function createTestProduct()
    {
        $product = oxNew(Article::class);
        $product->setId('testProduct');
        $product->save();
    }

    private function createTestRatings()
    {
        $rating = oxNew(Rating::class);
        $rating->oxratings__oxobjectid = new Field('testProduct');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->oxratings__oxrating = new Field(3);
        $rating->save();

        $rating = oxNew(Rating::class);
        $rating->oxratings__oxobjectid = new Field('testProduct');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->oxratings__oxrating = new Field(4);
        $rating->save();

        $rating = oxNew(Rating::class);
        $rating->oxratings__oxobjectid = new Field('testProduct');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->oxratings__oxrating = new Field(5);
        $rating->save();
    }

    /**
     * Accessing the dao is difficult, because it is a private service.
     * In newer versions of the Symfony Container (since 4.1) this may be
     * done more elegant.
     *
     * @return ProductRatingDao
     */
    private function getProductRatingDao()
    {
        $bridge = $this->get(ProductRatingBridgeInterface::class);
        $serviceProperty = new \ReflectionProperty(ProductRatingBridge::class, 'productRatingService');
        $serviceProperty->setAccessible(true);
        $service = $serviceProperty->getValue($bridge);
        $daoProperty = new \ReflectionProperty(ProductRatingService::class, 'productRatingDao');
        $daoProperty->setAccessible(true);

        return $daoProperty->getValue($service);
    }
}
