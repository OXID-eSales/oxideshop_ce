<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'           => 'ModuleWithNamespace',
    'title'        => 'Test module #10 - namespaced',
    'description'  => 'Raise the price by *3',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'       => [
        \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\ModuleWithNamespace\Application\Model\TestModuleTenPrice::class
    ],
    'controllers'  => [
        'TestModuleTenPaymentController' => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\ModuleWithNamespace\Application\Controller\TestModuleTenPaymentController::class
    ]
];
