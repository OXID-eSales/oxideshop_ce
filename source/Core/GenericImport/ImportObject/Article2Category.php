<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for Articles assignment to categories.
 */
class Article2Category extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /**
     * @var string database table name
     */
    protected $tableName = 'oxobject2category';

    /**
     * @var array List of database key fields (i.e. oxid).
     */
    protected $keyFieldList = [
        'OXOBJECTID' => 'OXOBJECTID',
        'OXCATNID' => 'OXCATNID',
    ];
}
