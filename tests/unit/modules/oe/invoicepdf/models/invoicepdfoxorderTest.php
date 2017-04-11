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
* InvoicepdfOxOrder parrent chain class.
*/
class InvoicepdfOxOrder_parent extends oxOrder {}

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

require_once getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfoxorder.php';
require_once getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfblock.php';
require_once getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfarticlesummary.php';
require_once getShopBasePath() . 'core/oxpdf.php';

/**
* InvoicepdfOxOrder parrent chain class.
*/
class New_InvoicepdfArticleSummary extends InvoicepdfArticleSummary
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
class Unit_Modules_Oe_Invoicepdf_Models_InvoicePdfOxOrderTest extends OxidTestCase
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
    $myConfig = oxRegistry::getConfig();

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
    $myConfig = oxRegistry::getConfig();

    $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                values ('_testArticleId','".$myConfig->getShopId()."','testArticleTitle','2','20','119')";

    $oDB->Execute( $sInsert );
}

/**
 * Get test InvoicepdfOxOrder object.
 *
 * @return null
 */
private function _getTestInvoicepdfOxOrder()
{
    $this->_insertTestOrder();
    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );
    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->setNonPublicVar( "_oCur", $oInvoicepdfOxOrder->getConfig()->getCurrencyObject( 'EUR' ) );

    return $oInvoicepdfOxOrder;
}

/**
 * Testing InvoicepdfOxOrder::getVats()
 *
 * @return null
 */
public function testGetVatsGetProductVatsReturnsVatsArray()
{
    // getProductVats returns VATs array
    $aVats = array( 1, 2, 3 );
    $oInvoicepdfOxOrder = $this->getMock( "InvoicepdfOxOrder", array( "getProductVats" ) );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'getProductVats')->will( $this->returnValue( $aVats ));

    $this->assertEquals( $aVats, $oInvoicepdfOxOrder->getVats() );

    // getProductVats does not return VATs
}

/*
 * Testing InvoicepdfBlock class
 */

/**
 * Testing adding variables to chache
 *
 * @return null
 */
