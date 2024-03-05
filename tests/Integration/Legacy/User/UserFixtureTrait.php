<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\User;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;

trait UserFixtureTrait
{
    private string $salt;
    private string $username = '_testUserName@oxid-esales.com';
    private string $pass = '_testPassword';

    private function addUserFixture(string $hash): User
    {
        $user = oxNew(User::class);
        $user->oxuser__oxusername = new Field('_testUserName@oxid-esales.com', Field::T_RAW);
        $user->oxuser__oxpassword = new Field($hash, Field::T_RAW);
        $user->oxuser__oxpasssalt = new Field($this->getSalt(), Field::T_RAW);
        $user->save();

        return $user;
    }

    private function getSalt(): string
    {
        if (!isset($this->salt)) {
            $this->salt = uniqid('salt-', true);
        }
        return $this->salt;
    }

    private function getLegacyHash(): string
    {
        return oxNew(User::class)->encodePassword($this->pass, $this->getSalt());
    }

    private function getHash(): string
    {
        return $this->get(PasswordServiceBridgeInterface::class)->hash($this->pass);
    }

    private function setShopUserLoginRequestData(): void
    {
        $_POST['lgn_usr'] = $this->username;
        $_POST['lgn_pwd'] = $this->pass;
    }
}
