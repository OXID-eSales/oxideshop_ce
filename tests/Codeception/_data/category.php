<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

return [
    'oxcategories' => [
        [
            'OXID' => 'testcategory0',
            'OXPARENTID' => 'oxrootid',
            'OXLEFT' => 1,
            'OXRIGHT' => 4,
            'OXROOTID' => 'testcategory0',
            'OXSORT' => 1,
            'OXACTIVE' => 1,
            'OXSHOPID' => 1,
            'OXTITLE' => 'Test category 0 [DE] šÄßüл',
            'OXDESC' => 'Test category 0 desc [DE]',
            'OXLONGDESC' => 'Category 0 long desc [DE]',
            'OXDEFSORT' => 'oxartnum',
            'OXDEFSORTMODE' => 0,
            'OXPRICEFROM' => 0,
            'OXPRICETO' => 0,
            'OXACTIVE_1' => 1,
            'OXTITLE_1' => 'Test category 0 [EN] šÄßüл',
            'OXDESC_1' => 'Test category 0 desc [EN] šÄßüл',
            'OXLONGDESC_1' => 'Category 0 long desc [EN] šÄßüл',
            'OXVAT' => 5,
            'OXSHOWSUFFIX' => 1
        ],
        [
            'OXID' => 'testcategory1',
            'OXPARENTID' => 'testcategory0',
            'OXLEFT' => 2,
            'OXRIGHT' => 3,
            'OXROOTID' => 'testcategory0',
            'OXSORT' => 2,
            'OXACTIVE' => 1,
            'OXSHOPID' => 1,
            'OXTITLE' => 'Test category 1 [DE] šÄßüл',
            'OXDESC' => 'Test category 1 desc [DE]',
            'OXLONGDESC' => 'Category 1 long desc [DE]',
            'OXDEFSORT' => 'oxartnum',
            'OXDEFSORTMODE' => 1,
            'OXPRICEFROM' => 0,
            'OXPRICETO' => 0,
            'OXACTIVE_1' => 1,
            'OXTITLE_1' => 'Test category 1 [EN] šÄßüл',
            'OXDESC_1' => 'Test category 1 desc [EN] šÄßüл',
            'OXLONGDESC_1' => 'Category 1 long desc [EN] šÄßüл',
            'OXVAT' => null,
            'OXSHOWSUFFIX' => 1
        ]
    ],
    'oxobject2category' => [
        [
            'OXID' => '6f047a71f53e3b6c2.93342239',
            'OXOBJECTID' => '1000',
            'OXCATNID' => 'testcategory0',
            'OXPOS' => 0,
            'OXTIME' => 1202134867
        ],
        [
            'OXID' => 'testobject2category',
            'OXOBJECTID' => '1001',
            'OXCATNID' => 'testcategory0',
            'OXPOS' => 0,
            'OXTIME' => 1202134867
        ]
    ]
];
