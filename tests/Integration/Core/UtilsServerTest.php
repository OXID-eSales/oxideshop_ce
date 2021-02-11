<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class UtilsServerTest extends TestCase
{
    use ContainerTrait;

    protected function tearDown(): void
    {
        $utils = oxNew('oxutilsserver');
        if ($utils->getUserCookie()) {
            $utils->deleteUserCookie();
        }
        parent::tearDown();
    }

    public function testGetSetAndDeleteUserCookie(): void
    {
        $utils = oxNew('oxutilsserver');

        $this->assertNull($utils->getUserCookie());

        $utils->setUserCookie('admin', 'admin', null, 31536000, User::USER_COOKIE_SALT);

        $aData = explode('@@@', $utils->getUserCookie());

        $this->assertTrue(
            $this->get(PasswordServiceBridgeInterface::class)->verifyPassword('admin' . User::USER_COOKIE_SALT, $aData[1])
        );

        $utils->deleteUserCookie();
        $this->assertNull($utils->getUserCookie());
    }
}
