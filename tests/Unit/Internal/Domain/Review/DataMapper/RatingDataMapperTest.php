<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper\RatingDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

class RatingDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function testMapping()
    {
        $mapper = new RatingDataMapper();

        $mappedRating = $this->getMappedRating();
        $dataForMapping = $mapper->getData($mappedRating);

        $rating = new Rating();
        $ratingAfterMapping = $mapper->map($rating, $dataForMapping);

        $this->assertEquals(
            $mappedRating,
            $ratingAfterMapping
        );
    }

    public function testPrimaryKeyGetter()
    {
        $mapper = new RatingDataMapper();
        $mappedRating = $this->getMappedRating();

        $expectedPrimaryKey = [
            'OXID' => 'testId',
        ];

        $this->assertEquals(
            $expectedPrimaryKey,
            $mapper->getPrimaryKey($mappedRating)
        );
    }

    private function getMappedRating()
    {
        $rating = new Rating();
        $rating
            ->setId('testId')
            ->setRating(5)
            ->setUserId('userId')
            ->setObjectId('objectId')
            ->setType('product')
            ->setCreatedAt('time');

        return $rating;
    }
}
