<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use PHPUnit\Framework\TestCase;

final class AdminControllerTest extends TestCase
{
    public function testInitWithoutAuthorizationRedirectsToLogin(): void
    {
        $this->expectException(RedirectException::class);

        oxNew(AdminController::class)->init();
    }
}
