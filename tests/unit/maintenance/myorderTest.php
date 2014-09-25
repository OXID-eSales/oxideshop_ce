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
 * myOrder parrent chain class.
 */
class myOrder_parent extends oxOrder
{
}

/**
 * test pdf class.
 */
class testPdfClass
{
    /**
     * String width getter
     *
     * @param string $arg1 text
     *
     * @return integer
     */
    public function getStringWidth( $arg1=null )
    {
        return 1;
    }
}

require_once getShopBasePath() . 'modules/oe/invoicepdf/myorder.php';
require_once getShopBasePath() . 'core/oxpdf.php';

/**
 * myOrder parrent chain class.
 */
class myOrder_PdfArticleSummary extends PdfArticleSummary
{
    public function setTotalCostsWithDiscount( &$iStartPos ) {
        $this->_setTotalCostsWithDiscount( $iStartPos );
    }

    public function setVoucherInfo( &$iStartPos ) {
        $this->_setVoucherInfo( $iStartPos );
    }

    public function setDeliveryInfo( &$iStartPos ) {
        $this->_setDeliveryInfo( $iStartPos );
    }

    public function setWrappingInfo( &$iStartPos ) {
        $this->_setWrappingInfo( $iStartPos );
    }

    public function setPaymentInfo( &$iStartPos ) {
        $this->_setPaymentInfo( $iStartPos );
    }

    public function setGrandTotalPriceInfo( &$iStartPos ) {
        $this->_setGrandTotalPriceInfo( $iStartPos );
    }

    public function setPayUntilInfo( &$iStartPos ) {
        $this->_setPayUntilInfo( $iStartPos );
    }

    public function setPaymentMethodInfo( &$iStartPos ) {
        $this->_setPaymentMethodInfo( $iStartPos );
    }

    public function getVar( $sVar ) {
        return $this->$sVar;
    }
}

/**
 * Testing myorder module for printing pdf's
 */
