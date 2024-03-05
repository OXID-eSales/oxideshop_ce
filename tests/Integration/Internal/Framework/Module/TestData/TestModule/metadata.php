<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

$sMetadataVersion = '2.1';

$aModule = array(
    'id'           => 'test-module',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => [
        'shopClass' => 'testModuleClassExtendsShopClass',
    ],
    'settings' => [
        [
            'group' => 'main',
            'name' => 'test-setting',
            'type' => 'arr',
            'value' => ['Preis', 'Hersteller'],
        ],
        [
            'name' => 'string-setting',
            'type' => 'str',
            'value' => 'default',
        ]
    ],
);
