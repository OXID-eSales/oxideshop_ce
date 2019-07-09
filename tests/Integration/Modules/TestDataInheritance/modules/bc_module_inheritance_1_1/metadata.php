<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'bc_module_inheritance_1_1', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 1.1',
    'description'  => 'Both module class and shop class use the old notation without namespaces',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_1_module_1_myclass' => 'oeTest/bc_module_inheritance_1_1/vendor_1_module_1_myclass.php',
        'vendor_1_module_1_anotherclass' => 'oeTest/bc_module_inheritance_1_1/vendor_1_module_1_anotherclass.php',
        'vendor_1_module_1_onemoreclass' => 'oeTest/bc_module_inheritance_1_1/vendor_1_module_1_onemoreclass.php'
    )
);
