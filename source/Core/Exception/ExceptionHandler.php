<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Exception;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use Throwable;
use function oxTriggerOfflinePageDisplay;

class ExceptionHandler
{
    public function __construct(private readonly bool $isDebugMode = false)
    {
    }

    /**
     * @throws Throwable
     */
    public function handleUncaughtException(Throwable $exception): void
    {
        try {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        } catch (Throwable) {
            (new LoggerServiceFactory(new Context(ShopIdCalculator::BASE_SHOP_ID)))
                ->getLogger()
                ->error($exception);
        }
        if ($this->isDebugMode || PHP_SAPI === 'cli') {
            throw $exception;
        }
        oxTriggerOfflinePageDisplay();
        exit(1);
    }
}
