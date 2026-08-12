<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\EshopCommunity\Application\Controller\FrontendController;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'extending_1_class',
    'title' => 'Test extending 1 shop class',
    'description' => 'Module testing extending 1 shop class',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Order::class => 'oeTest/extending_1_class/myorder',
    ],
    'controllers' => [
        FrontendController::class => 'oeTest/controller_1_class/myFrontendController',
    ],
];
