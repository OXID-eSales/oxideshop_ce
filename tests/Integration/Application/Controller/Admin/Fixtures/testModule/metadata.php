<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

$sMetadataVersion = '2.1';

$aModule = array(
    'id'          => 'testModuleId',
    'title'       => 'myTestModule',
    'description' => 'myTestModule',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'settings'    => [
        [
            'group' => 'someGroup',
            'name' => 'stringSetting',
            'type' => 'str',
            'value' => 'row'
        ],
        [
            'group' => 'someGroup',
            'name' => 'testInt',
            'type' => 'num',
            'value' => 0
        ],
        [
            'group' => 'someGroup',
            'name' => 'testFloat',
            'type' => 'num',
            'value' => 0.0
        ]
    ],
    'events'      => [
        'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin\Fixtures\testModule\ModuleSetup::onActivate',
        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin\Fixtures\testModule\ModuleSetup::onDeactivate'
    ]
);
