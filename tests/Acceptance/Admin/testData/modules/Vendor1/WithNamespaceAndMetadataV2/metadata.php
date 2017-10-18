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
    'id'           => 'EshopAcceptanceTestModuleNine',
    'title'        => 'Test module #9 - namespaced (EshopAcceptanceTestModuleNine)',
    'description'  => 'Raise the price.',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'       => [
        \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\Vendor1\WithNamespaceAndMetadataV2\Application\Model\TestModuleNinePrice::class
    ],
    'controllers'  => [
        'vendor1_metadatav2demo_mymodulecontroller' => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\Vendor1\WithNamespaceAndMetadataV2\Application\Controller\MyModuleController::class
    ],
    'templates' => [
        'vendor1_controller_routing.tpl' => 'Vendor1/WithNamespaceAndMetadataV2/views/tpl/vendor1_controller_routing.tpl'
    ]
];
