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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

require_once 'OxidTestCase.php';
require_once 'test_config.inc.php';

/**
 * Testing oxwrapping class
 */
class Unit_oxstatisticTest extends OxidTestCase {
    protected $aAllreports = array();
    /**
     * Creating some additional users for test
     */
    protected function setUp() {

        $myConfig = oxConfig::getInstance();
        $myDB     = oxConfig::getInstance()->getDB();



        $sData = 'a:0:{}';
        $sShopId = $myConfig->getBaseShopId();
        $sQ = 'insert into oxstatistics values ("oxstattest", "'.$sShopId.'", "oxstattest", "'.$sData.'") ';
        $myDB->Execute( $sQ );
    }
    protected function tearDown() {
        $myDB     = oxConfig::getInstance()->getDB();

        $sQ = 'delete from oxstatistics where oxid = "oxstattest" ';
        $myDB->Execute( $sQ );
    }

    /**
     * Testing if loading succeded
     */
    public function test_load() {
        if ( OXID_VERSION_EE ) {
            $this->markTestSkipped('Only for version PE.');
        }

        $myUtils  = oxUtils::getInstance();

        $oStat = oxNew( 'oxstatistic' );
        $oStat->load( 'oxstattest' );
        $this->assertEquals( 'oxstattest', $oStat->oxstatistics__oxtitle->value);
        $this->assertEquals(array(),$oStat->getReports());
    }

    public function test_setReports(){
        if ( OXID_VERSION_EE ) {
            $this->markTestSkipped('Only for version PE.');
        }
        $myUtils  = oxUtils::getInstance();

        $aTest = array();
        $aTest[0]="test1";
        $aTest[1]="test2";
        $oStat = oxNew( 'oxstatistic' );
        $oStat->load( 'oxstattest' );
        $oStat->setReports($aTest);
        $oStat->save();
        $this->assertEquals($aTest,$oStat->getReports());
    }

}
