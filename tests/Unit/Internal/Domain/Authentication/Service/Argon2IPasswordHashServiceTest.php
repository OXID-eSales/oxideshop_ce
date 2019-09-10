<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Authentication\Service;

use Exception;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\Argon2IPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy\PasswordPolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class Argon2IPasswordHashServiceTest
 */
class Argon2IPasswordHashServiceTest extends TestCase
{

    /**
     * Currently, Continuous Integration does not have Argon2I compiled into PHP 7.2. This leads to failing tests
     * due to skipTestIfArgon2IAvailable(). As a fast solution we skip all
     * tests of this class until Argon2I will be available.
     */
    protected function setUp()
    {
        $this->markTestSkipped("Argon2I not available currently on PHP 7.2.");
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Domain\Authentication\Exception\UnavailablePasswordHashException
     */
    public function testConstructorThrowsExceptionIfArgon2INotAvailable()
    {
        $this->skipTestIfArgon2IAvailable();
        $passwordPolicyMock = $this->getPasswordPolicyMock();

        new Argon2IPasswordHashService(
            $passwordPolicyMock,
            1024,
            2,
            2
        );
    }

    public function testHashForGivenPasswordIsEncryptedWithProperAlgorithm()
    {
        $this->skipTestIfArgon2INotAvailable();
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }

    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm()
    {
        $this->skipTestIfArgon2INotAvailable();
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }

    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes()
    {
        $this->skipTestIfArgon2INotAvailable();
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashService();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }

    /**
     * Invalid values as a memory cost value of 2^32 + 1 can cause the method hash to fail.
     */
    public function testHashThrowsExceptionOnInvalidSettings()
    {
        $this->skipTestIfArgon2INotAvailable();

        $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new Argon2IPasswordHashService(
            $passwordPolicyMock,
            1 << 32, // The value 2^32 is out of range and will produce a PHP Warning.
            PASSWORD_ARGON2_DEFAULT_TIME_COST,
            PASSWORD_ARGON2_DEFAULT_THREADS
        );

        $passwordHashService->hash('secret');
    }

    public function testPasswordNeedsRehashReturnsTrueOnChangedAlgorithm()
    {
        $this->skipTestIfArgon2INotAvailable();
        $passwordHashedWithOriginalAlgorithm = password_hash('secret', PASSWORD_BCRYPT);
        $passwordHashService = $this->getPasswordHashService();

        $this->assertTrue(
            $passwordHashService->passwordNeedsRehash($passwordHashedWithOriginalAlgorithm)
        );
    }

    public function testHashWithValidCostOption()
    {
        $this->skipTestIfArgon2INotAvailable();
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('secret');

        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
        $this->assertSame(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => PASSWORD_ARGON2_DEFAULT_THREADS
            ],
            $info['options']
        );
    }

    /**
     * @return PasswordHashServiceInterface
     */
    private function getPasswordHashService(): PasswordHashServiceInterface
    {
        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new Argon2IPasswordHashService(
            $passwordPolicyMock,
            PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            PASSWORD_ARGON2_DEFAULT_TIME_COST,
            PASSWORD_ARGON2_DEFAULT_THREADS
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordPolicyInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordPolicyMock(): PasswordPolicyInterface
    {
        $passwordPolicyMock = $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();

        return $passwordPolicyMock;
    }

    /**
     * PHP > 7.2 is compiled by default with support for ARGON2I. Instead of checking for the constant PASSWORD_ARGON2I,
     * we check for the PHP version in order to run this test and get an alarm if ARGON2I is not compiled into
     * the PHP version on the server.
     */
    private function skipTestIfArgon2INotAvailable()
    {
        if (version_compare(PHP_VERSION, '7.2') < 0) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }
    }

    /**
     * PHP > 7.2 is compiled by default with support for ARGON2I. Instead of checking for the constant PASSWORD_ARGON2I,
     * we check for the PHP version in order to run this test and get an alarm if ARGON2I is not compiled into
     * the PHP version on the server.
     */
    private function skipTestIfArgon2IAvailable()
    {
        if (version_compare(PHP_VERSION, '7.2') >= 0) {
            $this->markTestSkipped('This test can not be executed because the password hashing algorithm "PASSWORD_ARGON2I" is available.');
        }
    }
}
