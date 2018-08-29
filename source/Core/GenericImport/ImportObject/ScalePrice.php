<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for Scale Prices.
 */
class ScalePrice extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxprice2article';

    /** @var array List of database key fields (i.e. oxid). */
    protected $keyFieldList = [
        'OXID' => 'OXID'
    ];
}
