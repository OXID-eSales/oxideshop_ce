<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRequestRunner;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/bootstrap.php';

ContainerFacade::get(ShopRequestRunner::class)->run(
    static fn(): Response => oxNew(WidgetControl::class)->buildWidgetResponse()
);
