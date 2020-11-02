<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for Orders.
 */
class Order extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /**
     * @var string database table name
     */
    protected $tableName = 'oxorder';

    /**
     * @var string shop object name
     */
    protected $shopObjectName = 'oxorder';
}
