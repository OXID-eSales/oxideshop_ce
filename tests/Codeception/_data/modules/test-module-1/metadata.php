<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$sMetadataVersion = '1.1';

$aModule = [
    'id' => 'codeception/test-module-1',
    'title' => 'Codeception test module #1',
    'description' => 'Working module configuration with possible data types in settings',
    'thumbnail' => '',
    'version' => '1.0',
    'author' => 'OXID',
    'extend' => [
        'content' => 'test-module-1/Controller/ContentController'
    ],
    'settings' => [
        /** Group of empty values */
        [
            'group' => 'settingsEmpty',
            'name' => 'testEmptyBoolConfig',
            'type' => 'bool',
            'value' => 'false',
        ],
        [
            'group' => 'settingsEmpty',
            'name' => 'testEmptyStrConfig',
            'type' => 'str',
            'value' => '',
        ],
        [
            'group' => 'settingsEmpty',
            'name' => 'testEmptyArrConfig',
            'type' => 'arr',
            'value' => '',
        ],
        [
            'group' => 'settingsEmpty',
            'name' => 'testEmptyAArrConfig',
            'type' => 'aarr',
            'value' => '',
        ],
        [
            'group' => 'settingsEmpty',
            'name' => 'testEmptySelectConfig',
            'type' => 'select',
            'value' => '',
            'constraints' => '0|1|2',
        ],
        [
            'group' => 'settingsEmpty',
            'name' => 'testEmptyPasswordConfig',
            'type' => 'password',
            'value' => '',
        ],
        /** Group of non-empty values */
        [
            'group' => 'settingsFilled',
            'name' => 'testFilledBoolConfig',
            'type' => 'bool',
            'value' => 'true',
        ],
        [
            'group' => 'settingsFilled',
            'name' => 'testFilledStrConfig',
            'type' => 'str',
            'value' => 'testStr',
        ],
        [
            'group' => 'settingsFilled',
            'name' => 'testFilledArrConfig',
            'type' => 'arr',
            'value' => [
                'option1',
                'option2',
            ],
        ],
        [
            'group' => 'settingsFilled',
            'name' => 'testFilledAArrConfig',
            'type' => 'aarr',
            'value' => [
                'key1' => 'option1',
                'key2' => 'option2',
            ],
        ],
        [
            'group' => 'settingsFilled',
            'name' => 'testFilledSelectConfig',
            'type' => 'select',
            'value' => '2',
            'constraints' => '0|1|2',
            'position' => 3,
        ],
        [
            'group' => 'settingsFilled',
            'name' => 'testFilledPasswordConfig',
            'type' => 'password',
            'value' => 'testPassword',
        ],
    ]
];
