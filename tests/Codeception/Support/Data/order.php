<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

return [
    'testorder' =>
        [
            'OXID' => 'testorder',
            'OXSHOPID' => 1,
            'OXUSERID' => 'testuser',
            'OXORDERDATE' => '2023-03-30 11:00:04',
            'OXORDERNR' => '123',
            'OXBILLCOMPANY' => 'test bill company_name',
            'OXBILLEMAIL' => 'test@billemail.com',
            'OXBILLFNAME' => 'test bill fname',
            'OXBILLLNAME' => 'test bill lname',
            'OXBILLSTREET' => 'test address street',
            'OXBILLSTREETNR' => 'streetNBR',
            'OXBILLADDINFO' => 'test address info',
            'OXBILLCITY' => 'test address city',
            'OXBILLCOUNTRYID' => 'testcountry_de',
            'OXBILLZIP' => '55555',
            'OXREMARK' => 'custom user order remark',
            'OXFOLDER' => 'ORDERFOLDER_NEW',
            'OXBILLSTATEID' => 'BB',
            'OXCARDTEXT' => '',
            'PRODUCTS' => [
                [
                    'OXID' => '919edbc539f414bdefc7f6975bbdf2a1',
                    'OXAMOUNT' => '100',
                    'OXARTID' => '1000',
                    'OXARTNUM' => '1000',
                    'OXTITLE' => '[DE 4] Test product 0 šÄßüл'
                ],
                [
                    'OXID' => '919edbc539f414bdefc7f6975bbdf2b6',
                    'OXAMOUNT' => '150',
                    'OXARTID' => '1001',
                    'OXARTNUM' => '1001',
                    'OXTITLE' => '[DE 1] Test product 1 šÄßüл'
                ],
            ]
        ]
];
