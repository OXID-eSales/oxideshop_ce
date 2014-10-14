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


/**
 * Tests for Delivery_Groups_Ajax class
 */
class Unit_Admin_DeliverysetUsersAjaxTest extends OxidTestCase
{
    protected $_sShopId = '1';
    
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        
        oxDb::getDb()->execute( "insert into oxobject2delivery set oxid='_testDeliveryUser1', oxobjectid='_testObjectId'" );
        oxDb::getDb()->execute( "insert into oxobject2delivery set oxid='_testDeliveryUser2', oxobjectid='_testObjectId'" );
        //for delete all
        oxDb::getDb()->execute( "insert into oxobject2delivery set oxid='_testDeliveryUserDeleteAll1', oxdeliveryid='_testDeliveryUserRemoveAll', oxobjectid='_testUser1', oxtype='oxdelsetu'" );
        oxDb::getDb()->execute( "insert into oxobject2delivery set oxid='_testDeliveryUserDeleteAll2', oxdeliveryid='_testDeliveryUserRemoveAll', oxobjectid='_testUser2', oxtype='oxdelsetu'" );
        
        oxDb::getDb()->execute( "insert into oxuser set oxid='_testUser1', oxusername='_testUser1'" );
        oxDb::getDb()->execute( "insert into oxuser set oxid='_testUser2', oxusername='_testUser2'" );
        
            $this->setShopIdTest( 'oxbaseshop' );
    }
    
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxobject2delivery where oxid='_testDeliveryUser1'" );
        oxDb::getDb()->execute( "delete from oxobject2delivery where oxid='_testDeliveryUser2'" );
        
        oxDb::getDb()->execute( "delete from oxobject2delivery where oxid='_testDeliveryUserDeleteAll1'" );
        oxDb::getDb()->execute( "delete from oxobject2delivery where oxid='_testDeliveryUserDeleteAll2'" );
        
        oxDb::getDb()->execute( "delete from oxuser where oxid='_testUser1'" );
        oxDb::getDb()->execute( "delete from oxuser where oxid='_testUser2'" );
        
        oxDb::getDb()->execute( "delete from oxobject2delivery where oxdeliveryid='_testActionAddUser'" );
        oxDb::getDb()->execute( "delete from oxobject2delivery where oxdeliveryid='_testActionAddUserAll'" );
        
        parent::tearDown();
    }
    
    public function setShopIdTest( $sParam )
    {
        $this->_sShopId = $sParam;
    }
    
    public function getShopIdTest()
    {
        return $this->_sShopId;
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxuser where 1 and oxuser.oxshopid = '".$this->getShopIdTest()."'" , trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryMallUsers()
    {
        $this->getConfig()->setConfigParam( "blMallUsers", true );
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxuser where 1" , trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxuser where 1 and oxuser.oxshopid = '".$this->getShopIdTest()."' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sSynchoxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidMallUsers()
    {
        $this->getConfig()->setConfigParam( "blMallUsers", true );
        $sSynchoxid = '_testAction';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxuser where 1 and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sSynchoxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sOxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxobject2group left join oxuser on oxuser.oxid = oxobject2group.oxobjectid  where oxobject2group.oxgroupsid = '".$sOxid."'and oxuser.oxshopid = '".$this->getShopIdTest()."' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sSynchoxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxidMallUsers()
    {
        $this->getConfig()->setConfigParam( "blMallUsers", true );
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( "from oxobject2group left join oxuser on oxuser.oxid = oxobject2group.oxobjectid  where oxobject2group.oxgroupsid = '".$sOxid."'and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sSynchoxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetUsersAjax::removeUserFromSet() test case
     *
     * @return null
     */
    public function testRemoveUserFromSet()
    {
        $oView = $this->getMock( "deliveryset_users_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testDeliveryUser1', '_testDeliveryUser2' ) ) );
        
        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryUser1', '_testDeliveryUser2')";
        $this->assertEquals( 2, oxDb::getDb()->getOne( $sSql ) );
        $oView->removeUserFromSet();
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
    }
    
    /**
     * DeliverysetUsersAjax::removeUserFromSet() test case
     *
     * @return null
     */
    public function testRemoveUserFromSetAll()
    {
        $sOxid = '_testDeliveryUserRemoveAll';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        $this->getConfig()->setParameter( "all", true );
        
        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sOxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu'";
        $oView = oxNew( 'deliveryset_users_ajax' );
        $this->assertEquals( 2, oxDb::getDb()->getOne( $sSql ) );
        $oView->removeUserFromSet();
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
    }
    
    /**
     * DeliverysetUsersAjax::addUserToSet() test case
     *
     * @return null
     */
    public function testAddUserToSet()
    {
        $sSynchoxid = '_testActionAddUser';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
        
        $oView = $this->getMock( "deliveryset_users_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testActionAdd1', '_testActionAdd2' ) ) );
        
        $oView->addUserToSet();
        $this->assertEquals( 2, oxDb::getDb()->getOne( $sSql ) );
    }
    
    /**
     * DeliverysetUsersAjax::addUserToSet() test case
     *
     * @return null
     */
    public function testAddUserToSetAll()
    {
        $sSynchoxid = '_testActionAddUserAll';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        $this->getConfig()->setParameter( "all", true );
        
        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne( "select count(oxuser.oxid) from oxuser where 1 and oxuser.oxshopid = '".$this->getShopIdTest()."' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '".$sSynchoxid."'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )" );
        
        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
        
        $oView = $this->getMock( "deliveryset_users_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testActionAdd1', '_testActionAdd2' ) ) );
        
        $oView->addUserToSet();
        $this->assertEquals( $iCount, oxDb::getDb()->getOne( $sSql ) );
    }
}