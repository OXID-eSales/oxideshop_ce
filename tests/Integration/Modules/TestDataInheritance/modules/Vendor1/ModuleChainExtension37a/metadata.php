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
    'id'          => 'Vendor1/ModuleChainExtension37a',
    'title'       => 'Test OXID eShop class module chain extension 3.7a',
    'description' => 'The test has three test modules - they are sorted in every possible order in different test cases.',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [\OxidEsales\Eshop\Application\Model\Article::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a::class]
);
