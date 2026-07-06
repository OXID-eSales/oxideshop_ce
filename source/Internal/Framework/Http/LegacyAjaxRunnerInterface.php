<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

/**
 * @deprecated will be removed once admin ajax requests are served by Symfony routes
 */
interface LegacyAjaxRunnerInterface
{
    public function runController(callable $controller): void;
}
