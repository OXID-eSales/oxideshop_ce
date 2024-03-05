<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use DateTime;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\UtilsDate;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Exception\ReviewAndRatingObjectTypeException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\ViewDataObject\ReviewAndRating;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ReviewTest extends IntegrationTestCase
{
    public function testReviewAndRatingListByUserId(): void
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

        $this->assertIsArray(
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

    public function testGetReviewAndRatingListByUserIdWithWrongRatingType(): void
    {
        /** @var  $wrongTypeValue see `oxreview`.`oxtype` enum('oxarticle', 'oxrecommlist') */
        $wrongTypeValue = 'wrong_type';
        $review = oxNew(Review::class);
        $review->oxreviews__oxuserid = new Field('testUser');
        $review->oxreviews__oxtype = new Field($wrongTypeValue);
        $review->save();

        $this->expectException(ReviewAndRatingObjectTypeException::class);

        $review->getReviewAndRatingListByUserId('testUser');
    }

    public function testLoadListFormatsCreateDates(): void
    {
        $reviewType = 'oxrecommlist';
        $objectId = uniqid('id-', true);
        $createdDate = new DateTime();
        $formattedDate = (oxNew(UtilsDate::class)->formatDBDate($createdDate->format('Y/m/d H:i:s')));
        for ($i = 0; $i < 2; $i++) {
            $review = oxNew(Review::class);
            $review->oxreviews__oxobjectid = new Field($objectId);
            $review->oxreviews__oxtype = new Field($reviewType);
            $review->oxreviews__oxlang = new Field(0);
            $review->oxcreate = new Field($createdDate);
            $review->save();
        }

        $list = (oxNew(Review::class))->loadList($reviewType, $objectId, true, 0);

        foreach ($list as $review) {
            $this->assertEqualsWithDelta(
                DateTime::createFromFormat('d.m.Y H:i:s', $formattedDate),
                DateTime::createFromFormat('d.m.Y H:i:s', $review->getFieldData('oxcreate')),
                1
            );
        }
    }
}
