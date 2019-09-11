<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test-module',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => [
        'shopClass' => 'testModuleClassExtendsShopClass',
    ],
    'smartyPluginDirectories'  => [
        'SmartyPlugins/directory1',
        'SmartyPlugins/directory2',
    ],
    'settings' => [
        [
            'group' => 'main',
            'name' => 'setting',
            'type' => 'arr',
            'value' => ['Preis', 'Hersteller'],
        ]
    ],
);
