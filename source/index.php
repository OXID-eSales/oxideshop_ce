<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunnerInterface;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/bootstrap.php';

ContainerFacade::get(ShopRunnerInterface::class)->run(
    static fn(): Response => oxNew(ShopControl::class)->buildResponse()
);
