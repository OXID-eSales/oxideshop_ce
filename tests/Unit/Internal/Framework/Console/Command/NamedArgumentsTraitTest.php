<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Console\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Console\Command\NamedArgumentsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class NamedArgumentsTraitTest extends TestCase
{
    /** @var NamedArgumentsTrait|MockObject */
    private $namedArgumentsTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->namedArgumentsTrait = $this->getMockForTrait(NamedArgumentsTrait::class);
    }

    /** @doesNotPerformAssertions */
    public function testValidateRequiredOptionsWithEmptyList(): void
    {
        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [],
            $this->createMock(InputInterface::class)
        );
    }

    /** @doesNotPerformAssertions */
    public function testValidateRequiredOptionsWithNonRequiredEmptyValue(): void
    {
        $optionName = 'abc';

        $option = $this->createMock(InputOption::class);
        $option->method('getName')
            ->willReturn($optionName);
        $option->method('isValueRequired')
            ->willReturn(false);

        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')
            ->with($optionName)
            ->willReturn(null);

        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [$option],
            $input
        );
    }

    /** @doesNotPerformAssertions */
    public function testValidateRequiredOptionsWithRequiredNonEmptyValue(): void
    {
        $optionName = 'abc';

        $option = $this->createMock(InputOption::class);
        $option->method('getName')
            ->willReturn($optionName);
        $option->method('isValueRequired')
            ->willReturn(true);

        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')
            ->with($optionName)
            ->willReturn(0);

        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [$option],
            $input
        );
    }

    public function testValidateRequiredOptionsWithRequiredEmptyValue(): void
    {
        $optionName = 'abc';

        $option = $this->createMock(InputOption::class);
        $option->method('getName')
            ->willReturn($optionName);
        $option->method('isValueRequired')
            ->willReturn(true);

        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')
            ->with($optionName)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [$option],
            $input
        );
    }
}
