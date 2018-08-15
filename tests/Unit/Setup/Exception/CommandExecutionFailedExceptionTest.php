<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Setup\Exception;

use OxidEsales\EshopCommunity\Setup\Exception\CommandExecutionFailedException;

class CommandExecutionFailedExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testCanCreateSut()
    {
        new CommandExecutionFailedException('command');
    }

    public function testProvidesInformationAboutFailedCommand()
    {
        $this->expectException(
            CommandExecutionFailedException::class,
            "There was an error while executing 'test_string'."
        );

        throw new CommandExecutionFailedException('test_string');
    }

    public function testIsAbleToReturnCommand()
    {
        $sut = new CommandExecutionFailedException('command_name');

        $this->assertSame('command_name', $sut->getCommand());
    }

    public function testIsAbleToReturnTheReturnCode()
    {
        $sut = new CommandExecutionFailedException('command_name');
        $sut->setReturnCode(5);

        $this->assertSame(5, $sut->getReturnCode());
    }

    public function testReturnCodeIsZeroAsDefault()
    {
        $sut = new CommandExecutionFailedException('command_name');

        $this->assertSame(0, $sut->getReturnCode());
    }

    public function testIsAbleToReturnTheCommandErrorOutput()
    {
        $sut = new CommandExecutionFailedException('command_name');
        $sut->setCommandOutput(['line_1', 'line_2']);

        $this->assertSame("line_1\nline_2", $sut->getCommandOutput());
    }

    public function testErrorOutputNullAsDefault()
    {
        $sut = new CommandExecutionFailedException('command_name');

        $this->assertSame(null, $sut->getCommandOutput());
    }
}
