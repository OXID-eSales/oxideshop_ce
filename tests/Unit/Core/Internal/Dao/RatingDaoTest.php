<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Internal\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Internal\Dao\RatingDao;
use OxidEsales\Eshop\Internal\DataObject\Rating;

class RatingDaoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRatingsByUserIdReturnType()
    {
        $ratingDao = new RatingDao($this->getDatabaseMock());
        $ratings = $ratingDao->getRatingsByUserId(1);

        $this->assertInstanceOf(
            ArrayCollection::class,
            $ratings
        );
    }

    public function testGetRatingsByUserIdReturnsCorrectAmountOfEntities()
    {
        $ratingDao = new RatingDao($this->getDatabaseMock());
        $ratings = $ratingDao->getRatingsByUserId(1);

        $this->assertEquals(2, $ratings->count());
    }

    public function testGetRatingsByUserIdReturnsMappedRatings()
    {
        $ratingDao = new RatingDao($this->getDatabaseMock());
        $ratings = $ratingDao->getRatingsByUserId(1);

        $this->assertEquals(
            $this->getTestMappedRating(),
            $ratings->first()
        );
    }

    public function testGetRatingsByProductIdReturnType()
    {
        $ratingDao = new RatingDao($this->getDatabaseMock());
        $ratings = $ratingDao->getRatingsByProductId(1);

        $this->assertInstanceOf(
            ArrayCollection::class,
            $ratings
        );
    }

    public function testGetRatingsByProductIdReturnsCorrectAmountOfEntities()
    {
        $ratingDao = new RatingDao($this->getDatabaseMock());
        $ratings = $ratingDao->getRatingsByProductId(1);

        $this->assertEquals(2, $ratings->count());
    }

    public function testGetRatingsByProductIdReturnsMappedRatings()
    {
        $ratingDao = new RatingDao($this->getDatabaseMock());
        $ratings = $ratingDao->getRatingsByProductId(1);

        $this->assertEquals(
            $this->getTestMappedRating(),
            $ratings->first()
        );
    }

    private function getDatabaseMock()
    {
        $database = $this
            ->getMockBuilder(DatabaseInterface::class)
            ->getMock();

        $database
            ->method('select')
            ->willReturn($this->getTestRatingsDatabaseData());

        return $database;
    }

    private function getTestRatingsDatabaseData()
    {
        return [
            [
                'OXID'          => '1',
                'OXRATING'      => '5',
                'OXOBJECTID'    => '1',
                'OXUSERID'      => '1',
                'OXTYPE'        => 'article',
                'OXTIMESTAMP'   => '2018-03-06 11:48:47',
            ],
            [
                'OXID'          => '2',
                'OXRATING'      => '4',
                'OXOBJECTID'    => '2',
                'OXUSERID'      => '1',
                'OXTYPE'        => 'article',
                'OXTIMESTAMP'   => '2018-03-06 11:48:48',
            ],
        ];
    }

    public function getTestMappedRating()
    {
        $rating = new Rating();
        $rating
            ->setId(1)
            ->setRating(5)
            ->setObjectId(1)
            ->setUserId(1)
            ->setType('article')
            ->setCreatedAt('2018-03-06 11:48:47');

        return $rating;
    }
}
