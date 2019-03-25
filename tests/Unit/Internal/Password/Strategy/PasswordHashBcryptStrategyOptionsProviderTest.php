<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashBcryptStrategyOptionsProvider;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordHashBcryptStrategyOptionsProviderTest
 */
class PasswordHashBcryptStrategyOptionsProviderTest extends TestCase
{

    /**
     * @dataProvider validCostOptionDataProvider
     *
     * @param mixed $validCostOption
     */
    public function testGetOptionsWithValidCostOptionReturnsOptionsArray(int $validCostOption)
    {
        $contextStub = $this->getMockBuilder(ContextStub::class)
            ->setMethods(['getPasswordHashingBcryptCost'])
            ->getMock();

        $contextStub->method('getPasswordHashingBcryptCost')->willReturn($validCostOption);

        $passwordHashBcryptServiceOptionsProvider = new PasswordHashBcryptStrategyOptionsProvider($contextStub);
        $options = $passwordHashBcryptServiceOptionsProvider->getOptions();


        $this->assertSame($validCostOption, $options['cost']);
    }

    /**
     * @return array
     */
    public function validCostOptionDataProvider(): array
    {
        /** The permitted values for the bcrypt cost option range between 4 and 31  */
        $validCostRange = range(4, 31);
        $validCostOptions = [];

        foreach ($validCostRange as $cost) {
            $validCostOptions[] = [$cost];
        }

        return $validCostOptions;
    }

    /**
     * @dataProvider invalidCostOptionDataProvider
     *
     * @param mixed $invalidCostOption
     */
    public function testGetOptionsWithInvalidCostOptionValueThrowsPasswordHashException($invalidCostOption)
    {
        $this->expectException(PasswordHashException::class);

        $contextStub = $this->getMockBuilder(ContextStub::class)
            ->setMethods(['getPasswordHashingBcryptCost'])
            ->getMock();

        $contextStub->method('getPasswordHashingBcryptCost')->willReturn($invalidCostOption);

        $passwordHashBcryptServiceOptionsProvider = new PasswordHashBcryptStrategyOptionsProvider($contextStub);
        $passwordHashBcryptServiceOptionsProvider->getOptions();
    }

    /**
     * @return array
     */
    public function invalidCostOptionDataProvider(): array
    {
        return [
            [-5],
            [0],
            [3], // Cost must not be smaller than 4
            [32], // Cost must not be bigger than 31
        ];
    }
}
