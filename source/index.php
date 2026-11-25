<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunnerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/bootstrap.php';

$shopRunner = ContainerFacade::get(ShopRunnerInterface::class);
$request = ContainerFacade::get(Request::class);
$shopController = static fn(): Response => oxNew(ShopControl::class)->handleRequest();

if (isAdmin()) {
    $shopRunner->runController($request, $shopController);
} else {
    $shopRunner->run($request, $shopController);
}
