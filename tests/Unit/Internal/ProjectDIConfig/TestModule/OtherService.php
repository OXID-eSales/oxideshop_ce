<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule;

use OxidEsales\EshopCommunity\Internal\Application\Events\AbstractShopAwareEventSubscriber;
use Symfony\Component\EventDispatcher\Event;

/**
 * @internal
 */
class OtherService
{
    public function doSomething()
    {
        return null;
    }

}
