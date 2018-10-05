<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application\Events;


use OxidEsales\Eshop\Core\Config;

class TestConfig extends Config
{
    public function getConfigParam($key, $default=null)
    {
        if ($key == 'sShopDir') {
            return __DIR__;
        }

        if ($key == 'sCompileDir') {
            return __DIR__;
        }

        throw new \Exception('Unknown key ' . $key);
    }

}