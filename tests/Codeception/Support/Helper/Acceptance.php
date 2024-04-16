<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Support\Helper;

use Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
final class Acceptance extends Module
{
    public function getCurrentURL(): string
    {
        return $this->getModule('WebDriver')->webDriver->getCurrentURL();
    }
}
