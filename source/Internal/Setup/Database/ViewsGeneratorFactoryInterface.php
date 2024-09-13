<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;

interface ViewsGeneratorFactoryInterface
{
    public function create(): ViewsGenerator;
}
