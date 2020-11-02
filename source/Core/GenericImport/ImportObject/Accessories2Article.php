<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for assignment of accessories to articles.
 */
class Accessories2Article extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /**
     * @var string database table name
     */
    protected $tableName = 'oxaccessoire2article';
}
