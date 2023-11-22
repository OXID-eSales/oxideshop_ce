<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model\Address;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Contract\AbstractUpdatableFields;

class ShippingAddressUpdatableFields extends AbstractUpdatableFields
{
    public function __construct()
    {
        $user = oxNew(Address::class);
        $this->tableName = $user->getCoreTableName();
    }
    public function getUpdatableFields()
    {
        return [
            'OXCOMPANY',
            'OXFNAME',
            'OXLNAME',
            'OXSTREET',
            'OXSTREETNR',
            'OXADDINFO',
            'OXCITY',
            'OXCOUNTRY',
            'OXCOUNTRYID',
            'OXSTATEID',
            'OXZIP',
            'OXFON',
            'OXFAX',
            'OXSAL',
            'OXTIMESTAMP'
        ];
    }
}
