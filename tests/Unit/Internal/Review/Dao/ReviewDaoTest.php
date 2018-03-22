<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ReviewDao;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;

class ReviewDaoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReviewsByUserIdReturnType()
    {
        $reviewDao = new ReviewDao($this->getDatabaseMock());
        $reviews = $reviewDao->getReviewsByUserId(1);

        $this->assertInstanceOf(
            ArrayCollection::class,
            $reviews
        );
    }

    public function testGetReviewsByUserIdReturnsCorrectAmountOfEntities()
    {
        $reviewDao = new ReviewDao($this->getDatabaseMock());
        $reviews = $reviewDao->getReviewsByUserId(1);

        $this->assertEquals(2, $reviews->count());
    }

    public function testGetReviewsByUserIdReturnsMappedReviews()
    {
        $reviewDao = new ReviewDao($this->getDatabaseMock());
        $reviews = $reviewDao->getReviewsByUserId(1);

        $this->assertEquals(
            $this->getTestMappedReview(),
            $reviews->first()
        );
    }

    private function getDatabaseMock()
    {
        $database = $this
            ->getMockBuilder(DatabaseInterface::class)
            ->getMock();

        $database
            ->method('select')
            ->willReturn($this->getTestReviewsDatabaseData());

        return $database;
    }

    private function getTestReviewsDatabaseData()
    {
        return [
            [
                'OXID'          => '1',
                'OXRATING'      => '5',
                'OXOBJECTID'    => '1',
                'OXUSERID'      => '1',
                'OXTEXT'        => 'Test text',
                'OXTYPE'        => 'article',
                'OXTIMESTAMP'   => '2018-03-06 11:48:47',
            ],
            [
                'OXID'          => '2',
                'OXRATING'      => '4',
                'OXOBJECTID'    => '2',
                'OXTEXT'        => 'Test text',
                'OXUSERID'      => '1',
                'OXTYPE'        => 'article',
                'OXTIMESTAMP'   => '2018-03-06 11:48:48',
            ],
        ];
    }

    public function getTestMappedReview()
    {
        $review = new Review();
        $review
            ->setId(1)
            ->setRating(5)
            ->setObjectId(1)
            ->setUserId(1)
            ->setText('Test text')
            ->setType('article')
            ->setCreatedAt('2018-03-06 11:48:47');

        return $review;
    }
}
