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

/**
 * @covers oxApplicationServer
 */
class Unit_Core_oxApplicationServerTest extends OxidTestCase
{

    public function testSetGetId()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setId('ThisIsServerId');
        $this->assertSame('ThisIsServerId', $oServerNode->getId());
    }

    public function testSetGetIp()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setIp('11.11.11.11');
        $this->assertSame('11.11.11.11', $oServerNode->getIp());
    }

    public function testSetGetTimeStamp()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setTimestamp(123456789);
        $this->assertSame(123456789, $oServerNode->getTimestamp());
    }

    public function testSetGetLastFrontendUsage()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setLastFrontendUsage(123456789);
        $this->assertSame(123456789, $oServerNode->getLastFrontendUsage());
    }

    public function testSetGetLastAdminUsage()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setLastAdminUsage(123456789);
        $this->assertSame(123456789, $oServerNode->getLastAdminUsage());
    }

    public function testServerValidityOnCreation()
    {
        $oServerNode = new oxApplicationServer();
        $this->assertFalse($oServerNode->isValid());
    }

    public function testServerValidityOnSetFalse()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setIsValid(false);
        $this->assertFalse($oServerNode->isValid());
    }

    public function testServerValidityOnSetTrue()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setIsValid(true);
        $this->assertTrue($oServerNode->isValid());
    }

    public function testServerValidityOnSetDefault()
    {
        $oServerNode = new oxApplicationServer();
        $oServerNode->setIsValid();
        $this->assertTrue($oServerNode->isValid());
    }

}