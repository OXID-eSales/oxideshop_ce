<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractPasswordHashStrategyTest
 */
abstract class AbstractPasswordHashStrategyTest extends TestCase
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
        $passwordHashService = $this->getPasswordHashStrategyImplementation();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame($this->hashingAlgorithm, $info['algo']);
    }

    /**
     * @return PasswordHashStrategyInterface
     */
    abstract protected function getPasswordHashStrategyImplementation(): PasswordHashStrategyInterface;

    /**
     *
     */
    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm()
    {
        $password = '';

        $passwordHashService = $this->getPasswordHashStrategyImplementation();
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

        $passwordHashService = $this->getPasswordHashStrategyImplementation();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }
}
