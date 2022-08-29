<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Dao;

use OxidEsales\Eshop\Application\Model\Rating as EshopRating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class RatingDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGetRatingsByUserId(): void
    {
        $this->createTestRatingsForGetRatingsByUserIdTest();

        $ratings = $this->get(RatingDaoInterface::class)
            ->getRatingsByUserId('user1');

        $this->assertCount(2, $ratings->toArray());
        $this->assertInstanceOf(Rating::class, $ratings->first());
    }

    public function testGetRatingsByProductId(): void
    {
        $this->createTestRatingsForGetRatingsByProductIdTest();

        $ratings = $this->get(RatingDaoInterface::class)
            ->getRatingsByProductId('product1');

        $this->assertCount(2, $ratings->toArray());
        $this->assertInstanceOf(Rating::class, $ratings->first());
    }

    public function testDeleteRating(): void
    {
        $this->createTestRatingsForDeleteRatingTest();

        $ratingDao = $this->get(RatingDaoInterface::class);

        $ratingsBeforeDeletion = $ratingDao->getRatingsByUserId('user1');
        $ratingToDelete = $ratingsBeforeDeletion->first();

        $ratingDao->delete($ratingToDelete);

        $ratingsAfterDeletion = $ratingDao->getRatingsByUserId('user1');

        $this->assertNotContains(
            $ratingToDelete,
            $ratingsAfterDeletion->toArray()
        );
    }

    private function createTestRatingsForDeleteRatingTest(): void
    {
        $rating = oxNew(EshopRating::class);
        $rating->setId('id1');
        $rating->oxratings__oxuserid = new Field('user1');
        $rating->save();

        $rating = oxNew(EshopRating::class);
        $rating->setId('id2');
        $rating->oxratings__oxuserid = new Field('user1');
        $rating->save();
    }

    private function createTestRatingsForGetRatingsByUserIdTest(): void
    {
        $rating = oxNew(EshopRating::class);
        $rating->setId('id1');
        $rating->oxratings__oxuserid = new Field('user1');
        $rating->save();

        $rating = oxNew(EshopRating::class);
        $rating->setId('id2');
        $rating->oxratings__oxuserid = new Field('user1');
        $rating->save();

        $rating = oxNew(EshopRating::class);
        $rating->setId('id3');
        $rating->oxratings__oxuserid = new Field('userNotMatched');
        $rating->save();
    }

    private function createTestRatingsForGetRatingsByProductIdTest(): void
    {
        $rating = oxNew(EshopRating::class);
        $rating->setId('id1');
        $rating->oxratings__oxobjectid = new Field('product1');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->save();

        $rating = oxNew(EshopRating::class);
        $rating->setId('id2');
        $rating->oxratings__oxobjectid = new Field('product1');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->save();

        $rating = oxNew(EshopRating::class);
        $rating->setId('id3');
        $rating->oxratings__oxobjectid = new Field('productNotMatched');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->save();

        $rating = oxNew(EshopRating::class);
        $rating->setId('id4');
        $rating->oxratings__oxobjectid = new Field('product1');
        $rating->oxratings__oxtype = new Field('oxrecommlist');
        $rating->save();
    }
}
