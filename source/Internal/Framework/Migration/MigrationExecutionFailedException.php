<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use RuntimeException;

class MigrationExecutionFailedException extends RuntimeException
{
    public function __construct(int $exitCode)
    {
        parent::__construct('Could not execute database migrations', $exitCode);
    }
}
