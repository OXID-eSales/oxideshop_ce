<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception;

use function sprintf;

class InvalidShopException extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Provided shopId %d is not a valid shop id.', $id));
    }
}
