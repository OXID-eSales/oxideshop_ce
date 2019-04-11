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
    'id'           => 'bc_module_inheritance_1_5', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 1.5',
    'description'  => 'Module class uses old notation and inherits from unified namespace shop class',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_1_module_5_myclass' => 'oeTest/bc_module_inheritance_1_5/vendor_1_module_5_myclass.php'
    )
);
