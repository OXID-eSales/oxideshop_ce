<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'Vendor3_ModuleChainExtension37c',
    'title' => 'Test OXID eShop class module chain extension 3.7c',
    'description' => 'The test has three test modules - they are sorted in every possible order in different test cases.',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        'oxArticle' => MyClass37c::class,
    ],
];
