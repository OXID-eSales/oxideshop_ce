<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory;

use Exception;

class NonExistenceDirectoryException extends Exception
{
    public const NON_EXISTENCE_DIRECTORY = 'Following folder does not exist';
}
