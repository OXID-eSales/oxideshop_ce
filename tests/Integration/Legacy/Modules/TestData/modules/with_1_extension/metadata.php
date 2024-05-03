<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

public function setUp
$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'with_1_extension',
    'title' => 'Test extending 1 shop classes',
    'description' => 'Module testing extending 3 shop classes',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        'oxarticle' => 'with_1_extension/mybaseclass',
    ],
];
