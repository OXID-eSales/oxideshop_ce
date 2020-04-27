<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception;

use Exception;
use Throwable;

/**
 * Class DirectoryException
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Directory
 */
class DirectoryException extends Exception
{
    public const NON_EXISTENCE_DIRECTORY = 'Non existence directory';
    public const NO_PERMISSION_DIRECTORY = 'No permission directory';

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
