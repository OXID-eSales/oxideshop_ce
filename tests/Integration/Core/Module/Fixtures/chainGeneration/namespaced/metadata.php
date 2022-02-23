<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

$sMetadataVersion = '2.1';
$aModule = [
    'id' => 'chainGeneration/namespaced',
    'title' => 'Test module',
    'description' => 'Module with namespaces',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        \OxidEsales\Eshop\Application\Model\Article::class => 'chainGeneration\namespaced\Model\Product',
    ],
];