class Unit_Maintenance_myorderTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable( 'oxorder' );
        $this->cleanUpTable( 'oxorderarticles' );
        $this->cleanUpTable( 'oxarticles' );
        parent::tearDown();
    }

    /**
     * Inserts test order.
     *
     * @return null
     */
    private function _insertTestOrder()
    {
        $myConfig = oxConfig::getInstance();

        //set order
        $this->_oOrder = oxNew( "oxOrder" );
        $this->_oOrder->setId( '_testOrderId' );
        $this->_oOrder->oxorder__oxshopid = new oxField($myConfig->getShopId(), oxField::T_RAW);
        $this->_oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);
        $this->_oOrder->oxorder__oxbillcountryid = new oxField('10', oxField::T_RAW);
        $this->_oOrder->oxorder__oxdelcountryid = new oxField('11', oxField::T_RAW);
        $this->_oOrder->oxorder__oxdeltype = new oxField('_testDeliverySetId', oxField::T_RAW);
        $this->_oOrder->oxorder__oxdelvat = new oxField('19', oxField::T_RAW);
        $this->_oOrder->oxorder__oxdelcost = new oxField('21', oxField::T_RAW);
        $this->_oOrder->oxorder__oxpaymentid = new oxField('_testPaymentId', oxField::T_RAW);
        $this->_oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel', oxField::T_RAW);
        $this->_oOrder->oxorder__oxpayvat = new oxField('19', oxField::T_RAW);
        $this->_oOrder->oxorder__oxpaycost = new oxField('6', oxField::T_RAW);
        $this->_oOrder->oxorder__oxcardid = new oxField('_testWrappingId', oxField::T_RAW);
        $this->_oOrder->oxorder__oxtotalnetsum = new oxField('12', oxField::T_RAW);
        $this->_oOrder->oxorder__oxtotalbrutsum = new oxField('13', oxField::T_RAW);
        $this->_oOrder->oxorder__oxartvat1  = new oxField('19', oxField::T_RAW);
        $this->_oOrder->oxorder__oxartvatprice1  = new oxField('7', oxField::T_RAW);
        $this->_oOrder->oxorder__oxcurrency = new oxField('1', oxField::T_RAW);
        $this->_oOrder->oxorder__oxdiscount = new oxField('5', oxField::T_RAW);
        $this->_oOrder->oxorder__oxvoucherdiscount = new oxField('6', oxField::T_RAW);
        $this->_oOrder->oxorder__oxwrapvat = new oxField('19', oxField::T_RAW);
        $this->_oOrder->oxorder__oxwrapcost = new oxField('8', oxField::T_RAW);
        $this->_oOrder->oxorder__oxtotalordersum = new oxField('25', oxField::T_RAW);
        $this->_oOrder->save();
    }

    /**
     * Inserts test order articles.
     *
     * @param integer $iStorno canceled product
     *
     * @return null
     */
    private function _insertTestOrderArticle( $iStorno = 0 )
    {
        $oOrderArticle = oxNew( "oxOrderArticle" );
        $oOrderArticle->setId( '_testOrderArticleId' );
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(5, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxvat = new oxField(19, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxvatprice = new oxField(7, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxstorno = new oxField($iStorno, oxField::T_RAW);
        $oOrderArticle->save();

        return $oOrderArticle;
    }

    /**
     * Inserts test article.
     *
     * @return null
     */
    private function _insertTestArticle()
    {
        $oDB = oxDb::getDb();
        $myConfig = oxConfig::getInstance();

        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                    values ('_testArticleId','".$myConfig->getShopId()."','testArticleTitle','2','20','119')";

        $oDB->Execute( $sInsert );
    }

    /**
     * Get test myOrder object.
     *
     * @return null
     */
    private function _getTestMyOrder()
    {
        $this->_insertTestOrder();
        $oMyOrder = $this->getProxyClass( "MyOrder" );
        $oMyOrder->load('_testOrderId');
        $oMyOrder->setNonPublicVar( "_oCur", $oMyOrder->getConfig()->getCurrencyObject( 'EUR' ) );

        return $oMyOrder;
    }

    /**
     * Testing myOrder::getVats()
     *
     * @return null
     */
    public function testGetVatsGetProductVatsReturnsVatsArray()
    {
        // getProductVats returns VATs array
        $aVats = array( 1, 2, 3 );
        $oMyOrder = $this->getMock( "MyOrder", array( "getProductVats" ) );
        $oMyOrder->expects( $this->once() )->method( 'getProductVats')->will( $this->returnValue( $aVats ));

        $this->assertEquals( $aVats, $oMyOrder->getVats() );

        // getProductVats does not return VATs
    }

    /*
     * Testing PdfBlock class
     */

    /**
     * Testing adding variables to chache
     *
     * @return null
     */
    public function testPdfBlock_ToCache()
    {
        $oPdf = $this->getProxyClass( "Pdfblock" );

        $aParams = array( 1, 2 );
        $oPdf->UNITtoCache( 'testFunctionName', $aParams );

        $oItem = new stdClass();
        $oItem->sFunc = 'testFunctionName';
        $oItem->aParams = $aParams;
        $aCache[] = $oItem;

        $this->assertEquals( $aCache, $oPdf->getNonPublicVar('_aCache') );
    }

    /**
     * Testing executing functions from cache - if calls setted function with exact parameters
     *
     * @return null
     */
    public function testPdfBlock_Run()
    {
        $sClassName = oxTestModules::addFunction('PdfBlock', 'getArgsNumber', '{ $this->iArgsNum = count(func_get_args()); }');
        $oPdf = $this->getProxyClass( $sClassName );

        $oItem = new stdClass();
        $oItem->sFunc = 'getArgsNumber';

        $aParams = array();
        for ($n = 1; $n <= 5; $n++ ) {
            $oItem->aParams = $aParams;
            $aCache = array( $oItem );
            $oPdf->setNonPublicVar('_aCache', $aCache);
            $oPdf->run( $oPdf );
            $this->assertEquals( $n - 1, $oPdf->iArgsNum );
            $aParams[] = $n;
        }
    }

    /**
     * Testing setting pdf function and params
     *
     * @return null
     */
    public function testPdfBlock_line()
    {
        $oPdf = $this->getProxyClass( "Pdfblock" );
        $oPdf->line( 1, 2, 3, 4 );

        $oItem = new stdClass();
        $oItem->sFunc = 'Line';
        $oItem->aParams = array( 1, 2, 3, 4 );

        $aCache = $oPdf->getNonPublicVar('_aCache' );
        $this->assertEquals( $oItem, $aCache[0] );
    }

    /**
     * Testing setting pdf function and params
     *
     * @return null
     */
    public function testPdfBlock_text()
    {
        $oPdf = $this->getProxyClass( "Pdfblock" );
        $oPdf->text( 1, 2, 3 );

        $oItem = new stdClass();
        $oItem->sFunc = 'Text';
        $oItem->aParams = array( 1, 2, 3 );

        $aCache = $oPdf->getNonPublicVar('_aCache' );
        $this->assertEquals( $oItem, $aCache[0] );
    }

    /**
     * Testing setting pdf function and params
     *
     * @return null
     */
    public function testPdfBlock_font()
    {
        $oPdf = $this->getProxyClass( "Pdfblock" );
        $oPdf->font( 1, 2, 3 );

        $oItem = new stdClass();
        $oItem->sFunc = 'SetFont';
        $oItem->aParams = array( 1, 2, 3 );

        $aCache = $oPdf->getNonPublicVar('_aCache' );
        $this->assertEquals( $oItem, $aCache[0] );
    }

    /**
     * Testing function ajustHeight updates "line" function parameters
     *
     * @return null
     */
    public function testPdfBlock_ajustHeightWithLineCommand()
    {
        $oPdf = $this->getProxyClass( "Pdfblock" );
        $oPdf->line( 1, 1, 1, 1 );
        $oPdf->ajustHeight( 3 );

        $aCache = $oPdf->getNonPublicVar('_aCache' );
        $this->assertEquals( array(1, 4, 1, 4), $aCache[0]->aParams );
    }

    /**
     * Testing function ajustHeight updates "text" function parameters
     *
     * @return null
     */
    public function testPdfBlock_ajustHeightWithTextCommand()
    {
        $oPdf = $this->getProxyClass( "Pdfblock" );
        $oPdf->text( 1, 1, 1 );
        $oPdf->ajustHeight( 3 );

        $aCache = $oPdf->getNonPublicVar('_aCache' );
        $this->assertEquals( array(1, 4, 1), $aCache[0]->aParams );
    }


    /**
     * Testing pdfArticleSummary class
     */

    /**
     * Testing constructor
     *
     * @return null
     */
    public function testPdfArticleSummary_construct()
    {
        $oPdf = $this->getProxyClass( "PdfArticleSummary", array(1,2) );

        $this->assertEquals( 1, $oPdf->getNonPublicVar('_oData') );
        $this->assertEquals( 2, $oPdf->getNonPublicVar('_oPdf') );
    }


    /*
     * Testing pdfArticleSummary class
     * Admin mode is off, so while testing generated pdf, there will be no translations,
     * only translation constants (because not in admin mode)
     *
     */

    /**
     * Testing method _setTotalCostsWithoutDiscount
     *
     * @return null
     */
    public function testPdfArticleSummary_setTotalCostsWithoutDiscount()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $sClass = oxTestModules::addFunction('PdfArticleSummary', 'getNonPublicVar( $sName )', '{return $this->$sName;}');
        $sClass = oxTestModules::addFunction($sClass, 'p_setTotalCostsWithoutDiscount( &$iStartPos )', '{return $this->_setTotalCostsWithoutDiscount( $iStartPos );}');
        $oPdf = new testPdfClass;
        $oPdfArtSum = new $sClass( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->p_setTotalCostsWithoutDiscount( $iStartPos );

        $aCache = $oPdfArtSum->getNonPublicVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ALLPRICENETTO', $aCache[1]->aParams[2] );
        $this->assertEquals( '12,00 EUR', trim($aCache[2]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[3]->aParams[2] );
        $this->assertEquals( '7,00 EUR', trim($aCache[4]->aParams[2]) );
    }

    /**
     * Testing method _setTotalCostsWithDiscount
     *
     * @return null
     */
    public function testPdfArticleSummary_setTotalCostsWithDiscount()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setTotalCostsWithDiscount( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO', $aCache[1]->aParams[2] );
        $this->assertEquals( '13,00 EUR', trim($aCache[2]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_DISCOUNT', $aCache[4]->aParams[2] );
        $this->assertEquals( '-5,00 EUR', trim($aCache[5]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ALLPRICENETTO', $aCache[7]->aParams[2] );
        $this->assertEquals( '12,00 EUR', trim($aCache[8]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[9]->aParams[2] );
        $this->assertEquals( '7,00 EUR', trim($aCache[10]->aParams[2]) );
    }

    /**
     * Testing method _setVoucherInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setVoucherInfo()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setVoucherInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_VOUCHER', $aCache[0]->aParams[2] );
        $this->assertEquals( '-6,00 EUR', trim($aCache[1]->aParams[2]) );
    }

    /**
     * Testing method _setDeliveryInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setDeliveryInfo()
    {
        modConfig::getInstance()->setConfigParam( 'blCalcVATForDelivery', 1 );
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setDeliveryInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');


        //checking values
        /*$this->assertEquals( 'ORDER_OVERVIEW_PDF_SHIPCOST', $aCache[0]->aParams[2] );
        $this->assertEquals( '21,00 EUR', trim($aCache[1]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[2]->aParams[2] );
        $this->assertEquals( '3,35 EUR', trim($aCache[3]->aParams[2]) );*/

        $this->assertEquals( 'ORDER_OVERVIEW_PDF_SHIPCOST', trim($aCache[0]->aParams[2]) );
        $this->assertEquals( '21,00 EUR', trim($aCache[1]->aParams[2]) );
    }

    /**
     * Testing method _setWrappingInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setWrappingInfo()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setWrappingInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'WRAPPING_COSTS ORDER_OVERVIEW_PDF_BRUTTO', $aCache[0]->aParams[2] );
        $this->assertEquals( '8,00 EUR', trim($aCache[1]->aParams[2]) );
        /*$this->assertEquals( 'ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[2]->aParams[2] );
        $this->assertEquals( '1,28 EUR', trim($aCache[3]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_WRAPPING ORDER_OVERVIEW_PDF_BRUTTO', trim($aCache[5]->aParams[2]) );
        $this->assertEquals( '8,00 EUR', trim($aCache[6]->aParams[2]) );*/
    }

    /**
     * Testing method _setWrappingInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setWrappingInfo_WithGiftCardOnly()
    {
        $oMyOrder = $this->_getTestMyOrder();
        $oMyOrder->oxorder__oxwrapvat = new oxField('0', oxField::T_RAW);
        $oMyOrder->oxorder__oxwrapcost = new oxField('0', oxField::T_RAW);
        $oMyOrder->oxorder__oxgiftcardvat = new oxField('19', oxField::T_RAW);
        $oMyOrder->oxorder__oxgiftcardcost = new oxField('8', oxField::T_RAW);

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setWrappingInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'GIFTCARD_COSTS ORDER_OVERVIEW_PDF_BRUTTO', $aCache[0]->aParams[2] );
        $this->assertEquals( '8,00 EUR', trim($aCache[1]->aParams[2]) );
    }

    /**
     * Testing method _setPaymentInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setPaymentInfo()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setPaymentInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_PAYMENTIMPACT', $aCache[0]->aParams[2] );
        $this->assertEquals( '6,00 EUR', trim($aCache[1]->aParams[2]) );
       /* $this->assertEquals( 'ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[2]->aParams[2] );
        $this->assertEquals( '0,96 EUR', trim($aCache[3]->aParams[2]) );
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_PAYMENTIMPACT', trim($aCache[4]->aParams[2]) );
        $this->assertEquals( '6,00 EUR', trim($aCache[5]->aParams[2]) );*/
    }

    /**
     * Testing method _setGrandTotalPriceInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setGrandTotalPriceInfo()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setGrandTotalPriceInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_ALLSUM', $aCache[1]->aParams[2] );
        $this->assertEquals( '25,00 EUR', trim($aCache[2]->aParams[2]) );
    }

    /**
     * Testing method _setPaymentMethodInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setPaymentMethodInfo()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setPaymentMethodInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_SELPAYMENTNachnahme', $aCache[1]->aParams[2] );
    }

    /**
     * Testing method _setPaymentMethodInfo in not delfault language
     *
     * @return null
     */
    public function testPdfArticleSummary_setPaymentMethodInfoInOtherLang()
    {
        $oMyOrder = $this->_getTestMyOrder();
        $oMyOrder->setNonPublicVar( '_iSelectedLang', 1 );

        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setPaymentMethodInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_SELPAYMENTCOD (Cash on Delivery)', $aCache[1]->aParams[2] );
    }

    /**
     * Testing method _setPayUntilInfo
     *
     * @return null
     */
    public function testPdfArticleSummary_setPayUntilInfo()
    {
        $oMyOrder = $this->_getTestMyOrder();
        $oMyOrder->oxorder__oxbilldate = new oxField('2000-01-01', oxField::T_RAW);
        
        $oPdf = new testPdfClass;
        $oPdfArtSum = new myOrder_PdfArticleSummary( $oMyOrder, $oPdf );

        $iStartPos = 1;
        $oPdfArtSum->setPayUntilInfo( $iStartPos );

        $aCache = $oPdfArtSum->getVar('_aCache');

        //checking values
        $this->assertEquals( 'ORDER_OVERVIEW_PDF_PAYUPTO'. '08.01.2000', $aCache[1]->aParams[2] );
    }

    /**
     * Testing method generate
     *
     * @return null
     */
    public function testPdfArticleSummary_generate()
    {
        $oMyOrder = $this->_getTestMyOrder();

        $oPdf = new testPdfClass;

        $aFunctions = array( '_setTotalCostsWithDiscount', '_setVoucherInfo', '_setDeliveryInfo', '_setWrappingInfo', '_setPaymentInfo', '_setGrandTotalPriceInfo', '_setPaymentMethodInfo', '_setPayUntilInfo');
        $oPdfArtSum = $this->getMock( 'pdfArticleSummary', $aFunctions, array($oMyOrder, $oPdf) );
        $oPdfArtSum->expects( $this->once() )->method( '_setTotalCostsWithDiscount');
        $oPdfArtSum->expects( $this->once() )->method( '_setVoucherInfo');
        $oPdfArtSum->expects( $this->once() )->method( '_setDeliveryInfo');
        $oPdfArtSum->expects( $this->once() )->method( '_setWrappingInfo');
        $oPdfArtSum->expects( $this->once() )->method( '_setPaymentInfo');
        $oPdfArtSum->expects( $this->once() )->method( '_setGrandTotalPriceInfo');
        $oPdfArtSum->expects( $this->once() )->method( '_setPaymentMethodInfo');
        $oPdfArtSum->expects( $this->once() )->method( '_setPayUntilInfo');

        $oPdfArtSum->generate( 1 );
    }

    /**
     * Testing method generate when order is without discount
     *
     * @return null
     */
    public function testPdfArticleSummary_generateWithoutDiscount()
    {
        $oMyOrder = $this->_getTestMyOrder();
        $oMyOrder->oxorder__oxdiscount->value = null;
        //$oMyOrder->setNonPublicVar( '_oData', $oData );

        $oPdf = new testPdfClass;

        $aFunctions = array( '_setTotalCostsWithoutDiscount', '_setTotalCostsWithDiscount' );
        $oPdfArtSum = $this->getMock( 'pdfArticleSummary', $aFunctions, array($oMyOrder, $oPdf) );

        $oPdfArtSum->expects( $this->once() )->method( '_setTotalCostsWithoutDiscount');
        $oPdfArtSum->expects( $this->never() )->method( '_setTotalCostsWithDiscount');

        $oPdfArtSum->generate( 1 );
    }

    /*
     * Testing muOrder class
     * Admin mode is off, so while testing generated pdf, there will be no translations,
     * only translation constatns
     */

    /**
     * Testing method _getActShop
     *
     * @return null
     */
    public function testMyOrder_getActShop()
    {
        $sShopId = 'oxbaseshop';

        $oMyOrder = $this->getProxyClass( "MyOrder" );
        $oShop = $oMyOrder->UNITgetActShop();

        $this->assertEquals( $sShopId, $oShop->getId() );
    }

    /**
     * Testing translate method
     *
     * @return null
     */
    public function testMyOrder_translate()
    {
        $oMyOrder =  new MyOrder();

        $oMyOrder->setSelectedLang( 1 );
        $this->setAdminMode( true );

        $this->assertEquals( 'phone: ', $oMyOrder->translate('ORDER_OVERVIEW_PDF_PHONE') );
    }

    /**
     * Testing genPdf method - generating standart pdf
     *
     * @return null
     */
    public function testMyOrder_genPdfStandart()
    {
        $this->_insertTestOrder();

        oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

        $oMyOrder = $this->getMock( 'myOrder', array('pdfHeader', 'exportStandart', 'pdfFooter') );
        $oMyOrder->expects( $this->once() )->method( 'pdfHeader');
        $oMyOrder->expects( $this->once() )->method( 'exportStandart');
        $oMyOrder->expects( $this->once() )->method( 'pdfFooter');

        $oMyOrder->load('_testOrderId');
        $oMyOrder->genPdf( 'testfilename', 1 );
    }

    /**
     * Testing genPdf method - generating standart pdf and counting number of generated pages.
     *
     * @return null
     */
    public function testMyOrder_genPdfStandartCountingNumberOfGeneratedPages()
    {
        $this->_insertTestOrder();

        for ( $i = 0; $i < 80; $i++ ) {
            $this->_insertTestOrderArticle();

            $oOrderArticle = oxNew( 'oxOrderArticle' );
            if ( $oOrderArticle->load( '_testOrderArticleId' ) ) {
                $oOrderArticle->setId( '_testOrderArticleId'.$i );
                $oOrderArticle->save();
            }
        }

        oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

        $oMyOrder = $this->getMock( 'myOrder', array( 'pdfHeader' ) );
        $oMyOrder->expects( $this->exactly( 3 ) )->method( 'pdfHeader');
        $oMyOrder->load('_testOrderId');

        $oMyOrder->genPdf( 'testfilename', 1 );
    }

    /**
     * Testing genPdf method - generating delivery note pdf
     *
     * @return null
     */
    public function testMyOrder_genPdfDeliveryNote()
    {
        $this->_insertTestOrder();

        oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

        $oMyOrder = $this->getMock( 'myOrder', array('pdfHeader', 'exportDeliveryNote', 'pdfFooter') );
        $oMyOrder->expects( $this->once() )->method( 'pdfHeader');
        $oMyOrder->expects( $this->once() )->method( 'exportDeliveryNote');
        $oMyOrder->expects( $this->once() )->method( 'pdfFooter');

        $oMyOrder->load('_testOrderId');
        modConfig::setParameter( 'pdftype', 'dnote' );
        $oMyOrder->genPdf( 'testfilename', 1 );
    }

    /**
     * Testing genPdf method - adding invoice number
     *
     * @return null
     */
    public function testMyOrder_genPdfSettingInvoiceNr()
    {
        $this->_insertTestOrder();

        oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

        $oMyOrder = $this->getMock( 'myOrder', array('getNextBillNum') );
        $oMyOrder->expects( $this->once() )->method( 'getNextBillNum')->will( $this->returnValue( 'testInvoiceNr' ));

        $oMyOrder->load( '_testOrderId' );
        $oMyOrder->genPdf( 'testfilename', 1 );

        $this->assertEquals( 'testInvoiceNr', $oMyOrder->oxorder__oxbillnr->value );
    }

    /**
     * Testing exportStandart method - calling needed methods
     *
     * @return null
     */
    public function testMyOrder_exportStandart()
    {
        $this->_insertTestOrder();

        $oPdf = new oxPdf;

        $oMyOrder = $this->getMock( 'myOrder', array('_setBillingAddressToPdf', '_setDeliveryAddressToPdf', '_setOrderArticlesToPdf') );
        $oMyOrder->expects( $this->once() )->method( '_setBillingAddressToPdf');
        $oMyOrder->expects( $this->never() )->method( '_setDeliveryAddressToPdf');
        $oMyOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

        $oMyOrder->load('_testOrderId');
        $oMyOrder->exportStandart( $oPdf );
    }

    /**
     * Testing exportStandart method - when order is canceled
     *
     * @return null
     */
    public function testMyOrder_exportStandartWhenOrderIsCanceled()
    {
        // marking order article as variant ..
        $oSelVariantField = $this->getMock( 'oxfield', array( '__get' ) );
        $oSelVariantField->expects( $this->once() )->method( '__get');

        $this->_insertTestOrder();
        $oArticle = $this->_insertTestOrderArticle();
        $oArticle->oxorderarticles__oxtitle = new oxField("testtitle");
        $oArticle->oxorderarticles__oxselvariant = $oSelVariantField;

        $oPdf = new oxPdf;

        $oMyOrder = $this->getMock( "myOrder", array( "getOrderArticles" ) );
        $oMyOrder->expects( $this->any() )->method( 'getOrderArticles')->will( $this->returnValue( array( $oArticle->getId() => $oArticle ) ) );
        $oMyOrder->load('_testOrderId');

        //
        $oMyOrder->oxorder__oxdelcost = $this->getMock( 'oxfield', array( 'setValue' ) );
        $oMyOrder->oxorder__oxdelcost->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( 0 ) );

        $oMyOrder->oxorder__oxpaycost = $this->getMock( 'oxfield', array( 'setValue' ) );
        $oMyOrder->oxorder__oxpaycost->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( 0 ) );

        $oMyOrder->oxorder__oxordernr = $this->getMock( 'oxfield', array( 'setValue' ) );
        $oMyOrder->oxorder__oxordernr->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( '   ORDER_OVERVIEW_PDF_STORNO' ), $this->equalTo( 2 ) );

        // marking as canceled
        $oMyOrder->oxorder__oxstorno = new oxField( 1 );

        $oMyOrder->exportStandart( $oPdf );
    }

    /**
     * Testing exportStandart method - calling needed methods when delivery address is setted
     *
     * @return null
     */
    public function testMyOrder_exportStandart_WithDeliveryAddress()
    {
        $this->_insertTestOrder();

        $oPdf = new oxPdf;

        $oMyOrder = $this->getMock( 'myOrder', array('_setBillingAddressToPdf', '_setDeliveryAddressToPdf', '_setOrderArticlesToPdf') );
        $oMyOrder->expects( $this->once() )->method( '_setBillingAddressToPdf');
        $oMyOrder->expects( $this->once() )->method( '_setDeliveryAddressToPdf');
        $oMyOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

        $oMyOrder->load('_testOrderId');
        $oMyOrder->oxorder__oxdelsal = new oxField('1', oxField::T_RAW);
        $oMyOrder->exportStandart( $oPdf );
    }

    /**
     * Testing exportStandart method - setting order currency
     *
     * @return null
     */
    public function testMyOrder_exportStandart_SettingCurrency()
    {
        $this->_insertTestOrder();

        $oPdf = new oxPdf;
        $oMyOrder = $this->getProxyClass( "MyOrder" );

        $oMyOrder->load('_testOrderId');
        $oMyOrder->oxorder__oxdelsal = new oxField( "testSal" );

        $oCur = $oMyOrder->getConfig()->getCurrencyObject( 'EUR' );
        $oMyOrder->exportStandart( $oPdf );

        $this->assertEquals( $oCur, $oMyOrder->getNonPublicVar('_oCur') );
    }

    /**
     * Testing exportDeliveryNote method - calling needed methods
     *
     * @return null
     */
    public function testMyOrder_exportDeliveryNote()
    {
        $this->_insertTestOrder();

        $oPdf = new oxPdf;

        $oMyOrder = $this->getMock( 'myOrder', array('_setBillingAddressToPdf', '_setOrderArticlesToPdf') );
        $oMyOrder->expects( $this->never() )->method( '_setBillingAddressToPdf');
        $oMyOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

        $oMyOrder->load('_testOrderId');
        $oMyOrder->oxorder__oxdelsal = new oxField( '1', oxField::T_RAW );
        $oMyOrder->exportDeliveryNote( $oPdf );
    }

    /**
     * Testing exportDeliveryNote method - when order is canceled.
     *
     * @return null
     */
    public function testMyOrder_exportDeliveryNoteWhenOrderIsCanceled()
    {
        $this->_insertTestOrder();

        $oPdf = new oxPdf;

        $oMyOrder = new myOrder();
        $oMyOrder->load('_testOrderId');

        //
        $oMyOrder->oxorder__oxdelcost = $this->getMock( 'oxfield', array( 'setValue' ) );
        $oMyOrder->oxorder__oxdelcost->expects( $this->never() )->method( 'setValue');

        $oMyOrder->oxorder__oxpaycost = $this->getMock( 'oxfield', array( 'setValue' ) );
        $oMyOrder->oxorder__oxpaycost->expects( $this->never() )->method( 'setValue');

        $oMyOrder->oxorder__oxordernr = $this->getMock( 'oxfield', array( 'setValue' ) );
        $oMyOrder->oxorder__oxordernr->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( '   ORDER_OVERVIEW_PDF_STORNO' ), $this->equalTo( 2 ) );

        // marking as canceled
        $oMyOrder->oxorder__oxstorno = new oxField( 1 );

        $oMyOrder->exportDeliveryNote( $oPdf );
    }

    /**
     * Testing exportDeliveryNote method - uses billing address info
     * if delivery address is not setted
     *
     * @return null
     */
    public function testMyOrder_exportDeliveryNote_WithoutDeliveryAddress()
    {
        $this->_insertTestOrder();

        $oPdf = new oxPdf;

        $oMyOrder = $this->getMock( 'myOrder', array('_setBillingAddressToPdf', '_setOrderArticlesToPdf') );
        $oMyOrder->expects( $this->once() )->method( '_setBillingAddressToPdf');
        $oMyOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

        $oMyOrder->load('_testOrderId');
        $oMyOrder->oxorder__oxdelsal = new oxField( null, oxField::T_RAW );
        $oMyOrder->exportDeliveryNote( $oPdf );
    }

    /**
     * Testing _replaceExtendedChars method
     *
     * @return null
     */
    public function testMyOrder_replaceExtendedChars()
    {
        $oMyOrder = $this->getProxyClass( "MyOrder" );

        $sInput = " &euro; &copy; &quot; &#039; &#97; &#98; some text";
        $sStr = $oMyOrder->UNITreplaceExtendedChars($sInput, true);
        $this->assertEquals( " " . chr(128) . " " . chr(169) . " \" ' a b some text", $sStr );
    }

    /**
     * Testing getProductVats method
     *
     * @return null
     */
    public function testMyOrder_getProductVats()
    {
        $oMyOrder = $this->getProxyClass( "MyOrder" );

        $oMyOrder->oxorder__oxartvat1 = new oxField('19', oxField::T_RAW);
        $oMyOrder->oxorder__oxartvatprice1 = new oxField('9', oxField::T_RAW);

        $this->assertEquals( array("19"=>9), $oMyOrder->getProductVats(false) );
    }

    /**
     * Testing getCurrency method
     *
     * @return null
     */
    public function testMyOrder_getCurrency()
    {
        $oMyOrder = $this->getProxyClass( "MyOrder" );
        $oMyOrder->setNonPublicVar( '_oCur', 5 );

        $this->assertEquals( 5, $oMyOrder->getCurrency() );
    }

    /**
     * Testing getSelectedLang method
     *
     * @return null
     */
    public function testMyOrder_getSelectedLang()
    {
        $oMyOrder = $this->getProxyClass( "MyOrder" );
        $oMyOrder->setNonPublicVar( '_iSelectedLang', 1 );

        $this->assertEquals( 1, $oMyOrder->getSelectedLang() );
    }

    /**
     * Data provider for testMyOrder_getPaymentTerm
     *
     * @return array
     */
    public function getPaymentDataProvider()
    {
        return array( array(null, 7), array(10, 10), array(0, 0) );
    }

    /**
     * Testing getPaymentTerm() method
     * getPaymentTerm() method returns config param iPaymentTerm, default value is 7;
     *
     * @dataProvider getPaymentDataProvider
     */
    public function testMyOrder_getPaymentTerm($param, $expect)
    {
        $oMyOrder = new MyOrder();
        oxConfig::getInstance()->setConfigParam('iPaymentTerm', $param);

        $this->assertEquals( $expect, $oMyOrder->getPaymentTerm() );
    }
}
