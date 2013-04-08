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
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for dyn_ipayment class
 */
class Unit_Admin_dynipaymentTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "drop table if exists oxobject2ipayment" );
        parent::tearDown();
    }

    /**
     * dyn_ipayment::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new dyn_ipayment();
        $this->assertEquals( 'dyn_ipayment.tpl', $oView->render() );
    }

    /**
     * dyn_ipayment::AddPayment() test case
     *
     * @return null
     */
    public function testAddPayment()
    {
        modConfig::setParameter( "allpayments", array( "payment1", "payment2" ) );
        oxTestModules::addFunction( 'oxbase', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction( 'oxbase', 'init', '{}');

        // testing..
        try {
            $oView = new dyn_ipayment();
            $oView->addPayment();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in dyn_ipayment::addPayment()" );
            return;
        }
        $this->fail( "error in dyn_ipayment::addPayment()" );
    }

    /**
     * dyn_ipayment::RemovePayment() test case
     *
     * @return null
     */
    public function testRemovePayment()
    {
        modConfig::setParameter( "addpayments", array( "1", "2" ) );

        $iShopId = oxConfig::getInstance()->getBaseShopId();

        $oDb = oxDb::getDb();
        $oDb->execute( "CREATE TABLE `oxobject2ipayment` (`oxid` char( 32 ) NOT NULL, `oxshopid` char( 32 ) NOT NULL) ENGINE = MYISAM" );
        $oDb->execute( "insert into `oxobject2ipayment` values ( '1', '{$iShopId}' ), ( '2', '{$iShopId}' ), ( '3', '{$iShopId}' ), ( '4', '{$iShopId}' )" );
        $this->assertEquals( 4, $oDb->getOne( "select count(*) from oxobject2ipayment" ) );

        $oView = new dyn_ipayment();
        $oView->removePayment();

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from oxobject2ipayment" ) );
    }

    /**
     * dyn_ipayment::SavePayment() test case
     *
     * @return null
     */
    public function testSavePayment()
    {
        modConfig::setParameter( "oxpaymentid", oxDb::getDb()->getOne( "select oxid from oxpayments where oxactive = 1" ) );
        modConfig::setParameter( "editval", array( "testParam" => "testValue" ) );

        // testing..
        oxTestModules::addFunction( 'oxbase', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction( 'oxbase', 'assignRecord', '{ return true; }');
        oxTestModules::addFunction( 'oxbase', 'init', '{}');

        // testing..
        try {
            $oView = new dyn_ipayment();
            $oView->savePayment();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in dyn_ipayment::savePayment()" );
            return;
        }
        $this->fail( "error in dyn_ipayment::savePayment()" );
    }

    /**
     * dyn_ipayment::SetFilter() test case
     *
     * @return null
     */
    public function testSetFilter()
    {
        $oView = $this->getProxyClass( "dyn_ipayment" );
        $oView->setFilter();
        $this->assertTrue( $oView->getNonPublicVar( "blfiltering" ));
    }

    /**
     * dyn_ipayment::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = new dyn_ipayment();
        $this->assertEquals( 'dyn_interface', $oView->getViewId() );
    }
}
