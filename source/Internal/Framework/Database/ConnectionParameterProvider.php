<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

readonly class ConnectionParameterProvider implements ConnectionParameterProviderInterface
{
    public function __construct(
        private BasicContextInterface $basicContext,
    ) {
    }

    public function getParameters(): array
    {
        return (new DatabaseConfiguration($this->basicContext->getDatabaseUrl()))->getConnectionParameters();
    }
}
