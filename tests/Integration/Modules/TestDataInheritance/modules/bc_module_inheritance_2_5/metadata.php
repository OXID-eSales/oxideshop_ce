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
    'id'           => 'bc_module_inheritance_2_5', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 2.5',
    'description'  => 'All involved module classes and shop class use the old notation without namespaces',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_2_module_5_myclass' => 'oeTest/bc_module_inheritance_2_5/vendor_2_module_5_myclass.php'
    )
);
