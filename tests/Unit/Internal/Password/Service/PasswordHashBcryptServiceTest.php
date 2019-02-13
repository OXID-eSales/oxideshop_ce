<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptServiceOptionsProvider;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class PasswordHashBcryptServiceTest extends TestCase
{
    /**
     *
     */
    public function testHashForGivenPasswordIsEncryptedWithBcrypt()
    {
        $password = 'secret';
        $contextStub = new ContextStub();
        $passwordHashService = $this->getPasswordHashService($contextStub);
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     * @param ContextStub $contextStub
     *
     * @return PasswordHashBcryptService
     */
    private function getPasswordHashService(ContextStub $contextStub): PasswordHashBcryptService
    {
        $passwordHashBcryptServiceOptionProvider = new PasswordHashBcryptServiceOptionsProvider($contextStub);
        $passwordHashService = new PasswordHashBcryptService($passwordHashBcryptServiceOptionProvider);

        return $passwordHashService;
    }

    /**
     *
     */
    public function testHashForEmptyPasswordIsEncryptedWithBcrypt()
    {
        $password = '';

        $contextStub = new ContextStub();
        $passwordHashService = $this->getPasswordHashService($contextStub);
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     *
     */
    public function testHashWithDefaultContextStubOptions()
    {
        $password = 'secret';

        $contextStub = new ContextStub();
        $passwordHashService = $this->getPasswordHashService($contextStub);
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(4, $info['options']['cost']);
    }

    /**
     * @dataProvider invalidCostOptionDataProvider
     *
     * @param mixed $invalidCostOption
     */
    public function testHashWithInvalidCostOptionValueThrowsPasswordHashException($invalidCostOption)
    {
        $this->expectException(PasswordHashException::class);

        $password = 'secret';

        $contextStub = $this->getMockBuilder(ContextStub::class)
            ->setMethods(['getPasswordHashingBcryptCost'])
            ->getMock();

        $contextStub->method('getPasswordHashingBcryptCost')->willReturn($invalidCostOption);

        $passwordHashService = $this->getPasswordHashService($contextStub);
        $passwordHashService->hash($password);
    }

    /**
     * @return array
     */
    public function invalidCostOptionDataProvider(): array
    {
        return [
            [-5],
            [0],
            [3], // Cost must be at least 4
        ];
    }

    /**
     *
     */
    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes()
    {
        $password = 'secret';

        $contextStub = new ContextStub();
        $passwordHashService = $this->getPasswordHashService($contextStub);
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }
}
