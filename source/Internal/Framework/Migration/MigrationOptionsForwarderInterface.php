<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

interface MigrationOptionsForwarderInterface
{
    public function mirror(InputDefinition $definition): void;

    /**
     * @return array<string, mixed>
     */
    public function collect(InputInterface $input): array;
}
