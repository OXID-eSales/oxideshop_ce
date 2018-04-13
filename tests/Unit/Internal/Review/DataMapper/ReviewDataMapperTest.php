<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Review\DataMapper\ReviewDataMapper;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;

class ReviewDataMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMapping()
    {
        $mapper = new ReviewDataMapper();

        $mappedReview = $this->getMappedReview();
        $dataForMapping = $mapper->getData($mappedReview);

        $review = new Review();
        $reviewAfterMapping = $mapper->map($review, $dataForMapping);

        $this->assertEquals(
            $mappedReview,
            $reviewAfterMapping
        );
    }

    public function testPrimaryKeyGetter()
    {
        $mapper = new ReviewDataMapper();
        $mappedReview = $this->getMappedReview();

        $expectedPrimaryKey = [
            'OXID' => 'testId',
        ];

        $this->assertEquals(
            $expectedPrimaryKey,
            $mapper->getPrimaryKey($mappedReview)
        );
    }

    private function getMappedReview()
    {
        $review = new Review();
        $review
            ->setId('testId')
            ->setText('so so')
            ->setRating(3)
            ->setUserId('userId')
            ->setObjectId('objectId')
            ->setType('product')
            ->setCreatedAt('time');

        return $review;
    }
}
