<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;

$sMetadataVersion = '2.1';
$aModule = [
    'id' => 'with_multiple_extensions',
    'title' => 'with multiple extensions',
    'description' => 'Test multiple extensions',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Article::class => 'with_multiple_extensions/articleExtension1&with_multiple_extensions/articleExtension2&with_multiple_extensions/articleExtension3',
        Order::class => 'with_multiple_extensions/oxOrder',
        Basket::class => 'with_multiple_extensions/basketExtension',
    ],
];
