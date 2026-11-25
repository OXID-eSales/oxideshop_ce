<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Exception;

use Error;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ShopOfflineException extends Error implements RequestTerminationInterface, HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return Response::HTTP_SERVICE_UNAVAILABLE;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
