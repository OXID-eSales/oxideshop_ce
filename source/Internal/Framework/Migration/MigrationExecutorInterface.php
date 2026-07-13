<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

/**
 * @deprecated since v7.6.0, will be removed in v8.0, use
 *             ConfigurableMigrationExecutorInterface::executeWithOptions() instead
 */
interface MigrationExecutorInterface
{
    public function execute(): void;
}
