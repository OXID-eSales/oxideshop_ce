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
 * Class Unit_Core_oxOnlineServerEmailBuilderTest
 */
class Unit_Core_oxOnlineServerEmailBuilderTest extends OxidTestCase
{

    public function testBuildIfParametersWereSetCorrectly()
    {
        $sBody = '_testXML';
        $oExpirationEmailBuilder = new oxOnlineServerEmailBuilder();
        $oExpirationEmail = $oExpirationEmailBuilder->build($sBody);
        $aRecipient = $oExpirationEmail->getRecipient();

        $this->assertSame($sBody, $oExpirationEmail->getBody(), 'Email content is not as it should be.');
        $this->assertSame('olc@oxid-esales.com', $aRecipient[0][0], 'Recipient email address is wrong.');
        $this->assertSame(oxRegistry::getLang()->translateString('SUBJECT_UNABLE_TO_SEND_VIA_CURL', null, true), $oExpirationEmail->getSubject(), 'Subject is wrong.');
    }
}