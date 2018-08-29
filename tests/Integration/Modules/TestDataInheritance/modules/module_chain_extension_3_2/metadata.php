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
    'id'          => 'module_chain_extension_3_2',
    'title'       => 'Test OXID eShop class module chain extension 3.2',
    'description' => 'The module class has no namespace and chain extends a namespaced OXID eShop class',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [\OxidEsales\EshopCommunity\Application\Model\Article::class => 'module_chain_extension_3_2/vendor_1_module_3_2_myclass']
);
