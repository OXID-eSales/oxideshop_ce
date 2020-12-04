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
$aModule = array(
    'id'           => 'test_module_controller_routing_ns',
    'title'        => 'Test metadata_controllers_feature_ns',
    'description'  => '',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'controllers'  => [
        'test_module_controller_routing_ns_MyModuleController' => OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\test_module_controller_routing_ns\MyModuleController::class,
        'test_module_controller_routing_ns_MyOtherModuleController' => OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\test_module_controller_routing_ns\MyOtherModuleController::class,
    ],
    'templates' => [
        'test_module_controller_routing_ns.tpl' => 'test_module_controller_routing_ns.tpl',
        'test_module_controller_routing_ns_other.tpl' => 'test_module_controller_routing_ns_other.tpl'
    ]
);
