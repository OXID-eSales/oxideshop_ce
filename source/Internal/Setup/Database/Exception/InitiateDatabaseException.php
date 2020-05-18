<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Exception;

use Exception;
use Throwable;

class InitiateDatabaseException extends Exception
{
    public const EXECUTE_MIGRATIONS_PROBLEM = 'Failed: Could not execute migrations';
    public const READ_SQL_FILE_PROBLEM = 'Failed: SQL file can not be read';
}
