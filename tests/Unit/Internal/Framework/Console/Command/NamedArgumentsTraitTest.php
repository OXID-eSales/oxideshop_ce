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
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class NamedArgumentsTraitTest extends TestCase
{
    use ProphecyTrait;

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
            $this->prophesize(InputInterface::class)->reveal()
        );
    }

    /** @doesNotPerformAssertions */
    public function testValidateRequiredOptionsWithNonRequiredEmptyValue(): void
    {
        $optionName = 'abc';
        $option = $this->prophesize(InputOption::class);
        $option->getName()->willReturn($optionName);
        $option->isValueRequired()->willReturn(false);
        $input = $this->prophesize(InputInterface::class);
        $input->getOption($optionName)->willReturn(null);

        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [$option->reveal()],
            $input->reveal()
        );
    }

    /** @doesNotPerformAssertions */
    public function testValidateRequiredOptionsWithRequiredNonEmptyValue(): void
    {
        $optionName = 'abc';
        $option = $this->prophesize(InputOption::class);
        $option->getName()->willReturn($optionName);
        $option->isValueRequired()->willReturn(true);
        $input = $this->prophesize(InputInterface::class);
        $input->getOption($optionName)->willReturn(0);

        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [$option->reveal()],
            $input->reveal()
        );
    }

    public function testValidateRequiredOptionsWithRequiredEmptyValue(): void
    {
        $optionName = 'abc';
        $option = $this->prophesize(InputOption::class);
        $option->getName()->willReturn($optionName);
        $option->isValueRequired()->willReturn(true);
        $input = $this->prophesize(InputInterface::class);
        $input->getOption($optionName)->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->namedArgumentsTrait->checkRequiredCommandOptions(
            [$option->reveal()],
            $input->reveal()
        );
    }
}
