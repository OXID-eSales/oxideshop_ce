<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Common\DataMapper;

/**
 * @internal
 */
interface ObjectMapperInterface
{
    /**
     * Maps a data from a storage to object.
     *
     * @param object $object
     * @param array  $data
     *
     * @return object
     */
    public function map($object, $data);

    /**
     * Returns an object data mapped to storage fields in format like storageFieldName => value
     *
     * @param object $object
     *
     * @return array
     */
    public function getData($object);
}
