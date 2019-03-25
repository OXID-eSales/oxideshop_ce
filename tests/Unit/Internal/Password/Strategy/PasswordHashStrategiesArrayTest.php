<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategiesArray;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordHashStrategiesArrayTest
 */
class PasswordHashStrategiesArrayTest extends TestCase
{
    /**
     *
     */
    public function testPasswordHashStrategiesArrayThrowExceptionOnWrongElement()
    {
        $this->expectException(\RuntimeException::class);
        $passwordHashStrategiesArray = new PasswordHashStrategiesArray();

        $passwordHashStrategiesArray['description'] = 'value';
    }

    /**
     *
     */
    public function testPasswordHashStrategiesArrayThrowExceptionOnWrongKey()
    {
        $this->expectException(\RuntimeException::class);
        $passwordHashStrategiesArray = new PasswordHashStrategiesArray();

        $passwordHashStrategyInterfaceImplementation = $this
            ->getMockBuilder(PasswordHashStrategyInterface::class)
            ->getMock();

        $passwordHashStrategiesArray[] = $passwordHashStrategyInterfaceImplementation;
    }

    /**
     *
     */
    public function testPasswordHashStrategiesArrayThrowsExceptionOnAccessingNonExistingKey()
    {
        $this->expectException(\RuntimeException::class);

        $passwordHashStrategiesArray = new PasswordHashStrategiesArray();
        $passwordHashStrategyInterfaceImplementation = $this
            ->getMockBuilder(PasswordHashStrategyInterface::class)
            ->getMock();

        $passwordHashStrategiesArray['custom_password_hash_strategy'] = $passwordHashStrategyInterfaceImplementation;

        $this->assertNotNull($passwordHashStrategiesArray['custom_password_hash_strategy']);

        unset($passwordHashStrategiesArray['custom_password_hash_strategy']);

        $strategy = $passwordHashStrategiesArray['custom_password_hash_strategy'];
    }
}
