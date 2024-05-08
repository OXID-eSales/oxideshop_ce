<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'with_events',
    'title' => 'Test module with onActivate and onDeactivate events',
    'description' => 'Module testing with onActivate and onDeactivate events',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'events' => [
        'onActivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_events\Event\MyEvents::onActivate',
        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_events\Event\MyEvents::onDeactivate',
    ],
];
