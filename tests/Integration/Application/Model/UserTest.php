<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class UserTest extends TestCase
{
    use ProphecyTrait;
    use ContainerTrait;

    public function testSetPassword(): void
    {
        $password = 'some-pass';
        $user = oxNew(User::class);

        $user->setPassword($password);

        $this->assertTrue(
            $this->get(PasswordServiceBridgeInterface::class)
                ->verifyPassword(
                    $password,
                    $user->getFieldData('oxpassword')
                )
        );
        $this->assertEmpty($user->getFieldData('oxpasssalt'));
    }

    public function testSetPasswordWithEmptyPass(): void
    {
        $user = oxNew(User::class);

        $user->setPassword('');

        $this->assertEmpty($user->getFieldData('oxpassword'));
        $this->assertEmpty($user->getFieldData('oxpasssalt'));
    }

    public function testGetBoniWithDefaultConfig(): void
    {
        $rating = oxNew(User::class)->getBoni();

        $this->assertEquals(1000, $rating);
    }

    public function testGetBoniWithModifiedConfig(): void
    {
        $configValue = 123;
        $this->setParameter('oxid_shop_credit_rating', $configValue);
        $this->setParameter('oxid_build_directory', getenv('OXID_BUILD_DIRECTORY'));
        $this->replaceContainerInstance();

        $rating = oxNew(User::class)->getBoni();

        $this->assertEquals($configValue, $rating);
    }
}
