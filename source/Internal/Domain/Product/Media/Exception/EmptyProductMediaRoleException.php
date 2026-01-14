<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Exception;

class EmptyProductMediaRoleException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('ProductMediaRole must not be empty');
    }
}
