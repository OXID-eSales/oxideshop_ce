<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Exception;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionHandler
{
    public function __construct(private readonly bool $debugMode = false)
    {
    }

    /**
     * @throws Throwable
     */
    public function handleUncaughtException(Throwable $exception): void
    {
        $this->logException($exception);

        if ($this->debugMode || PHP_SAPI === 'cli') {
            throw $exception;
        }

        $response = new OfflinePageResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->headers->set('Connection', 'close');
        $response->send();
        exit(1);
    }

    private function logException(Throwable $exception): void
    {
        try {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        } catch (Throwable) {
            (new LoggerServiceFactory(new Context(ShopIdCalculator::BASE_SHOP_ID)))
                ->getLogger()
                ->error($exception);
        }
    }
}
