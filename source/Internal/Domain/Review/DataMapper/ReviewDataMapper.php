<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Review;

class ReviewDataMapper implements ReviewDataMapperInterface
{
    /**
     * @param Review $review
     * @param array  $data
     *
     * @return Review
     */
    public function map(Review $review, array $data): Review
    {
        $review
            ->setId($data['OXID'])
            ->setRating($data['OXRATING'])
            ->setText($data['OXTEXT'])
            ->setObjectId($data['OXOBJECTID'])
            ->setUserId($data['OXUSERID'])
            ->setType($data['OXTYPE'])
            ->setCreatedAt($data['OXTIMESTAMP']);

        return $review;
    }

    /**
     * @param Review $review
     *
     * @return array
     */
    public function getData(Review $review): array
    {
        return [
            'OXID'        => $review->getId(),
            'OXRATING'    => $review->getRating(),
            'OXTEXT'      => $review->getText(),
            'OXOBJECTID'  => $review->getObjectId(),
            'OXUSERID'    => $review->getUserId(),
            'OXTYPE'      => $review->getType(),
            'OXTIMESTAMP' => $review->getCreatedAt(),
        ];
    }

    /**
     * @param Review $review
     *
     * @return array
     */
    public function getPrimaryKey(Review $review): array
    {
        return [
            'OXID' => $review->getId(),
        ];
    }
}
