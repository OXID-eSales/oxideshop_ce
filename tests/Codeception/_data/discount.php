<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

return [
    'testcatdiscount' =>
        [
            'oxdiscount' => [
                'OXID' => 'testcatdiscount',
                'OXSHOPID' => 1,
                'OXACTIVE' => 1,
                'OXTITLE' => 'discount for category [DE] šÄßüл',
                'OXTITLE_1' => 'discount for category [EN] šÄßüл',
                'OXAMOUNT' => 1,
                'OXAMOUNTTO' => 999999,
                'OXPRICETO' => 0,
                'OXPRICE' => 0,
                'OXADDSUMTYPE' => 'abs',
                'OXADDSUM' => 5,
                'OXITMARTID' => '',
                'OXITMAMOUNT' => 0,
                'OXITMMULTIPLE' => 0,
                'OXSORT' => 100
            ],
            'oxobject2discount' => [
                [
                    'OXID' => 'fa647a823ce118996.58546955',
                    'OXDISCOUNTID' => 'testcatdiscount',
                    'OXOBJECTID' => 'a7c40f631fc920687.20179984',
                    'OXTYPE' => 'oxcountry'
                ],
                [
                    'OXID' => 'fa647a823d5079104.99115703',
                    'OXDISCOUNTID' => 'testcatdiscount',
                    'OXOBJECTID' => 'testcategory0',
                    'OXTYPE' => 'oxcategories'
                ]
            ]
        ]
];
