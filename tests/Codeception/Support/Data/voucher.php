<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

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
                'OXENDDATE' => date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)),
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
        ],
    'testvoucherarticle' =>
        [
            'oxvoucherseries' => [
                'OXID' => 'testvoucherarticle',
                'OXSHOPID' => 1,
                'OXSERIENR' => 'Article Coupon šÄßüл',
                'OXSERIEDESCRIPTION' => 'Article Coupon šÄßüл',
                'OXDISCOUNT' => 10.00,
                'OXDISCOUNTTYPE' => 'percent',
                'OXBEGINDATE' => '2008-01-01 00:00:00',
                'OXENDDATE' => date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)),
                'OXALLOWSAMESERIES' => 0,
                'OXALLOWOTHERSERIES' => 0,
                'OXALLOWUSEANOTHER' => 0,
                'OXMINIMUMVALUE' => 45.00,
                'OXCALCULATEONCE' => 1
            ],
            'oxvouchers' => [
                'OXDATEUSED' => '0000-00-00',
                'OXRESERVED' => 0,
                'OXVOUCHERNR' => '123456',
                'OXVOUCHERSERIEID' => 'testvoucherarticle',
                'OXID' => 'testvoucherarticle001'
            ],
            'oxobject2discount' => [
                'OXID' => 'testvouchertoarticle001',
                'OXDISCOUNTID' => 'testvoucherarticle',
                'OXOBJECTID' => '10015',
                'OXTYPE' => 'oxarticles',
            ]
        ]
];
