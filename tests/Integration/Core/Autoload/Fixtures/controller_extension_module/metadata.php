<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\controller_extension_module\src\GreetingController;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\controller_module\src\GreetingController as ExtendedGreetingController;

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'controller_extension_module',
    'extend' => [
        ExtendedGreetingController::class => GreetingController::class,
    ],
];
