<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Article;

$sMetadataVersion = '2.1';
$aModule = [
    'id' => 'with_class_extensions_cleaner',
    'title' => 'Smarty plugin directoies',
    'description' => 'Test defining smarty plugin directoies',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Article::class => 'with_class_extensions_cleaner/ModuleArticle',
    ],
];
