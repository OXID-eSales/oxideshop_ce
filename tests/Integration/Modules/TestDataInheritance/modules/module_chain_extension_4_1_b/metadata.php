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
    'id'          => 'module_chain_extension_4_1_b',
    'title'       => 'Test OXID eShop class module chain extension 4.1',
    'description' => 'The module class has no namespace and chain extends a non namespaced other module class.',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => ['vendor_1_module_4_1_a_myclass' => 'oeTest/module_chain_extension_4_1_b/vendor_1_module_4_1_b_myclass']
);
