<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Service;

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
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException::class);

        $algorithm = 'non-existent';
        $factory = new PasswordHashServiceFactory();

        $factory->getPasswordHashService($algorithm);
    }

    /**
     */
    public function testGetPasswordHashServiceReturnsInstanceOfSha512Service()
    {
        $algorithm = PasswordHashServiceFactory::ALGORITHM_SHA_512;
        $factory = new PasswordHashServiceFactory();

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(Hasher::class, $service);
    }

    /**
     */
    public function testGetPasswordHashServiceReturnsInstanceOfBcryptService()
    {
        $algorithm = PasswordHashServiceFactory::ALGORITHM_BCRYPT;
        $options = [];
        $factory = new PasswordHashServiceFactory();

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashBcryptService::class, $service);
    }
}