public function testInvoicepdfBlock_ToCache()
{
    $oPdf = $this->getProxyClass( "InvoicepdfBlock" );

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
public function testInvoicepdfBlock_Run()
{
    $sClassName = oxTestModules::addFunction('InvoicepdfBlock', 'getArgsNumber', '{ $this->iArgsNum = count(func_get_args()); }');
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
public function testInvoicepdfBlock_line()
{
    $oPdf = $this->getProxyClass( "InvoicepdfBlock" );
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
public function testInvoicepdfBlock_text()
{
    $oPdf = $this->getProxyClass( "InvoicepdfBlock" );
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
public function testInvoicepdfBlock_font()
{
    $oPdf = $this->getProxyClass( "InvoicepdfBlock" );
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
public function testInvoicepdfBlock_ajustHeightWithLineCommand()
{
    $oPdf = $this->getProxyClass( "InvoicepdfBlock" );
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
public function testInvoicepdfBlock_ajustHeightWithTextCommand()
{
    $oPdf = $this->getProxyClass( "InvoicepdfBlock" );
    $oPdf->text( 1, 1, 1 );
    $oPdf->ajustHeight( 3 );

    $aCache = $oPdf->getNonPublicVar('_aCache' );
    $this->assertEquals( array(1, 4, 1), $aCache[0]->aParams );
}


/**
 * Testing InvoicepdfArticleSummary class
 */

/**
 * Testing constructor
 *
 * @return null
 */
public function testInvoicepdfArticleSummary_construct()
{
    $oPdf = $this->getProxyClass( "InvoicepdfArticleSummary", array(1,2) );

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
public function testInvoicepdfArticleSummary_setTotalCostsWithoutDiscount()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $sClass = oxTestModules::addFunction('InvoicepdfArticleSummary', 'getNonPublicVar( $sName )', '{return $this->$sName;}');
    $sClass = oxTestModules::addFunction($sClass, 'p_setTotalCostsWithoutDiscount( &$iStartPos )', '{return $this->_setTotalCostsWithoutDiscount( $iStartPos );}');
    $oPdf = new testPdfClass;
    $oPdfArtSum = new $sClass( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setTotalCostsWithDiscount()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setVoucherInfo()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setDeliveryInfo()
{
    modConfig::getInstance()->setConfigParam( 'blCalcVATForDelivery', 1 );
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setWrappingInfo()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
    $oMyOrder = $this->_getTestInvoicepdfOxOrder();
    $oMyOrder->oxorder__oxwrapvat = new oxField('0', oxField::T_RAW);
    $oMyOrder->oxorder__oxwrapcost = new oxField('0', oxField::T_RAW);
    $oMyOrder->oxorder__oxgiftcardvat = new oxField('19', oxField::T_RAW);
    $oMyOrder->oxorder__oxgiftcardcost = new oxField('8', oxField::T_RAW);

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oMyOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setPaymentInfo()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setGrandTotalPriceInfo()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setPaymentMethodInfo()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setPaymentMethodInfoInOtherLang()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();
    $oInvoicepdfOxOrder->setNonPublicVar( '_iSelectedLang', 1 );

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_setPayUntilInfo()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();
    $oInvoicepdfOxOrder->oxorder__oxbilldate = new oxField('2000-01-01', oxField::T_RAW);

    $oPdf = new testPdfClass;
    $oPdfArtSum = new New_InvoicepdfArticleSummary( $oInvoicepdfOxOrder, $oPdf );

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
public function testInvoicepdfArticleSummary_generate()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();

    $oPdf = new testPdfClass;

    $aFunctions = array( '_setTotalCostsWithDiscount', '_setVoucherInfo', '_setDeliveryInfo', '_setWrappingInfo', '_setPaymentInfo', '_setGrandTotalPriceInfo', '_setPaymentMethodInfo', '_setPayUntilInfo');
    $oPdfArtSum = $this->getMock( 'InvoicepdfArticleSummary', $aFunctions, array($oInvoicepdfOxOrder, $oPdf) );
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
public function testInvoicepdfArticleSummary_generateWithoutDiscount()
{
    $oInvoicepdfOxOrder = $this->_getTestInvoicepdfOxOrder();
    $oInvoicepdfOxOrder->oxorder__oxdiscount->value = null;
    //$oInvoicepdfOxOrder->setNonPublicVar( '_oData', $oData );

    $oPdf = new testPdfClass;

    $aFunctions = array( '_setTotalCostsWithoutDiscount', '_setTotalCostsWithDiscount' );
    $oPdfArtSum = $this->getMock( 'InvoicepdfArticleSummary', $aFunctions, array($oInvoicepdfOxOrder, $oPdf) );

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
public function testInvoicepdfOxOrder_getActShop()
{
    $sShopId = 'oxbaseshop';

    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );
    $oShop = $oInvoicepdfOxOrder->UNITgetActShop();

    $this->assertEquals( $sShopId, $oShop->getId() );
}

/**
 * Testing translate method
 *
 * @return null
 */
public function testInvoicepdfOxOrder_translate()
{
    $oInvoicepdfOxOrder =  new InvoicepdfOxOrder();

    $oInvoicepdfOxOrder->setSelectedLang( 1 );
    $this->setAdminMode( true );

    $this->assertEquals( 'phone: ', $oInvoicepdfOxOrder->translate('ORDER_OVERVIEW_PDF_PHONE') );
}

/**
 * Testing genPdf method - generating standart pdf
 *
 * @return null
 */
public function testInvoicepdfOxOrder_genPdfStandart()
{
    $this->_insertTestOrder();

    oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('pdfHeader', 'exportStandart', 'pdfFooter') );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'pdfHeader');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'exportStandart');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'pdfFooter');

    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->genPdf( 'testfilename', 1 );
}

/**
 * Testing genPdf method - generating standart pdf and counting number of generated pages.
 *
 * @return null
 */
public function testInvoicepdfOxOrder_genPdfStandartCountingNumberOfGeneratedPages()
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

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array( 'pdfHeader' ) );
    $oInvoicepdfOxOrder->expects( $this->exactly( 3 ) )->method( 'pdfHeader');
    $oInvoicepdfOxOrder->load('_testOrderId');

    $oInvoicepdfOxOrder->genPdf( 'testfilename', 1 );
}

/**
 * Testing genPdf method - generating delivery note pdf
 *
 * @return null
 */
public function testInvoicepdfOxOrder_genPdfDeliveryNote()
{
    $this->_insertTestOrder();

    oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('pdfHeader', 'exportDeliveryNote', 'pdfFooter') );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'pdfHeader');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'exportDeliveryNote');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'pdfFooter');

    $oInvoicepdfOxOrder->load('_testOrderId');
    modConfig::setRequestParameter( 'pdftype', 'dnote' );
    $oInvoicepdfOxOrder->genPdf( 'testfilename', 1 );
}

/**
 * Testing genPdf method - adding invoice number
 *
 * @return null
 */
