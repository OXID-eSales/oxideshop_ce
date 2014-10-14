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

require_once realpath( "." ) . '/unit/OxidTestCase.php';
require_once realpath( "." ) . '/unit/test_config.inc.php';
require_once getShopBasePath() . '/setup/oxsetup.php';

class Unit_Core_oxonlinemoduleversionnotifierTest extends OxidTestCase
{

    /**
     * Test set/get Online Module Version Notifier web service url.
     */
    public function testSetGetWebServiceUrl()
    {
        $oOmvn = new oxOnlineModuleVersionNotifier();

        $sExpectedWebServiceUrl = 'https://omvn.oxid-esales.com/check.php';
        $sExpectedNewWebServiceUrl = 'new.webservice.url';

        $this->assertEquals( $sExpectedWebServiceUrl, $oOmvn->getWebServiceUrl() );

        $oOmvn->setWebServiceUrl( $sExpectedNewWebServiceUrl );
        $this->assertEquals( $sExpectedNewWebServiceUrl, $oOmvn->getWebServiceUrl() );
    }

    /**
     * Test set/get raw response received from Online Module Version Notifier web service.
     */
    public function testSetGetRawResponseMessage()
    {
        $oOmvn = new oxOnlineModuleVersionNotifier();

        $sExpectedRawResponseMessage = 'raw response message';

        $oOmvn->setRawResponseMessage( $sExpectedRawResponseMessage );
        $this->assertEquals( $sExpectedRawResponseMessage, $oOmvn->getRawResponseMessage() );
    }

    /**
     * Test if module notification was without exceptions.
     */
    public function testVersionNotify()
    {
        $oOmvn = new oxOnlineModuleVersionNotifier();

        $this->assertTrue($oOmvn->versionNotify());
        $this->assertFalse($oOmvn->isException());
    }
}