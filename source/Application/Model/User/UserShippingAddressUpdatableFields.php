<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model\User;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Contract\AbstractUpdatableFields;

/**
 * @inheritdoc
 */
class UserShippingAddressUpdatableFields extends AbstractUpdatableFields
{
    /**
     * UserShippingAddressUpdatableFields constructor.
     */
    public function __construct()
    {
        $address = oxNew(Address::class);
        $this->tableName = $address->getCoreTableName();
    }

    /**
     * Return list of fields which could be updated by shop customer.
     *
     * @return array
     */
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
