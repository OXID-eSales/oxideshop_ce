<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\EshopCommunity\Internal\Review\Exception\ReviewAndRatingObjectTypeException;
use OxidEsales\EshopCommunity\Internal\Review\ViewDataObject\ReviewAndRating;
use OxidEsales\TestingLibrary\UnitTestCase;

class ReviewTest extends UnitTestCase
{
    public function testReviewAndRatingListByUserId()
    {
        $review = oxNew(Review::class);
        $review->setId('id1');
        $review->oxreviews__oxactive = new Field(1);
        $review->oxreviews__oxuserid = new Field('testUser');
        $review->oxreviews__oxobjectid = new Field('xx1');
        $review->oxreviews__oxtype = new Field('oxarticle');
        $review->oxreviews__oxtext = new Field('revtext');
        $review->save();

        $review = oxNew(Review::class);
        $review->setId('id2');
        $review->oxreviews__oxactive = new Field(1);
        $review->oxreviews__oxuserid = new Field('testUser');
        $review->oxreviews__oxobjectid = new Field('xx2');
        $review->oxreviews__oxtype = new Field('oxrecommlist');
        $review->oxreviews__oxtext = new Field('revtext');
        $review->save();

        $rating = oxNew(Rating::class);
        $rating->oxratings__oxuserid = new Field('testUser');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->save();

        $review = oxNew(Review::class);

        $reviewAndRatingList = $review->getReviewAndRatingListByUserId('testUser');

        $this->assertInternalType(
            'array',
            $reviewAndRatingList
        );

        $this->assertCount(
            3,
            $reviewAndRatingList
        );

        $this->assertContainsOnlyInstancesOf(
            ReviewAndRating::class,
            $reviewAndRatingList
        );
    }

    public function testReviewAndRatingListByUserIdWithWrongRatingType()
    {
        $this->setExpectedException(ReviewAndRatingObjectTypeException::class);

        $rating = oxNew(Rating::class);
        $rating->oxratings__oxuserid = new Field('testUser');
        $rating->oxratings__oxtype = new Field('wrong_type');
        $rating->save();

        $review = oxNew(Review::class);

        $review->getReviewAndRatingListByUserId('testUser');
    }
}
