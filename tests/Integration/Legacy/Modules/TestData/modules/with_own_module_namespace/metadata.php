<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'with_own_module_namespace',
    'title' => 'OXID eShop namespaced test module',
    'description' => 'Double the price. Show payment error message during checkout.',
    'thumbnail' => 'module.png',
    'version' => '1.0.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        PaymentController::class => TestModuleOnePaymentController::class,
        Price::class => TestModuleOnePrice::class,
    ],
    'settings' => [],
];
