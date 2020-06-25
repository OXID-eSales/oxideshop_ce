<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$sMetadataVersion = '1.1';

$aModule = [
    'id' => 'codeception/test-module-problems',
    'title' => 'Module with problems (Namespaced)',
    'description' => 'Test module validation for modules, which use namespaces',
    'thumbnail' => '',
    'version' => '1.0',
    'author' => 'OXID',
    'extend' => [
        /** The class file does not exist at all and thus the class cannot be loaded */
        \OxidEsales\Eshop\Application\Model\Article::class => \Codeception\TestModule\Problems\Model\NonExistentFile::class
    ],
];
