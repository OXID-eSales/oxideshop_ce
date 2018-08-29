<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test_module_controller_routing',
    'title'        => 'Test metadata_controllers_feature',
    'description'  => '',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'  => [
        'test_module_controller_routing_MyModuleController' => 'test_module_controller_routing/test_module_controller_routing_MyModuleController.php',
        'test_module_controller_routing_MyOtherModuleController' => 'test_module_controller_routing/test_module_controller_routing_MyOtherModuleController.php',
    ],
    'templates' => [
        'test_module_controller_routing.tpl' => 'test_module_controller_routing/test_module_controller_routing.tpl',
        'test_module_controller_routing_other.tpl' => 'test_module_controller_routing/test_module_controller_routing_other.tpl'
    ]
);
