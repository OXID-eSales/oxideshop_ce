<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\ReviewDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Review;

class ReviewDataMapperTest extends \PHPUnit\Framework\TestCase
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
