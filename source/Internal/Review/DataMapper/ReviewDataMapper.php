<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;

/**
 * @internal
 */
class ReviewDataMapper implements EntityMapperInterface
{
    /**
     * @param Review $object
     * @param array  $data
     *
     * @return Review
     */
    public function map($object, $data)
    {
        $object
            ->setId($data['OXID'])
            ->setRating($data['OXRATING'])
            ->setText($data['OXTEXT'])
            ->setObjectId($data['OXOBJECTID'])
            ->setUserId($data['OXUSERID'])
            ->setType($data['OXTYPE'])
            ->setCreatedAt($data['OXTIMESTAMP']);

        return $object;
    }

    /**
     * @param Review $object
     *
     * @return array
     */
    public function getData($object)
    {
        return [
            'OXID'          => $object->getId(),
            'OXRATING'      => $object->getRating(),
            'OXTEXT'        => $object->getText(),
            'OXOBJECTID'    => $object->getObjectId(),
            'OXUSERID'      => $object->getUserId(),
            'OXTYPE'        => $object->getType(),
            'OXTIMESTAMP'   => $object->getCreatedAt(),
        ];
    }

    /**
     * @param Review $object
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
