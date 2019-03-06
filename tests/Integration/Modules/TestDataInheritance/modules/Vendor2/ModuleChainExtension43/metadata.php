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
    'id'          => 'Vendor2/ModuleChainExtension43',
    'title'       => 'Test OXID eShop class module chain extension 4.3',
    'description' => 'The module class and the chain extended OXID eShop class life in their namespaces.',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [
        'vendor_1_module_4_3_myclass' => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension43\MyClass43::class
    ]
);
