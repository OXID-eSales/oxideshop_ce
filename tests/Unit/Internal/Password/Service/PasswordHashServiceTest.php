<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Policy\PasswordPolicyInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashService;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashArgon2IdStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashArgon2IStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashBcryptStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyOptionsProviderInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PasswordHashServiceTest extends TestCase
{
    /** @var int For test choose the minimal possible cost */
    const BCRYPT_COST = 4;
    const ARGON2_TIME_COST = 1;
    const ARGON2_MEMORY_COST = 512;
    const ARGON2_THREADS = 1;

    /**
     */
    public function testHashThrowsExceptionOnNonSupportedAlgorithm()
    {
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy::class);

        $password = 'secret';
        $algorithm = '1234';
        $passwordHashService = new PasswordHashService();

        $passwordHashService->hash($password, $algorithm);
    }

    /**
     */
    public function testHashChoosesProperStrategyForBcrypt()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }
        $password = 'secret';
        $algorithm = 'PASSWORD_BCRYPT';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyBcrypt());

        $hash = $passwordHashService->hash($password, $algorithm);

        $this->assertEquals(password_get_info($hash)['algo'], PASSWORD_BCRYPT);
    }

    /**
     */
    public function testHashChoosesProperStrategyForArgon2i()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }
        $password = 'secret';
        $algorithm = 'PASSWORD_ARGON2I';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyArgon2i());

        $hash = $passwordHashService->hash($password, $algorithm);

        $this->assertEquals(password_get_info($hash)['algo'], PASSWORD_ARGON2I);
    }

    /**
     */
    public function testHashChoosesProperStrategyForArgon2id()
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }
        $password = 'secret';
        $algorithm = 'PASSWORD_ARGON2ID';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyArgon2id());

        $hash = $passwordHashService->hash($password, $algorithm);

        $this->assertEquals(password_get_info($hash)['algo'], PASSWORD_ARGON2ID);
    }

    /**
     */
    public function testPasswordNeedsRehashThrowsExceptionOnNonSupportedAlgorithm()
    {
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy::class);

        $passwordHash = 'passwordHash';
        $algorithm = '1234';
        $passwordHashService = new PasswordHashService();

        $passwordHashService->passwordNeedsRehash($passwordHash, $algorithm);
    }

    /**
     */
    public function testPasswordNeedsRehashReturnsTrueOnChangedParameters()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }

        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST + 1]);
        $algorithm = 'PASSWORD_BCRYPT';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyBcrypt());

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash, $algorithm));
    }

    /**
     */
    public function testPasswordNeedsRehashReturnsTrueOnUnknownHash()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }

        $passwordHash = 'some_unrecognizable_custom_hash';
        $algorithm = 'PASSWORD_BCRYPT';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyBcrypt());

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash, $algorithm));
    }

    /**
     */
    public function testPasswordNeedsRehashReturnsTrueOnChangedAlgorithm()
    {
        $originalAlgorithm = 'PASSWORD_BCRYPT';
        $newAlgorithm = 'PASSWORD_ARGON2I';
        if (!defined($originalAlgorithm) || !defined($newAlgorithm)) {
            $this->markTestSkipped(
                'The password hashing algorithms "' . $originalAlgorithm . '" and/or "' . $newAlgorithm . '" are not available'
            );
        }
        $originalAlgorithmConstantValue = PASSWORD_BCRYPT;

        $passwordHashedWithOriginalAlgorithm = password_hash('secret', $originalAlgorithmConstantValue);

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($originalAlgorithm, $this->getPasswordHashStrategyBcrypt());
        $passwordHashService->addPasswordHashStrategy($newAlgorithm, $this->getPasswordHashStrategyArgon2i());

        $this->assertTrue(
            $passwordHashService->passwordNeedsRehash(
                $passwordHashedWithOriginalAlgorithm,
                $newAlgorithm
            )
        );
    }

    /**
     */
    public function testPasswordNeedsRehashReturnsFalseOnSameAlgorithmAndOptions()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }

        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST]);
        $algorithm = 'PASSWORD_BCRYPT';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyBcrypt());

        $this->assertFalse($passwordHashService->passwordNeedsRehash($passwordHash, $algorithm));
    }

    /**
     */
    public function testPasswordNeedsRehashChoosesProperStrategyForArgon2id()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }

        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST + 1]);
        $algorithm = 'PASSWORD_BCRYPT';

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy($algorithm, $this->getPasswordHashStrategyBcrypt());

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash, $algorithm));
    }

    /**
     *
     */
    public function testAddPasswordHashStrategyThrowsTypeErrorOnInvalidParameter()
    {
        $this->expectException(\TypeError::class);
        $algorithm = 'PASSWORD_BCRYPT';
        $objectDoesNotImplementPasswordHashStrategyInterface = new \StdClass();

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy(
            $algorithm,
            $objectDoesNotImplementPasswordHashStrategyInterface
        );
    }

    /**
     *
     */
    public function testAddPasswordHashStrategyAcceptsCustomPasswordStrategyDescription()
    {
        $algorithm = 'CUSTOM_PASSWORD_STRATEGY_DESCRIPTION';
        $passwordHashStrategyInterfaceImplementation = $this
            ->getMockBuilder(PasswordHashStrategyInterface::class)
            ->getMock();

        $passwordHashService = new PasswordHashService();
        $passwordHashService->addPasswordHashStrategy(
            $algorithm,
            $passwordHashStrategyInterfaceImplementation
        );
    }

    /**
     * @return PasswordHashBcryptStrategy
     */
    private function getPasswordHashStrategyBcrypt(): PasswordHashBcryptStrategy
    {
        $passwordHashStrategyOptionsProviderMock = $this->getPasswordHashStrategyOptionsProviderMock();
        $passwordHashStrategyOptionsProviderMock->method('getOptions')->willReturn(['cost' => self::BCRYPT_COST]);

        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new PasswordHashBcryptStrategy(
            $passwordHashStrategyOptionsProviderMock,
            $passwordPolicyMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashArgon2IStrategy
     */
    private function getPasswordHashStrategyArgon2i(): PasswordHashArgon2IStrategy
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashStrategyOptionsProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => self::ARGON2_MEMORY_COST,
                'time_cost'   => self::ARGON2_TIME_COST,
                'threads'     => self::ARGON2_THREADS
            ]
        );

        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new PasswordHashArgon2IStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashArgon2IdStrategy
     */
    private function getPasswordHashStrategyArgon2id(): PasswordHashArgon2IdStrategy
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashStrategyOptionsProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new PasswordHashArgon2IdStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashStrategyOptionsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordHashStrategyOptionsProviderMock()
    {
        $passwordHashServiceOptionProviderMock = $this
            ->getMockBuilder(PasswordHashStrategyOptionsProviderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOptions'])
            ->getMock();

        return $passwordHashServiceOptionProviderMock;
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
}
