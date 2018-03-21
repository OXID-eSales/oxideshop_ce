<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Bridge;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Review\Dao\RatingDao;
use OxidEsales\EshopCommunity\Internal\Review\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Rating;
use OxidEsales\EshopCommunity\Internal\Review\Service\ProductRatingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\RatingCalculatorService;
use OxidEsales\TestingLibrary\UnitTestCase;

class ProductRatingBridgeTest extends UnitTestCase
{
    public function testUpdateProductRating()
    {
        $productId = '09602cddb5af0aba745293d08ae6bcf6';
        $productRatingDao = $this->getProductRatingDao();
        $productRatingBridge = $this->getProductRatingBridge();

        $productRatingBridge->updateProductRating($productId);

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

    private function getProductRatingBridge()
    {
        return new ProductRatingBridge(
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RatingDaoInterface
     */
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
