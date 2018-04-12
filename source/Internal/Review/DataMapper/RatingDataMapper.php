<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Rating;

/**
 * @internal
 */
class RatingDataMapper implements EntityMapperInterface
{
    /**
     * @param Rating $object
     * @param array  $data
     *
     * @return Rating
     */
    public function map($object, $data)
    {
        $object
            ->setId($data['OXID'])
            ->setRating($data['OXRATING'])
            ->setObjectId($data['OXOBJECTID'])
            ->setUserId($data['OXUSERID'])
            ->setType($data['OXTYPE'])
            ->setCreatedAt($data['OXTIMESTAMP']);

        return $object;
    }

    /**
     * @param Rating $object
     *
     * @return array
     */
    public function getData($object)
    {
        return [
            'OXID'          => $object->getId(),
            'OXRATING'      => $object->getRating(),
            'OXOBJECTID'    => $object->getObjectId(),
            'OXUSERID'      => $object->getUserId(),
            'OXTYPE'        => $object->getType(),
            'OXTIMESTAMP'   => $object->getCreatedAt(),
        ];
    }

    /**
     * @param Rating $object
     *
     * @return array
     */
    public function getPrimaryKey($object)
    {
        return [
            'OXID' => $object->getId(),
        ];
    }
}
