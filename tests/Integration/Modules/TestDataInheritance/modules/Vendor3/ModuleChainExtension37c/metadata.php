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
    'id'          => 'Vendor3/ModuleChainExtension37c',
    'title'       => 'Test OXID eShop class module chain extension 3.7c',
    'description' => 'The test has three test modules - they are sorted in every possible order in different test cases.',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => ['oxArticle' => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c::class]
);
