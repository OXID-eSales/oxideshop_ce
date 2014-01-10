<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Testing oxshoplist class
 */
class Unit_Core_oxshoplistTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxDb::getDb()->Execute( "delete from oxshops where oxid > 1" );
        for ( $i = 2; $i < 5; $i++ ) {
            $sQ = "insert into `oxshops` (OXID, OXACTIVE, OXNAME) VALUES ($i, 1, 'Test Shop $i') ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Testing if shop list loading succeded
     */
    public function testLoad()
    {
        $oShopList = new oxShoplist();
        $oShopList->selectString( 'select * from oxshops where oxactive = 1 order by oxid ' );
        $this->assertEquals( 4, $oShopList->count() );

            $aIds = array( 2, 3, 4, oxConfig::getInstance()->getBaseShopId() );
        // checking ids of loaded shops
        $this->assertEquals( $aIds, $oShopList->arrayKeys() );
    }
}
