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
    'id'           => 'bc_module_inheritance_2_1', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 2.1',
    'description'  => 'Both module class and shop class use the old notation without namespaces. Module 2.1 extends module 1.1',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_2_module_1_myclass' => 'oeTest/bc_module_inheritance_2_1/vendor_2_module_1_myclass.php'
    )
);