public function testInvoicepdfOxOrder_genPdfSettingInvoiceNr()
{
    $this->_insertTestOrder();

    oxTestModules::addFunction( "oxPdf", "output", "{return '';}" );

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('getNextBillNum') );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( 'getNextBillNum')->will( $this->returnValue( 'testInvoiceNr' ));

    $oInvoicepdfOxOrder->load( '_testOrderId' );
    $oInvoicepdfOxOrder->genPdf( 'testfilename', 1 );

    $this->assertEquals( 'testInvoiceNr', $oInvoicepdfOxOrder->oxorder__oxbillnr->value );
}

/**
 * Testing exportStandart method - calling needed methods
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportStandart()
{
    $this->_insertTestOrder();

    $oPdf = new oxPdf;

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setDeliveryAddressToPdf', '_setOrderArticlesToPdf') );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setBillingAddressToPdf');
    $oInvoicepdfOxOrder->expects( $this->never() )->method( '_setDeliveryAddressToPdf');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->exportStandart( $oPdf );
}

/**
 * Testing exportStandart method - when order is canceled
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportStandartWhenOrderIsCanceled()
{
    // marking order article as variant ..
    $oSelVariantField = $this->getMock( 'oxfield', array( '__get' ) );
    $oSelVariantField->expects( $this->once() )->method( '__get');

    $this->_insertTestOrder();
    $oArticle = $this->_insertTestOrderArticle();
    $oArticle->oxorderarticles__oxtitle = new oxField("testtitle");
    $oArticle->oxorderarticles__oxselvariant = $oSelVariantField;

    $oPdf = new oxPdf;

    $oInvoicepdfOxOrder = $this->getMock( "InvoicepdfOxOrder", array( "getOrderArticles" ) );
    $oInvoicepdfOxOrder->expects( $this->any() )->method( 'getOrderArticles')->will( $this->returnValue( array( $oArticle->getId() => $oArticle ) ) );
    $oInvoicepdfOxOrder->load('_testOrderId');

    //
    $oInvoicepdfOxOrder->oxorder__oxdelcost = $this->getMock( 'oxfield', array( 'setValue' ) );
    $oInvoicepdfOxOrder->oxorder__oxdelcost->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( 0 ) );

    $oInvoicepdfOxOrder->oxorder__oxpaycost = $this->getMock( 'oxfield', array( 'setValue' ) );
    $oInvoicepdfOxOrder->oxorder__oxpaycost->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( 0 ) );

    $oInvoicepdfOxOrder->oxorder__oxordernr = $this->getMock( 'oxfield', array( 'setValue' ) );
    $oInvoicepdfOxOrder->oxorder__oxordernr->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( '   ORDER_OVERVIEW_PDF_STORNO' ), $this->equalTo( 2 ) );

    // marking as canceled
    $oInvoicepdfOxOrder->oxorder__oxstorno = new oxField( 1 );

    $oInvoicepdfOxOrder->exportStandart( $oPdf );
}

/**
 * Testing exportStandart method - calling needed methods when delivery address is setted
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportStandart_WithDeliveryAddress()
{
    $this->_insertTestOrder();

    $oPdf = new oxPdf;

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setDeliveryAddressToPdf', '_setOrderArticlesToPdf') );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setBillingAddressToPdf');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setDeliveryAddressToPdf');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->oxorder__oxdelsal = new oxField('1', oxField::T_RAW);
    $oInvoicepdfOxOrder->exportStandart( $oPdf );
}

/**
 * Testing exportStandart method - setting order currency
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportStandart_SettingCurrency()
{
    $this->_insertTestOrder();

    $oPdf = new oxPdf;
    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );

    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->oxorder__oxdelsal = new oxField( "testSal" );

    $oCur = $oInvoicepdfOxOrder->getConfig()->getCurrencyObject( 'EUR' );
    $oInvoicepdfOxOrder->exportStandart( $oPdf );

    $this->assertEquals( $oCur, $oInvoicepdfOxOrder->getNonPublicVar('_oCur') );
}

/**
 * Testing exportDeliveryNote method - calling needed methods
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportDeliveryNote()
{
    $this->_insertTestOrder();

    $oPdf = new oxPdf;

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setOrderArticlesToPdf') );
    $oInvoicepdfOxOrder->expects( $this->never() )->method( '_setBillingAddressToPdf');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->oxorder__oxdelsal = new oxField( '1', oxField::T_RAW );
    $oInvoicepdfOxOrder->exportDeliveryNote( $oPdf );
}

/**
 * Testing exportDeliveryNote method - when order is canceled.
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportDeliveryNoteWhenOrderIsCanceled()
{
    $this->_insertTestOrder();

    $oPdf = new oxPdf;

    $oInvoicepdfOxOrder = new InvoicepdfOxOrder();
    $oInvoicepdfOxOrder->load('_testOrderId');

    //
    $oInvoicepdfOxOrder->oxorder__oxdelcost = $this->getMock( 'oxfield', array( 'setValue' ) );
    $oInvoicepdfOxOrder->oxorder__oxdelcost->expects( $this->never() )->method( 'setValue');

    $oInvoicepdfOxOrder->oxorder__oxpaycost = $this->getMock( 'oxfield', array( 'setValue' ) );
    $oInvoicepdfOxOrder->oxorder__oxpaycost->expects( $this->never() )->method( 'setValue');

    $oInvoicepdfOxOrder->oxorder__oxordernr = $this->getMock( 'oxfield', array( 'setValue' ) );
    $oInvoicepdfOxOrder->oxorder__oxordernr->expects( $this->once() )->method( 'setValue')->with( $this->equalTo( '   ORDER_OVERVIEW_PDF_STORNO' ), $this->equalTo( 2 ) );

    // marking as canceled
    $oInvoicepdfOxOrder->oxorder__oxstorno = new oxField( 1 );

    $oInvoicepdfOxOrder->exportDeliveryNote( $oPdf );
}

/**
 * Testing exportDeliveryNote method - uses billing address info
 * if delivery address is not setted
 *
 * @return null
 */
