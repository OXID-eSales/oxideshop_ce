<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

class RatingDataMapper implements RatingDataMapperInterface
{
    /**
     * @param Rating $rating
     * @param array  $data
     *
     * @return Rating
     */
    public function map(Rating $rating, array $data): Rating
    {
        $rating
            ->setId($data['OXID'])
            ->setRating($data['OXRATING'])
            ->setObjectId($data['OXOBJECTID'])
            ->setUserId($data['OXUSERID'])
            ->setType($data['OXTYPE'])
            ->setCreatedAt($data['OXTIMESTAMP']);

        return $rating;
    }

    /**
     * @param Rating $rating
     *
     * @return array
     */
    public function getData(Rating $rating): array
    {
        return [
            'OXID'        => $rating->getId(),
            'OXRATING'    => $rating->getRating(),
            'OXOBJECTID'  => $rating->getObjectId(),
            'OXUSERID'    => $rating->getUserId(),
            'OXTYPE'      => $rating->getType(),
            'OXTIMESTAMP' => $rating->getCreatedAt(),
        ];
    }

    /**
     * @param Rating $object
     *
     * @return array
     */
    public function getPrimaryKey(Rating $object): array
    {
        return [
            'OXID' => $object->getId(),
        ];
    }
}
