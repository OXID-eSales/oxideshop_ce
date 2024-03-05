<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Payment;

$sMetadataVersion = '2.0';

$aModule = [
    'id'          => 'TestModuleMetaData20',
    'title'       => 'Module for testModuleMetaData20',
    'description' => [
        'de' => 'de description for testModuleMetaData20',
        'en' => 'en description for testModuleMetaData20',
    ],
    'lang'        => 'en',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'url'         => 'https://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => [
        Payment::class => 'TestModuleMetaData20\Payment',
        'oxArticle'                                        => 'TestModuleMetaData20\Article',
    ],
    'controllers' => [
        'myvendor_mymodule_MyModuleController'      => 'TestModuleMetaData20\Controller',
        'myvendor_mymodule_MyOtherModuleController' => 'TestModuleMetaData20\OtherController',
    ],
    'settings'    => [
        [
            'group' => 'main',
            'name' => 'setting_1',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1|2|3',
            'position' => 3
        ],
        ['group' => 'main', 'name' => 'setting_2', 'type' => 'arr', 'value' => ['value1', 'value2']]
    ],
    'events'      => [
        'onActivate'   => 'TestModuleMetaData20\Events::onActivate',
        'onDeactivate' => 'TestModuleMetaData20\Events::onDeactivate'
    ],

];
