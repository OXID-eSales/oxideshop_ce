<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

return [
    'testvoucher4' =>
        [
            'oxvoucherseries' => [
                'OXID' => 'testvoucher4',
                'OXSHOPID' => 1,
                'OXSERIENR' => '4 Coupon šÄßüл',
                'OXSERIEDESCRIPTION' => '4 Coupon šÄßüл',
                'OXDISCOUNT' => 50.00,
                'OXDISCOUNTTYPE' => 'percent',
                'OXBEGINDATE' => '2008-01-01 00:00:00',
                'OXENDDATE' => '2020-01-01 00:00:00',
                'OXALLOWSAMESERIES' => 0,
                'OXALLOWOTHERSERIES' => 0,
                'OXALLOWUSEANOTHER' => 0,
                'OXMINIMUMVALUE' => 45.00,
                'OXCALCULATEONCE' => 1
            ],
            'oxvouchers' => [
                [
                    'OXDATEUSED' => '0000-00-00',
                    'OXRESERVED' => 0,
                    'OXVOUCHERNR' => '123123',
                    'OXVOUCHERSERIEID' => 'testvoucher4',
                    'OXID' => 'testcoucher011'
                ]
            ]
        ]
];
