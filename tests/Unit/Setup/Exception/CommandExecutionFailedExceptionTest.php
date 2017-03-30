<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Setup\Exception;

use OxidEsales\EshopCommunity\Setup\Exception\CommandExecutionFailedException;

class CommandExecutionFailedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateSut()
    {
        new CommandExecutionFailedException('command');
    }

    public function testProvidesInformationAboutFailedCommand()
    {
        $this->setExpectedException(
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
