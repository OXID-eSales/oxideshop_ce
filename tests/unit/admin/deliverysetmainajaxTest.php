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
class Unit_Admin_DeliverysetMainAjaxTest extends OxidTestCase
{
    protected $_sDeliveryView = 'oxv_oxdelivery_1_de';
    
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        
        oxDb::getDb()->execute( "insert into oxdel2delset set oxid='_testDeliverysetMain1', oxdelsetid='_testObjectId'" );
        oxDb::getDb()->execute( "insert into oxdel2delset set oxid='_testDeliverysetMain2', oxdelsetid='_testObjectId'" );
        //for delete all
        oxDb::getDb()->execute( "insert into oxdel2delset set oxid='_testDeliverysetMainDelAll1', oxdelsetid='_testDeliverysetMainRemoveAll', oxdelid='_testMain1'" );
        oxDb::getDb()->execute( "insert into oxdel2delset set oxid='_testDeliverysetMainDelAll2', oxdelsetid='_testDeliverysetMainRemoveAll', oxdelid='_testMain2'" );
        
        
            $this->setDeliveryViewTable( 'oxv_oxdelivery_de' );
            oxDb::getDb()->execute( "insert into oxdelivery set oxid='_testMain1', oxtitle='_testMain1'" );
            oxDb::getDb()->execute( "insert into oxdelivery set oxid='_testMain2', oxtitle='_testMain2'" );
    }
    
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxdel2delset where oxid='_testDeliverysetMain1'" );
        oxDb::getDb()->execute( "delete from oxdel2delset where oxid='_testDeliverysetMain2'" );
        
        oxDb::getDb()->execute( "delete from oxdel2delset where oxid='_testDeliverysetMainDelAll1'" );
        oxDb::getDb()->execute( "delete from oxdel2delset where oxid='_testDeliverysetMainDelAll2'" );
        
        oxDb::getDb()->execute( "delete from oxdelivery where oxid='_testMain1'" );
        oxDb::getDb()->execute( "delete from oxdelivery where oxid='_testMain2'" );
        
        oxDb::getDb()->execute( "delete from oxdel2delset where oxdelsetid='_testActionAddMain'" );
        oxDb::getDb()->execute( "delete from oxdel2delset where oxdelsetid='_testActionAddMainAll'" );
        
        parent::tearDown();
    }
    
    public function setDeliveryViewTable( $sParam )
    {
        $this->_sDeliveryView = $sParam;
    }
    
    public function getDeliveryViewTable()
    {
        return $this->_sDeliveryView;
    }
    
    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew( 'deliveryset_main_ajax' );
        $this->assertEquals( "from ".$this->getDeliveryViewTable()." where 1" , trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'deliveryset_main_ajax' );
        $this->assertEquals( "from ".$this->getDeliveryViewTable()." where 1 and ".$this->getDeliveryViewTable().".oxid not in ( select ".$this->getDeliveryViewTable().".oxid from ".$this->getDeliveryViewTable()." left join oxdel2delset on oxdel2delset.oxdelid=".$this->getDeliveryViewTable().".oxid where oxdel2delset.oxdelsetid = '".$sSynchoxid."' )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'deliveryset_main_ajax' );
        $this->assertEquals( "from ".$this->getDeliveryViewTable()." left join oxdel2delset on oxdel2delset.oxdelid=".$this->getDeliveryViewTable().".oxid where oxdel2delset.oxdelsetid = '".$sOxid."'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'deliveryset_main_ajax' );
        $this->assertEquals( "from ".$this->getDeliveryViewTable()." left join oxdel2delset on oxdel2delset.oxdelid=".$this->getDeliveryViewTable().".oxid where oxdel2delset.oxdelsetid = '".$sOxid."'and ".$this->getDeliveryViewTable().".oxid not in ( select ".$this->getDeliveryViewTable().".oxid from ".$this->getDeliveryViewTable()." left join oxdel2delset on oxdel2delset.oxdelid=".$this->getDeliveryViewTable().".oxid where oxdel2delset.oxdelsetid = '".$sSynchoxid."' )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * DeliverysetMainAjax::removeFromSet() test case
     *
     * @return null
     */
    public function testRemoveFromSet()
    {
        $oView = $this->getMock( "deliveryset_main_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testDeliverysetMain1', '_testDeliverysetMain2' ) ) );
        
        $sSql = "select count(oxid) from oxdel2delset where oxid in ('_testDeliverysetMain1', '_testDeliverysetMain2')";
        $this->assertEquals( 2, oxDb::getDb()->getOne( $sSql ) );
        $oView->removeFromSet();
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
    }
    
    /**
     * DeliverysetMainAjax::removeFromSet() test case
     *
     * @return null
     */
    public function testRemoveFromSetAll()
    {
        $sOxid = '_testDeliverysetMainRemoveAll';
        $this->getConfig()->setParameter( "oxid", $sOxid );
        $this->getConfig()->setParameter( "all", true );
        
        $sSql = "select count(oxdel2delset.oxid) from ".$this->getDeliveryViewTable()." left join oxdel2delset on oxdel2delset.oxdelid=".$this->getDeliveryViewTable().".oxid where oxdel2delset.oxdelsetid = '".$sOxid."'";
        
        $oView = oxNew( 'deliveryset_main_ajax' );
        $this->assertEquals( 2, oxDb::getDb()->getOne( $sSql ) );
        $oView->removeFromSet();
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
    }
    
    /**
     * DeliverysetMainAjax::addToSet() test case
     *
     * @return null
     */
    public function testAddToset()
    {
        $sSynchoxid = '_testActionAddMain';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        
        $sSql = "select count(oxid) from oxdel2delset where oxdelsetid='$sSynchoxid'";
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
        
        $oView = $this->getMock( "deliveryset_main_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testActionAdd1', '_testActionAdd2' ) ) );
        
        $oView->addToSet();
        $this->assertEquals( 2, oxDb::getDb()->getOne( $sSql ) );
    }
    
    /**
     * DeliverysetMainAjax::addToSet() test case
     *
     * @return null
     */
    public function testAddToSetAll()
    {
        $sSynchoxid = '_testActionAddMainAll';
        $this->getConfig()->setParameter( "synchoxid", $sSynchoxid );
        $this->getConfig()->setParameter( "all", true );
        
        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne( "select count(".$this->getDeliveryViewTable().".oxid) from ".$this->getDeliveryViewTable()." where 1 and ".$this->getDeliveryViewTable().".oxid not in ( select ".$this->getDeliveryViewTable().".oxid from ".$this->getDeliveryViewTable()." left join oxdel2delset on oxdel2delset.oxdelid=".$this->getDeliveryViewTable().".oxid where oxdel2delset.oxdelsetid = '".$sSynchoxid."' )" );
        
        $sSql = "select count(oxid) from oxdel2delset where oxdelsetid='$sSynchoxid'";
        $this->assertEquals( 0, oxDb::getDb()->getOne( $sSql ) );
        
        $oView = $this->getMock( "deliveryset_main_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testActionAdd1', '_testActionAdd2' ) ) );
        
        $oView->addToSet();
        $this->assertEquals( $iCount, oxDb::getDb()->getOne( $sSql ) );
    }
}