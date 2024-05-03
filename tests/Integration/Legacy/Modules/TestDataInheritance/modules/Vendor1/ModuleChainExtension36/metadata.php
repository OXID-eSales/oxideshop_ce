<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension36\MyClass36;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'Vendor1_ModuleChainExtension36',
    'title' => 'Test OXID eShop class module chain extension 3.6',
    'description' => 'The module class and the chain extended OXID eShop class life in their namespaces. The OXID eShop class is from the unified namespace.',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Article::class => MyClass36::class,
    ],
];
