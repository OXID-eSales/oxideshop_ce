<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Parameters;

use Symfony\Component\Console\Input\InputInterface;

interface SetupParametersFactoryInterface
{
    public function create(InputInterface $input): SetupParameters;
}
