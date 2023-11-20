<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Env;

interface EnvLoaderInterface
{
    public function loadEnvironmentVariables(): void;
}
