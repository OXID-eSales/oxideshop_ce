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
    'id'          => 'Vendor1/ModuleChainExtension36',
    'title'       => 'Test OXID eShop class module chain extension 3.6',
    'description' => 'The module class and the chain extended OXID eShop class life in their namespaces. The OXID eShop class is from the unified namespace.',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [\OxidEsales\Eshop\Application\Model\Article::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension36\MyClass36::class]
);
