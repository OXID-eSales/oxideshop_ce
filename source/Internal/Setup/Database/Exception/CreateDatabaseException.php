<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Exception;

use Exception;
use Throwable;

/**
 * Class CreateDatabaseException
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Database
 */
class CreateDatabaseException extends Exception
{

    public const CONNECTION_PROBLEM = 'Failed: Unable to connect to database';
    public const CREATION_DATABASE_PROBLEM = 'Failed: Could not create database';
    public const DATABASE_IS_EXIST = 'Failed: Database is already exist';

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
