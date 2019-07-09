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
    'id'           => 'bc_module_inheritance_4_3', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 4.3',
    'description'  => 'Both module class and shop class use the old notation without namespaces',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_1_module_4_3_myclass' => 'oeTest/bc_module_inheritance_4_3/vendor_1_module_4_3_myclass.php',
    )
);
