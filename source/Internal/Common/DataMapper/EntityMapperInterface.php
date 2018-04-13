<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Common\DataMapper;

/**
 * @internal
 */
interface EntityMapperInterface extends ObjectMapperInterface
{
    /**
     * Returns array with storage primary key of an object in format like storageFieldName => value
     *
     * @param object $object
     *
     * @return array
     */
    public function getPrimaryKey($object);
}
