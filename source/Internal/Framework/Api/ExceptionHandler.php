<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Api;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler
{
    public function handle(\Throwable $throwable): void
    {
        (new LoggerServiceFactory(new Context()))
            ->getLogger()
            ->error($throwable);

        $error = getenv('OXID_DEBUG_MODE') ? $throwable->getMessage() : 'An error occurred';

        (new JsonResponse(['error' => $error], Response::HTTP_INTERNAL_SERVER_ERROR))->send();
    }
}
