<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

/**
 * @deprecated
 */

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunnerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__ . '/bootstrap.php';

ContainerFacade::get(ShopRunnerInterface::class)->run(
    static fn(): Response => throw new NotFoundHttpException()
);
