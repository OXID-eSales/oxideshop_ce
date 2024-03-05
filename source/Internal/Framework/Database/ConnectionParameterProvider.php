<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class ConnectionParameterProvider implements ConnectionParameterProviderInterface
{
    public function __construct(
        private readonly BasicContextInterface $basicContext,
    ) {
    }

    public function getParameters(): array
    {
        return ['url' => $this->basicContext->getDatabaseUrl()];
    }
}
