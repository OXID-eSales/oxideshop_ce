<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use Codeception\TestModule\Problems\Model\NonExistentFile;
use OxidEsales\Eshop\Application\Model\Article;

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'codeception_test-module-problems',
    'title' => 'Module with problems (Namespaced)',
    'description' => 'Test module validation for modules, which use namespaces',
    'thumbnail' => '',
    'version' => '1.0',
    'author' => 'OXID',
    'extend' => [
        /** The class file does not exist at all and thus the class cannot be loaded */
        Article::class => NonExistentFile::class
    ],
];
