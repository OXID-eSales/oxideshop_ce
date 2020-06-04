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
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ProductRatingService;
use OxidEsales\EshopCommunity\Tests\TestUtils\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\DatabaseTestingTrait;
use Webmozart\PathUtil\Path;

class ProductRatingBridgeTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'rating_bridge_fixtures.yaml')]);
    }

    public function testUpdateProductRating()
    {

        $productRatingBridge = $this->getProductRatingBridge();
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

    private function getProductRatingBridge()
    {
        return $this->get(ProductRatingBridgeInterface::class);
    }

    private function getProductRatingDao()
    {
        return $this->get(ProductRatingDaoInterface::class);
    }
}
