<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\User;

use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class LoginTest extends IntegrationTestCase
{
    use UserFixtureTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->setShopUserLoginRequestData();
    }

    public function testLogin(): void
    {
        $hash = $this->getHash();
        $user = $this->addUserFixture($hash);

        $login = oxNew(UserComponent::class)->login();

        $this->assertEquals('payment', $login);
        $this->assertEquals($user->getId(), Registry::getSession()->getVariable('usr'));
        $this->assertEquals($hash, $user->getFieldData('oxpassword'));
        $this->assertEquals($this->getSalt(), $user->getFieldData('oxpasssalt'));
    }

    public function testLoginWithLegacyHashing(): void
    {
        $hash = $this->getLegacyHash();
        $user = $this->addUserFixture($hash);

        $login = oxNew(UserComponent::class)->login();

        $this->assertEquals('payment', $login);
        $this->assertEquals($user->getId(), Registry::getSession()->getVariable('usr'),);
        $this->assertEquals($hash, $user->getFieldData('oxpassword'));
        $this->assertEquals($this->getSalt(), $user->getFieldData('oxpasssalt'));
    }

    public function testLoadUserLoggedInWithLegacyHashingWillUpdatePasswordHashAndSetEmptySalt(): void
    {
        $hash = $this->getLegacyHash();
        $user = $this->addUserFixture($hash);
        oxNew(UserComponent::class)->login();

        $user->load($user->getId());

        $this->assertNotEmpty($user->getFieldData('oxpassword'));
        $this->assertNotEquals($hash, $user->getFieldData('oxpassword'));
        $this->assertEmpty($user->getFieldData('oxpasssalt'));
    }

    public function testLoginFail(): void
    {
        $hash = $this->getHash();
        $user = $this->addUserFixture($hash);
        $_POST['lgn_pwd'] = uniqid('wrong-pass-', true);

        oxNew(UserComponent::class)->login();

        $this->assertNull(Registry::getSession()->getVariable('usr'));
        $this->assertEquals($hash, $user->getFieldData('oxpassword'));
        $this->assertEquals($this->getSalt(), $user->getFieldData('oxpasssalt'));
    }

    public function testLoadUserAfterLoginFailWithLegacyHashingWillNotUpdateHashAndSalt(): void
    {
        $hash = $this->getLegacyHash();
        $user = $this->addUserFixture($hash);
        $_POST['lgn_pwd'] = uniqid('wrong-pass-', true);
        oxNew(UserComponent::class)->login();

        $user->load($user->getId());

        $this->assertNull(Registry::getSession()->getVariable('usr'));
        $this->assertEquals($hash, $user->getFieldData('oxpassword'));
        $this->assertEquals($this->getSalt(), $user->getFieldData('oxpasssalt'));
    }
}
