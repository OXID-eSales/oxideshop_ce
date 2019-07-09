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
    'id'          => 'module_chain_extension_4_2',
    'title'       => 'Test plain module class chain extension of namespaced module class 4.2',
    'description' => 'This module has the non namespaced class in it',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension42\MyClass42::class => 'oeTest/module_chain_extension_4_2/module_chain_extension_4_2_myclass']
);
