<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model\User;

/**
 * @inheritdoc
 */
class UserInvoiceAddressUpdatableFields extends UserUpdatableFields
{
    public function getUpdatableFields()
    {
        return [
            'OXUSERNAME',
            'OXCUSTNR',
            'OXUSTID',
            'OXCOMPANY',
            'OXFNAME',
            'OXLNAME',
            'OXSTREET',
            'OXSTREETNR',
            'OXADDINFO',
            'OXCITY',
            'OXCOUNTRYID',
            'OXSTATEID',
            'OXZIP',
            'OXFON',
            'OXFAX',
            'OXSAL',
            'OXCREATE',
            'OXREGISTER',
            'OXPRIVFON',
            'OXMOBFON',
            'OXBIRTHDATE',
            'OXURL',
            'OXUPDATEKEY',
            'OXUPDATEEXP',
            'OXTIMESTAMP'
        ];
    }
}
