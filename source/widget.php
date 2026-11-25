<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunnerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/bootstrap.php';

ContainerFacade::get(ShopRunnerInterface::class)->runController(
    ContainerFacade::get(Request::class),
    static fn(): Response => oxNew(WidgetControl::class)->handleRequest()
);
