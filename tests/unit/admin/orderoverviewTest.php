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
 * Tests for Order_Overview class
 */
class Unit_Admin_OrderOverviewTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable( 'oxorder' );
        $this->cleanUpTable( "oxorderarticles" );
        parent::tearDown();
    }

    /**
     * Order_Overview::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setParameter( "oxid", "testId" );

        // testing..
        $oView = new Order_Overview();
        $this->assertEquals( 'order_overview.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['edit'] ) );
        $this->assertTrue( $aViewData['edit'] instanceof oxorder );
    }

    /**
     * Order_Overview::GetPaymentType() test case
     *
     * @return null
     */
    public function testGetPaymentType()
    {
        oxTestModules::addFunction( 'oxpayment', 'load', '{ $this->oxpayments__oxdesc = new oxField("testValue"); return true; }');

        // defining parameters
        $oOrder = $this->getMock( "oxorder", array( "getPaymentType" ) );
        $oOrder->oxorder__oxpaymenttype = new oxField( "testValue" );

        $oView = new Order_Overview();
        $oUserPayment = $oView->UNITgetPaymentType( $oOrder);

        $this->assertTrue( $oUserPayment instanceof oxuserpayment );
        $this->assertEquals( "testValue", $oUserPayment->oxpayments__oxdesc->value );
    }


    /**
     * Order_Overview::Exportlex() test case
     *
     * @return null
     */
    public function testExportlex()
    {
        oxTestModules::addFunction( 'oximex', 'exportLexwareOrders', '{ return "testExportData"; }');
        oxTestModules::addFunction( 'oxUtils', 'setHeader', '{ if ( !isset( $this->_aHeaderData ) ) { $this->_aHeaderData = array();} $this->_aHeaderData[] = $aA[0]; }');
        oxTestModules::addFunction( 'oxUtils', 'getHeaders', '{ return $this->_aHeaderData; }');
        oxTestModules::addFunction( 'oxUtils', 'showMessageAndExit', '{ $this->_aHeaderData[] = $aA[0]; }');

        // testing..
        $oView = new Order_Overview();
        $oView->exportlex();

        $aHeaders = oxUtils::getInstance()->getHeaders();
        $this->assertEquals( "Pragma: public", $aHeaders[0] );
        $this->assertEquals( "Cache-Control: must-revalidate, post-check=0, pre-check=0", $aHeaders[1] );
        $this->assertEquals( "Expires: 0", $aHeaders[2] );
        $this->assertEquals( "Content-type: application/x-download", $aHeaders[3] );
        $this->assertEquals( "Content-Length: ".strlen( "testExportData" ), $aHeaders[4] );
        $this->assertEquals( "Content-Disposition: attachment; filename=intern.xml", $aHeaders[5] );
        $this->assertEquals( "testExportData", $aHeaders[6] );
    }

    /**
     * Order_Overview::CreatePDF() test case
     *
     * @return null
     */
    public function testCreatePDF()
    {
        // testing..
        $soxId = '_testOrderId';

        // writing test order
        $oOrder = oxNew( "oxorder" );
        $oOrder->setId( $soxId );
        $oOrder->oxorder__oxshopid        = new oxField( oxConfig::getInstance()->getBaseShopId() );
        $oOrder->oxorder__oxuserid        = new oxField( "oxdefaultadmin" );
        $oOrder->oxorder__oxbillcompany   = new oxField( "Ihr Firmenname" );
        $oOrder->oxorder__oxbillemail     = new oxField( oxADMIN_LOGIN );
        $oOrder->oxorder__oxbillfname     = new oxField( "Hans" );
        $oOrder->oxorder__oxbilllname     = new oxField( "Mustermann" );
        $oOrder->oxorder__oxbillstreet    = new oxField( "Musterstr" );
        $oOrder->oxorder__oxbillstreetnr  = new oxField( "10" );
        $oOrder->oxorder__oxbillcity      = new oxField( "Musterstadt" );
        $oOrder->oxorder__oxbillcountryid = new oxField( "a7c40f6320aeb2ec2.72885259" );
        $oOrder->oxorder__oxbillzip       = new oxField( "79098" );
        $oOrder->oxorder__oxbillsal       = new oxField( "Herr" );
        $oOrder->oxorder__oxpaymentid     = new oxField( "1f53d82f6391b86db09786fd75b69cb9" );
        $oOrder->oxorder__oxpaymenttype   = new oxField( "oxidcashondel" );
        $oOrder->oxorder__oxtotalnetsum   = new oxField( 75.55 );
        $oOrder->oxorder__oxtotalbrutsum  = new oxField( 89.9 );
        $oOrder->oxorder__oxtotalordersum = new oxField( 117.4 );
        $oOrder->oxorder__oxdelcost       = new oxField( 20 );
        $oOrder->oxorder__oxdelval        = new oxField( 0 );
        $oOrder->oxorder__oxpaycost       = new oxField( 7.5 );
        $oOrder->oxorder__oxcurrency      = new oxField( "EUR" );
        $oOrder->oxorder__oxcurrate       = new oxField( 1 );
        $oOrder->oxorder__oxdeltype       = new oxField( "oxidstandard" );
        $oOrder->oxorder__oxordernr       = new oxField( 1 );
        $oOrder->save();
        modConfig::setParameter( "oxid", $soxId );
        oxTestModules::addFunction( 'oxUtils', 'setHeader', '{ if ( !isset( $this->_aHeaderData ) ) { $this->_aHeaderData = array();} $this->_aHeaderData[] = $aA[0]; }');
        oxTestModules::addFunction( 'oxUtils', 'getHeaders', '{ return $this->_aHeaderData; }');
        oxTestModules::addFunction( 'oxUtils', 'showMessageAndExit', '{ $this->_aHeaderData[] = "testExportData"; }');

        // testing..
        $oView = new Order_Overview();
        $oView->createPDF();

        $aHeaders = oxUtils::getInstance()->getHeaders();
        $this->assertEquals( "Pragma: public", $aHeaders[0] );
        $this->assertEquals( "Cache-Control: must-revalidate, post-check=0, pre-check=0", $aHeaders[1] );
        $this->assertEquals( "Expires: 0", $aHeaders[2] );
        $this->assertEquals( "Content-type: application/pdf", $aHeaders[3] );
        $this->assertEquals( "Content-Disposition: attachment; filename=1_Mustermann.pdf", $aHeaders[4] );
        $this->assertEquals( "testExportData", $aHeaders[5] );
    }

    /**
     * Order_Overview::ExportDTAUS() test case
     *
     * @return null
     */
    public function testExportDTAUS()
    {
        // testing..
        $soxId = '_testOrderId';

        // writing test order
        $oOrder = oxNew( "oxorder" );
        $oOrder->setId( $soxId );
        $oOrder->oxorder__oxshopid        = new oxField( oxConfig::getInstance()->getBaseShopId() );
        $oOrder->oxorder__oxuserid        = new oxField( "oxdefaultadmin" );
        $oOrder->oxorder__oxbillcompany   = new oxField( "Ihr Firmenname" );
        $oOrder->oxorder__oxbillemail     = new oxField( oxADMIN_LOGIN );
        $oOrder->oxorder__oxbillfname     = new oxField( "Hans" );
        $oOrder->oxorder__oxbilllname     = new oxField( "Mustermann" );
        $oOrder->oxorder__oxbillstreet    = new oxField( "Musterstr" );
        $oOrder->oxorder__oxbillstreetnr  = new oxField( "10" );
        $oOrder->oxorder__oxbillcity      = new oxField( "Musterstadt" );
        $oOrder->oxorder__oxbillcountryid = new oxField( "a7c40f6320aeb2ec2.72885259" );
        $oOrder->oxorder__oxbillzip       = new oxField( "79098" );
        $oOrder->oxorder__oxbillsal       = new oxField( "Herr" );
        $oOrder->oxorder__oxpaymentid     = new oxField( "1f53d82f6391b86db09786fd75b69cb9" );
        $oOrder->oxorder__oxpaymenttype   = new oxField( "oxiddebitnote" );
        $oOrder->oxorder__oxtotalnetsum   = new oxField( 75.55 );
        $oOrder->oxorder__oxtotalbrutsum  = new oxField( 89.9 );
        $oOrder->oxorder__oxtotalordersum = new oxField( 117.4 );
        $oOrder->oxorder__oxdelcost       = new oxField( 20 );
        $oOrder->oxorder__oxdelval        = new oxField( 0 );
        $oOrder->oxorder__oxpaycost       = new oxField( 7.5 );
        $oOrder->oxorder__oxcurrency      = new oxField( "EUR" );
        $oOrder->oxorder__oxcurrate       = new oxField( 1 );
        $oOrder->oxorder__oxdeltype       = new oxField( "oxidstandard" );
        $oOrder->oxorder__oxordernr       = new oxField( 1 );
        $oOrder->save();
        modConfig::setParameter( "ordernr", 1 );
        oxTestModules::addFunction( 'oxUtils', 'setHeader', '{ if ( !isset( $this->_aHeaderData ) ) { $this->_aHeaderData = array();} $this->_aHeaderData[] = $aA[0]; }');
        oxTestModules::addFunction( 'oxUtils', 'getHeaders', '{ return $this->_aHeaderData; }');
        oxTestModules::addFunction( 'oxUtils', 'showMessageAndExit', '{ $this->_aHeaderData[] = "testExportData"; }');

        $oView = new Order_Overview();
        $oView->exportDTAUS();

        $aHeaders = oxUtils::getInstance()->getHeaders();
        $this->assertEquals( "Content-Disposition: attachment; filename=\"dtaus0.txt\"", $aHeaders[0] );
        $this->assertEquals( "Content-type: text/plain", $aHeaders[1] );
        $this->assertEquals( "Cache-control: public", $aHeaders[2] );
        $this->assertEquals( "testExportData", $aHeaders[3] );
    }

    /**
     * Order_Overview::Sendorder() test case
     *
     * @return null
     */
    public function testSendorder()
    {
        modConfig::setParameter( "sendmail", true );
        oxTestModules::addFunction( 'oxemail', 'sendSendedNowMail', '{ throw new Exception( "sendSendedNowMail" ); }');
        oxTestModules::addFunction( 'oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxorder', 'save', '{ return true; }');
        oxTestModules::addFunction( 'oxorder', 'getOrderArticles', '{ return array(); }');

        // testing..
        try {
            $oView = new Order_Overview();
            $oView->sendorder();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "sendSendedNowMail", $oExcp->getMEssage(), "Error in Order_Overview::sendorder()" );
            return;
        }
        $this->fail( "Error in Order_Overview::sendorder()" );
    }

    /**
     * Order_Overview::Resetorder() test case
     *
     * @return null
     */
    public function testResetorder()
    {
        oxTestModules::addFunction( 'oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxorder', 'save', '{ throw new Exception( $this->oxorder__oxsenddate->value ); }');

        // testing..
        try {
            $oView = new Order_Overview();
            $oView->resetorder();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "0000-00-00 00:00:00", $oExcp->getMessage(), "Error in Order_Overview::resetorder()" );
            return;
        }
        $this->fail( "Error in Order_Overview::resetorder()" );
    }

    /**
     * Order_Overview::CanExport() test case
     *
     * @return null
     */
    public function testCanExport()
    {
        oxTestModules::addFunction( 'oxModule', 'isActive', '{ return true; }');

        $oBase = new oxbase();
        $oBase->init( "oxorderarticles" );
        $oBase->setId( "_testOrderArticleId");
        $oBase->oxorderarticles__oxorderid = new oxField( "testOrderId" );
        $oBase->oxorderarticles__oxamount  = new oxField( 1 );
        $oBase->oxorderarticles__oxartid   = new oxField( "1126" );
        $oBase->oxorderarticles__oxordershopid = new oxField( oxConfig::getInstance()->getShopId() );
        $oBase->save();

        // testing..
        $oView = new Order_Overview();

        $oView = $this->getMock( "Order_Overview", array( "getEditObjectId" ) );
        $oView->expects( $this->any() )->method( 'getEditObjectId')->will( $this->returnValue( 'testOrderId' ) );

        $this->assertTrue( $oView->canExport() );
    }

    /**
     * Order shipping date reset test case
     *
     * @return null
     */
    public function testCanReset(){
        $soxId = '_testOrderId';
        // writing test order
        $oOrder = oxNew( "oxorder" );
        $oOrder->setId( $soxId );
        $oOrder->oxorder__oxshopid        = new oxField( oxConfig::getInstance()->getBaseShopId() );
        $oOrder->oxorder__oxuserid        = new oxField( "oxdefaultadmin" );
        $oOrder->oxorder__oxbillcompany   = new oxField( "Ihr Firmenname" );
        $oOrder->oxorder__oxbillemail     = new oxField( oxADMIN_LOGIN );
        $oOrder->oxorder__oxbillfname     = new oxField( "Hans" );
        $oOrder->oxorder__oxbilllname     = new oxField( "Musterm0ann" );
        $oOrder->oxorder__oxbillstreet    = new oxField( "Musterstr" );
        $oOrder->oxorder__oxstorno        = new oxField( "0" );
        $oOrder->oxorder__oxsenddate      = new oxField( "0000-00-00 00:00:00");
        $oOrder->save();

        $oView = new Order_Overview();

        modConfig::setParameter( "oxid", $soxId );
        $this->assertFalse($oView->canResetShippingDate());

        $oOrder->oxorder__oxsenddate      = new oxField( date( "Y-m-d H:i:s", oxUtilsDate::getInstance()->getTime()));
        $oOrder->save();

        $this->assertTrue($oView->canResetShippingDate());

        $oOrder->oxorder__oxstorno        = new oxField( "1" );
        $oOrder->save();

        $this->assertFalse($oView->canResetShippingDate());

        $oOrder->oxorder__oxsenddate      = new oxField( "0000-00-00 00:00:00");
        $oOrder->save();

        $this->assertFalse($oView->canResetShippingDate());
    }
}
