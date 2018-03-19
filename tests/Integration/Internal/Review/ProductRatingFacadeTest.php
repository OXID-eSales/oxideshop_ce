<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Dao\RatingDao;
use OxidEsales\EshopCommunity\Internal\DataObject\Rating;
use OxidEsales\EshopCommunity\Internal\Facade\ProductRatingFacade;
use OxidEsales\EshopCommunity\Internal\Service\ProductRatingService;
use OxidEsales\EshopCommunity\Internal\Service\RatingCalculatorService;

class ProductRatingFacadeTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testUpdateProductRating()
    {
        $productId = '09602cddb5af0aba745293d08ae6bcf6';
        $productRatingDao = $this->getProductRatingDao();
        $productRatingFacade = $this->getProductRatingFacade();

        $productRatingFacade->updateProductRating($productId);

        $productRating = $productRatingDao->getProductRatingById($productId);

        $this->assertEquals(
            4,
            $productRating->getRatingAverage()
        );

        $this->assertEquals(
            3,
            $productRating->getRatingCount()
        );
    }

    private function getProductRatingFacade()
    {
        return new ProductRatingFacade(
            $this->getProductRatingService()
        );
    }

    private function getProductRatingService()
    {
        return new ProductRatingService(
            $this->getRatingDaoMock(),
            $this->getProductRatingDao(),
            new RatingCalculatorService()
        );
    }

    private function getProductRatingDao()
    {
        $database = DatabaseProvider::getDb();

        return new ProductRatingDao($database);
    }

    private function getRatingDaoMock()
    {
        $rating1 = new Rating();
        $rating1->setRating(5);

        $rating2 = new Rating();
        $rating2->setRating(4);

        $rating3 = new Rating();
        $rating3->setRating(3);

        $ratingDaoMock = $this->getMockBuilder(RatingDao::class)->disableOriginalConstructor()->getMock();
        $ratingDaoMock
            ->method('getRatingsByProductId')
            ->willReturn(
                new ArrayCollection(
                    [
                        $rating1,
                        $rating2,
                        $rating3,
                    ]
                )
            );

        return $ratingDaoMock;
    }
}
