<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension44\MyClass44;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'Vendor2_ModuleChainExtension44',
    'title' => 'Test OXID eShop class module chain extension 4.4',
    'description' => 'The module class and the chain extended OXID eShop class life in their namespaces.',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        MyClass44::class
        => \OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension44\MyClass44::class,
    ],
];
