<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\GenericImport\Fixtures;

use Exception;
use OxidEsales\Eshop\Core\GenericImport\GenericImport;

class FailingImport extends GenericImport
{
    public function importData($data)
    {
        throw new Exception('import failed');
    }
}
