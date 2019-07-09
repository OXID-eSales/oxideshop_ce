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
    'id'           => 'bc_module_inheritance_1_2', // maybe find a better name for that
    'title'        => 'Test backwards compatible PHP class inheritance 1.2',
    'description'  => 'Non namespace module class extends eShopCommunity namespace class, no patch.',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'       => array(
        'vendor_1_module_2_myclass' => 'oeTest/bc_module_inheritance_1_2/vendor_1_module_2_myclass.php'
    )
);
