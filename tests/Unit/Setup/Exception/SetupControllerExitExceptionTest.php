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

use OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException;

class SetupControllerExitExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateSut()
    {
        new SetupControllerExitException();
    }

    public function testIsThrowable()
    {
        $this->setExpectedException(SetupControllerExitException::class);

        throw new SetupControllerExitException();
    }

    public function testIndicatesWasThrownWithoutArgumentsOfTemplateFile()
    {
        $expectedTemplateFileName = null;

        try {
            throw new SetupControllerExitException();
        } catch (SetupControllerExitException $exception) {
            $actualTemplateFileName = $exception->getTemplateFileName();
        }

        $this->assertSame($expectedTemplateFileName, $actualTemplateFileName);
    }

    public function testIndicatesWasThrownWithArgumentOfTemplateFile()
    {
        $expectedTemplateFileName = 'test.php';

        try {
            throw new SetupControllerExitException($expectedTemplateFileName);
        } catch (SetupControllerExitException $exception) {
            $actualTemplateFileName = $exception->getTemplateFileName();
        }

        $this->assertSame($expectedTemplateFileName, $actualTemplateFileName);
    }
}
