<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module2\Controller\Test2ContentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module2\Model\Test2Content;

$sMetadataVersion = '2.1';
$aModule = [
    'id' => 'unifiednamespace_module2',
    'title' => [
        'de' => 'OXID eSales example module2',
        'en' => 'OXID eSales example module2',
    ],
    'description' => [
        'de' => 'This module overrides ContentController::getTitle()',
        'en' => 'This module overrides ContentController::getTitle()',
    ],
    'version' => '1.0.0',
    'author' => 'John Doe',
    'url' => 'www.johndoe.com',
    'email' => 'john@doe.com',
    'extend' => [
        'content' => Test2ContentController::class,
        'test2content' => Test2Content::class,
    ],
    'templates' => [],
    'settings' => [],
    'events' => [],
];
