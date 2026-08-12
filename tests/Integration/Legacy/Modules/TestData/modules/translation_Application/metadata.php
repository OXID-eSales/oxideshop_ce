<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Article;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'translation_Application',
    'title' => 'Translations in Application folder',
    'description' => 'In this module the translations lay in the Application folder.',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        // This one is needed, cause if the module is not extending anything, we don't search for the translations!
        Article::class => 'translation_Application/myarticle',
    ],
];
