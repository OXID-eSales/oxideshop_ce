<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception;

use function sprintf;

class InvalidRightsException extends \Exception
{
    public function __construct(string $right)
    {
        parent::__construct(sprintf('Provided right %s is not a valid shop right.', $right));
    }
}
