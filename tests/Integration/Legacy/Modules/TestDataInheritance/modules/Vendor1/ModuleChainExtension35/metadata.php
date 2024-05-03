<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension35\MyClass35;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'Vendor1_ModuleChainExtension35',
    'title' => 'Test OXID eShop class module chain extension 3.5',
    'description' => 'The module class and the chain extended OXID eShop class life in their namespaces.',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Article::class => MyClass35::class,
    ],
];
