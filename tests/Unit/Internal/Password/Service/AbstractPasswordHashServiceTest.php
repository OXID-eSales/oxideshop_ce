<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractPasswordHashServiceTest
 */
abstract class AbstractPasswordHashServiceTest extends TestCase
{
    /**
     * @var
     */
    protected $hashingAlgorithm;

    /**
     *
     */
    public function testHashForGivenPasswordIsEncryptedWithProperAlgorithm()
    {
        $password = 'secret';
        $passwordHashService = $this->getPasswordHashServiceImplementation();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame($this->hashingAlgorithm, $info['algo']);
    }

    /**
     * @return PasswordHashServiceInterface
     */
    abstract protected function getPasswordHashServiceImplementation(): PasswordHashServiceInterface;

    /**
     *
     */
    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm()
    {
        $password = '';

        $passwordHashService = $this->getPasswordHashServiceImplementation();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame($this->hashingAlgorithm, $info['algo']);
    }

    /**
     *
     */
    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes()
    {
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashServiceImplementation();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }
}
