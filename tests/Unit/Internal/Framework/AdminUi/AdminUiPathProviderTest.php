<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\AdminUi;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\AdminUiPathProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;

final class AdminUiPathProviderTest extends TestCase
{
    public function testGetAbsolutePathJoinsVendorPathAndPackageDirectory(): void
    {
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getVendorPath')->willReturn('/shop/vendor');

        $provider = new AdminUiPathProvider($context, 'oxid-esales/twig-admin-theme');

        $this->assertSame('/shop/vendor/oxid-esales/twig-admin-theme', $provider->getAbsolutePath());
    }
}