public function testInvoicepdfOxOrder_exportDeliveryNote_WithoutDeliveryAddress()
{
    $this->_insertTestOrder();

    $oPdf = new oxPdf;

    $oInvoicepdfOxOrder = $this->getMock( 'InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setOrderArticlesToPdf') );
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setBillingAddressToPdf');
    $oInvoicepdfOxOrder->expects( $this->once() )->method( '_setOrderArticlesToPdf');

    $oInvoicepdfOxOrder->load('_testOrderId');
    $oInvoicepdfOxOrder->oxorder__oxdelsal = new oxField( null, oxField::T_RAW );
    $oInvoicepdfOxOrder->exportDeliveryNote( $oPdf );
}

/**
 * Testing _replaceExtendedChars method
 *
 * @return null
 */
public function testInvoicepdfOxOrder_replaceExtendedChars()
{
    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );

    $sInput = " &euro; &copy; &quot; &#039; &#97; &#98; some text";
    $sStr = $oInvoicepdfOxOrder->UNITreplaceExtendedChars($sInput, true);
    $this->assertEquals( " " . chr(128) . " " . chr(169) . " \" ' a b some text", $sStr );
}

/**
 * Testing getProductVats method
 *
 * @return null
 */
public function testInvoicepdfOxOrder_getProductVats()
{
    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );

    $oInvoicepdfOxOrder->oxorder__oxartvat1 = new oxField('19', oxField::T_RAW);
    $oInvoicepdfOxOrder->oxorder__oxartvatprice1 = new oxField('9', oxField::T_RAW);

    $this->assertEquals( array("19"=>9), $oInvoicepdfOxOrder->getProductVats(false) );
}

/**
 * Testing getCurrency method
 *
 * @return null
 */
public function testInvoicepdfOxOrder_getCurrency()
{
    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );
    $oInvoicepdfOxOrder->setNonPublicVar( '_oCur', 5 );

    $this->assertEquals( 5, $oInvoicepdfOxOrder->getCurrency() );
}

/**
 * Testing getSelectedLang method
 *
 * @return null
 */
public function testInvoicepdfOxOrder_getSelectedLang()
{
    $oInvoicepdfOxOrder = $this->getProxyClass( "InvoicepdfOxOrder" );
    $oInvoicepdfOxOrder->setNonPublicVar( '_iSelectedLang', 1 );

    $this->assertEquals( 1, $oInvoicepdfOxOrder->getSelectedLang() );
}

/**
 * Data provider for testInvoicepdfOxOrder_getPaymentTerm
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
public function testInvoicepdfOxOrder_getPaymentTerm($param, $expect)
{
    $oInvoicepdfOxOrder = new InvoicepdfOxOrder();
    oxRegistry::getConfig()->setConfigParam('iPaymentTerm', $param);

    $this->assertEquals( $expect, $oInvoicepdfOxOrder->getPaymentTerm() );
}
}
