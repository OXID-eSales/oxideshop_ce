<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber;
use PHPUnit\Framework\TestCase;

final class DIServiceWrapperTest extends TestCase
{
    public function testGenerateServicesWithNoArgumentsButExistingShopAwareClass()
    {
        $service = new DIServiceWrapper(TestEventSubscriber::class, []);
        $this->assertTrue($service->isShopAware());
    }
}
