<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\without_own_module_namespace\Application\Controller\TestModuleTwoPaymentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\without_own_module_namespace\Application\Model\TestModuleTwoPrice;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'without_own_module_namespace',
    'title' => 'OXID eShop not namespaced test module',
    'description' => 'Double the price. Show payment error message during checkout.',
    'thumbnail' => 'module.png',
    'version' => '1.0.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        'payment' => TestModuleTwoPaymentController::class,
        'oxprice' => TestModuleTwoPrice::class,
    ],
    'settings' => [],
];
