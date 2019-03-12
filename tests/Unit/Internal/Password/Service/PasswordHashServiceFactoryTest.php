<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Core\Hasher;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PasswordHashServiceFactoryTest extends TestCase
{
    /**
     */
    public function testGetPasswordHashServiceThrowsExceptionOnNonSupportedAlgorithm()
    {
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashAlgorithm::class);

        $algorithm = 1234;
        $factory = new PasswordHashServiceFactory();

        $factory->getPasswordHashService($algorithm);
    }

    /**
     */
    public function testGetPasswordHashServiceReturnsInstanceOfBcryptService()
    {
        $algorithm = PASSWORD_BCRYPT;
        $factory = new PasswordHashServiceFactory();

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashBcryptService::class, $service);
    }
}
