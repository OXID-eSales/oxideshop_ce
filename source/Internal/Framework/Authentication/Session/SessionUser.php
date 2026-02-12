<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
readonly class SessionUser
{
    /** @param string[] $roles */
    public function __construct(
        public array $roles = [],
    ) {
    }
}
