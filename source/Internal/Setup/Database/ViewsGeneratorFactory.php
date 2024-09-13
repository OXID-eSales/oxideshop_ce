<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;

class ViewsGeneratorFactory implements ViewsGeneratorFactoryInterface
{
    public function create(): ViewsGenerator
    {
        return new ViewsGenerator();
    }
}
