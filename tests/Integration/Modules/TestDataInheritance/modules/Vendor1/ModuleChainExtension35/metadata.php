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
    'id'          => 'Vendor1/ModuleChainExtension35',
    'title'       => 'Test OXID eShop class module chain extension 3.5',
    'description' => 'The module class and the chain extended OXID eShop class life in their namespaces.',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [\OxidEsales\EshopCommunity\Application\Model\Article::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension35\MyClass35::class]
);
