<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'Vendor2_ModuleChainExtension37b',
    'title' => 'Test OXID eShop class module chain extension 3.7b',
    'description' => 'The test has three test modules - they are sorted in every possible order in different test cases.',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Article::class => MyClass37b::class,
    ],
];
