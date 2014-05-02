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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxexpirationemailTest extends OxidTestCase
{
    public function providerSendToCheckBody()
    {
        return array(
            array(null, oxRegistry::getLang()->translateString('SHOP_LICENSE_ERROR_GRACE_EXPIRED', null, true)),
            array('test', 'test')
        );
    }

    /**
     * @param string $sBody Email content.
     * @param string $sExpectedBody
     *
     * @dataProvider providerSendToCheckBody
     */
    public function testSendToCheckBody($sBody, $sExpectedBody)
    {
        /** @var oxEmail $oEmail */
        $oEmail = $this->getMock('oxEmail', array('send'));
        $oEmail->expects($this->once())->method('send');

        $oExpirationEmail = new oxExpirationEmail($oEmail);
        $oExpirationEmail->setBody($sBody);
        $oExpirationEmail->send();

        $this->assertSame(
            $sExpectedBody,
            $oExpirationEmail->getEmail()->getBody(),
            'Email message was set incorrectly.'
        );
    }

    /**
     * Check if oxEmail object was created oxExpirationEmail object construct.
     */
    public function testGetEmailIfWasNotSentOnConstruct()
    {
        $oExpirationEmail = new oxExpirationEmail();
        $this->assertInstanceOf(
            'oxEmail',
            $oExpirationEmail->getEmail(),
            'oxExpirationEmail construct did not create oxEmail object.'
        );
    }
}