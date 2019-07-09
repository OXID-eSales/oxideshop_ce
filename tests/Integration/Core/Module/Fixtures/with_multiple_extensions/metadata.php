<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'with_multiple_extensions',
    'title'       => 'with multiple extensions',
    'description' => 'Test multiple extensions',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [
        \OxidEsales\Eshop\Application\Model\Article::class => 'with_multiple_extensions/articleExtension1&with_multiple_extensions/articleExtension2&with_multiple_extensions/articleExtension3',
        \OxidEsales\Eshop\Application\Model\Order::class   => 'with_multiple_extensions/oxOrder',
        \OxidEsales\Eshop\Application\Model\Basket::class  => 'with_multiple_extensions/basketExtension',
    ]
);
