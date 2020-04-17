<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Exception;

use function sprintf;

final class PathNotFoundException extends \Exception
{
    public static function byPath(string $path): self
    {
        return new self(sprintf('Path %s does not exist', $path));
    }
}
