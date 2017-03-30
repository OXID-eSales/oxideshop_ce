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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * @covers oxApplicationServer
 */
class ApplicationServerTest extends \OxidTestCase
{

    public function testSetGetId()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setId('ThisIsServerId');
        $this->assertSame('ThisIsServerId', $oServerNode->getId());
    }

    public function testSetGetIp()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setIp('11.11.11.11');
        $this->assertSame('11.11.11.11', $oServerNode->getIp());
    }

    public function testSetGetTimeStamp()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setTimestamp(123456789);
        $this->assertSame(123456789, $oServerNode->getTimestamp());
    }

    public function testSetGetLastFrontendUsage()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setLastFrontendUsage(123456789);
        $this->assertSame(123456789, $oServerNode->getLastFrontendUsage());
    }

    public function testSetGetLastAdminUsage()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setLastAdminUsage(123456789);
        $this->assertSame(123456789, $oServerNode->getLastAdminUsage());
    }

    public function testServerValidityOnCreation()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $this->assertFalse($oServerNode->isValid());
    }

    public function testServerValidityOnSetFalse()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setIsValid(false);
        $this->assertFalse($oServerNode->isValid());
    }

    public function testServerValidityOnSetTrue()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setIsValid(true);
        $this->assertTrue($oServerNode->isValid());
    }

    public function testServerValidityOnSetDefault()
    {
        $oServerNode = oxNew('oxApplicationServer');
        $oServerNode->setIsValid();
        $this->assertTrue($oServerNode->isValid());
    }

}
