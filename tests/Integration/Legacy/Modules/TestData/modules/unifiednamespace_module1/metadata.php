<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module1\Controller\Test1ContentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module1\Model\Module1TestContent;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'unifiednamespace_module1',
    'title' => [
        'de' => 'OXID eSales example module 1',
        'en' => 'OXID eSales example module 1',
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
        'content' => Test1ContentController::class,
        'test1content' => Module1TestContent::class,
    ],
    'controllers' => [],
    'templates' => [],
    'settings' => [],
    'events' => [],
];
