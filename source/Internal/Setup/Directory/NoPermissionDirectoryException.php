<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory;

use Exception;

class NoPermissionDirectoryException extends Exception
{
    public const NO_PERMISSION_DIRECTORY = 'Following folder has no writing and reading permission';
}
