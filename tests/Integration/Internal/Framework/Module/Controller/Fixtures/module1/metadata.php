<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller\Fixtures\module1\src\Controller\ModuleController;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller\Fixtures\module1\src\Controller\ModuleControllerMissingTemplate;

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'module1',
    'controllers' => [
        'module1_controller' => ModuleController::class,
    ],
];
