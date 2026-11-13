<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\controller_module\src\GreetingController;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\controller_module\src\StartController;

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'controller_module',
    'extend' => [
        \OxidEsales\Eshop\Application\Controller\StartController::class => StartController::class,
    ],
    'controllers' => [
        'controller_module_greeting' => GreetingController::class,
        'controller_module_start' => StartController::class,
    ],
];
