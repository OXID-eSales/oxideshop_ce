<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

class RatingDataMapper implements RatingDataMapperInterface
{
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

    public function getData(Rating $rating): array
    {
        return [
            'OXID' => $rating->getId(),
            'OXRATING' => $rating->getRating(),
            'OXOBJECTID' => $rating->getObjectId(),
            'OXUSERID' => $rating->getUserId(),
            'OXTYPE' => $rating->getType(),
            'OXTIMESTAMP' => $rating->getCreatedAt(),
        ];
    }

    public function getPrimaryKey(Rating $object): array
    {
        return [
            'OXID' => $object->getId(),
        ];
    }
}
