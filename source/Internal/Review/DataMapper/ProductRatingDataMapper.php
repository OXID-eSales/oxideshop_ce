<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Common\DataMapper\EntityMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\ProductRating;

/**
 * @internal
 */
class ProductRatingDataMapper implements EntityMapperInterface
{
    /**
     * @param ProductRating $object
     * @param array         $data
     *
     * @return ProductRating
     */
    public function map($object, $data)
    {
        $object
            ->setProductId($data['OXID'])
            ->setRatingAverage($data['OXRATING'])
            ->setRatingCount($data['OXRATINGCNT']);

        return $object;
    }

    /**
     * @param ProductRating $object
     *
     * @return array
     */
    public function getData($object)
    {
        return [
            'OXID'          => $object->getProductId(),
            'OXRATING'      => $object->getRatingAverage(),
            'OXRATINGCNT'   => $object->getRatingCount(),
        ];
    }

    /**
     * @param ProductRating $object
     *
     * @return array
     */
    public function getPrimaryKey($object)
    {
        return [
            'OXID' => $object->getProductId(),
        ];
    }
}
