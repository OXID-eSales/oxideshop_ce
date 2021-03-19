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
        ]
    ],
    'events'      => [
        'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin\Fixtures\testModule\ModuleSetup::onActivate',
        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin\Fixtures\testModule\ModuleSetup::onDeactivate'
    ]
);
