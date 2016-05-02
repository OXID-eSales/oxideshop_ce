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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Modules\Oe\Invoicepdf\Models;

use \stdClass;
use \InvoicepdfArticleSummary;
use \oxField;
use \InvoicepdfOxOrder;
use \oxPdf;
use \ReflectionClass;
use \oxTestModules;

/**
 * Testing myorder module for printing pdf's
 */
class InvoicePdfOxOrderTest extends \OxidTestCase
{
    /**
     * Prepares test suite.
     */
    protected function setUp()
    {
        parent::setUp();

        $invoicePdfOrderClass = getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfoxorder.php';
        if ($this->getTestConfig()->getShopEdition() == 'EE' || !file_exists($invoicePdfOrderClass)) {
            $this->markTestSkipped('These tests only work when invoicePDF module is present.');
        }

        if (!class_exists('InvoicepdfOxOrder', false)) {
            class_alias('oxOrder', 'InvoicepdfOxOrder_parent');

            require_once getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfoxorder.php';
            require_once getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfblock.php';
            require_once getShopBasePath() . 'modules/oe/invoicepdf/models/invoicepdfarticlesummary.php';
            require_once getShopBasePath() . 'Core/oxpdf.php';
        }
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxarticles');
        parent::tearDown();
    }

    /**
     * Testing InvoicepdfOxOrder::getVats()
     */
    public function testGetVatsGetProductVatsReturnsVatsArray()
    {
        // getProductVats returns VATs array
        $aVats = array(1, 2, 3);
        $invoicePdfOxOrder = $this->getMock("InvoicepdfOxOrder", array("getProductVats"));
        $invoicePdfOxOrder->expects($this->once())->method('getProductVats')->will($this->returnValue($aVats));

        $this->assertEquals($aVats, $invoicePdfOxOrder->getVats());
    }

    /**
     * Testing adding variables to cache
     */
    public function testInvoicepdfBlock_ToCache()
    {
        $oPdf = $this->getProxyClass("InvoicepdfBlock");

        $aParams = array(1, 2);
        $oPdf->UNITtoCache('testFunctionName', $aParams);

        $oItem = new stdClass();
        $oItem->sFunc = 'testFunctionName';
        $oItem->aParams = $aParams;
        $aCache[] = $oItem;

        $this->assertEquals($aCache, $oPdf->getNonPublicVar('_aCache'));
    }

    /**
     * Testing executing functions from cache - if calls setted function with exact parameters
     */
    public function testInvoicepdfBlock_Run()
    {
        $sClassName = oxTestModules::addFunction('InvoicepdfBlock', 'getArgsNumber', '{ $this->iArgsNum = count(func_get_args()); }');
        $oPdf = $this->getProxyClass($sClassName);

        $oItem = new stdClass();
        $oItem->sFunc = 'getArgsNumber';

        $aParams = array();
        for ($n = 1; $n <= 5; $n++) {
            $oItem->aParams = $aParams;
            $aCache = array($oItem);
            $oPdf->setNonPublicVar('_aCache', $aCache);
            $oPdf->run($oPdf);
            $this->assertEquals($n - 1, $oPdf->iArgsNum);
            $aParams[] = $n;
        }
    }

    /**
     * Testing setting pdf function and params
     */
    public function testInvoicepdfBlock_line()
    {
        $oPdf = $this->getProxyClass("InvoicepdfBlock");
        $oPdf->line(1, 2, 3, 4);

        $oItem = new stdClass();
        $oItem->sFunc = 'Line';
        $oItem->aParams = array(1, 2, 3, 4);

        $aCache = $oPdf->getNonPublicVar('_aCache');
        $this->assertEquals($oItem, $aCache[0]);
    }

    /**
     * Testing setting pdf function and params
     */
    public function testInvoicepdfBlock_text()
    {
        $oPdf = $this->getProxyClass("InvoicepdfBlock");
        $oPdf->text(1, 2, 3);

        $oItem = new stdClass();
        $oItem->sFunc = 'Text';
        $oItem->aParams = array(1, 2, 3);

        $aCache = $oPdf->getNonPublicVar('_aCache');
        $this->assertEquals($oItem, $aCache[0]);
    }

    /**
     * Testing setting pdf function and params
     */
    public function testInvoicepdfBlock_font()
    {
        $oPdf = $this->getProxyClass("InvoicepdfBlock");
        $oPdf->font(1, 2, 3);

        $oItem = new stdClass();
        $oItem->sFunc = 'SetFont';
        $oItem->aParams = array(1, 2, 3);

        $aCache = $oPdf->getNonPublicVar('_aCache');
        $this->assertEquals($oItem, $aCache[0]);
    }

    /**
     * Testing function ajustHeight updates "line" function parameters
     */
    public function testInvoicepdfBlock_ajustHeightWithLineCommand()
    {
        $oPdf = $this->getProxyClass("InvoicepdfBlock");
        $oPdf->line(1, 1, 1, 1);
        $oPdf->ajustHeight(3);

        $aCache = $oPdf->getNonPublicVar('_aCache');
        $this->assertEquals(array(1, 4, 1, 4), $aCache[0]->aParams);
    }

    /**
     * Testing function ajustHeight updates "text" function parameters
     */
    public function testInvoicepdfBlock_ajustHeightWithTextCommand()
    {
        $oPdf = $this->getProxyClass("InvoicepdfBlock");
        $oPdf->text(1, 1, 1);
        $oPdf->ajustHeight(3);

        $aCache = $oPdf->getNonPublicVar('_aCache');
        $this->assertEquals(array(1, 4, 1), $aCache[0]->aParams);
    }

    /**
     * Testing constructor
     */
    public function testInvoicepdfArticleSummary_construct()
    {
        $oPdf = $this->getProxyClass("InvoicepdfArticleSummary", array(1,2));

        $this->assertEquals(1, $oPdf->getNonPublicVar('_oData'));
        $this->assertEquals(2, $oPdf->getNonPublicVar('_oPdf'));
    }


    /**
     * Testing pdfArticleSummary class
     * Admin mode is off, so while testing generated pdf, there will be no translations,
     * only translation constants (because not in admin mode)
     */

    /**
     * Testing method _setTotalCostsWithoutDiscount
     */
    public function testInvoicepdfArticleSummary_setTotalCostsWithoutDiscount()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $sClass = oxTestModules::addFunction('InvoicepdfArticleSummary', 'getNonPublicVar($sName)', '{return $this->$sName;}');
        $sClass = oxTestModules::addFunction($sClass, 'p_setTotalCostsWithoutDiscount(&$iStartPos)', '{return $this->_setTotalCostsWithoutDiscount($iStartPos);}');
        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new $sClass($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $oPdfArtSum->p_setTotalCostsWithoutDiscount($iStartPos);

        $aCache = $oPdfArtSum->getNonPublicVar('_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_ALLPRICENETTO', $aCache[1]->aParams[2]);
        $this->assertEquals('12,00 EUR', trim($aCache[2]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[3]->aParams[2]);
        $this->assertEquals('7,00 EUR', trim($aCache[4]->aParams[2]));
    }

    /**
     * Testing method _setTotalCostsWithDiscount
     */
    public function testInvoicepdfArticleSummary_setTotalCostsWithDiscount()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();

        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setTotalCostsWithDiscount', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO', $aCache[1]->aParams[2]);
        $this->assertEquals('13,00 EUR', trim($aCache[2]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_DISCOUNT', $aCache[4]->aParams[2]);
        $this->assertEquals('-5,00 EUR', trim($aCache[5]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_ALLPRICENETTO', $aCache[7]->aParams[2]);
        $this->assertEquals('12,00 EUR', trim($aCache[8]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[9]->aParams[2]);
        $this->assertEquals('7,00 EUR', trim($aCache[10]->aParams[2]));
    }

    /**
     * Testing method _setVoucherInfo
     */
    public function testInvoicepdfArticleSummary_setVoucherInfo()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setVoucherInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_VOUCHER', $aCache[0]->aParams[2]);
        $this->assertEquals('-6,00 EUR', trim($aCache[1]->aParams[2]));
    }

    /**
     * Testing method _setDeliveryInfo
     */
    public function testInvoicepdfArticleSummary_setDeliveryInfo()
    {
        $this->getConfig()->setConfigParam('blCalcVATForDelivery', 1);
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setDeliveryInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');


        //checking values
        /*$this->assertEquals('ORDER_OVERVIEW_PDF_SHIPCOST', $aCache[0]->aParams[2]);
        $this->assertEquals('21,00 EUR', trim($aCache[1]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[2]->aParams[2]);
        $this->assertEquals('3,35 EUR', trim($aCache[3]->aParams[2]));*/

        $this->assertEquals('ORDER_OVERVIEW_PDF_SHIPCOST', trim($aCache[0]->aParams[2]));
        $this->assertEquals('21,00 EUR', trim($aCache[1]->aParams[2]));
    }

    /**
     * Testing method _setWrappingInfo
     */
    public function testInvoicepdfArticleSummary_setWrappingInfo()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setWrappingInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('WRAPPING_COSTS ORDER_OVERVIEW_PDF_BRUTTO', $aCache[0]->aParams[2]);
        $this->assertEquals('8,00 EUR', trim($aCache[1]->aParams[2]));
        /*$this->assertEquals('ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[2]->aParams[2]);
        $this->assertEquals('1,28 EUR', trim($aCache[3]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_WRAPPING ORDER_OVERVIEW_PDF_BRUTTO', trim($aCache[5]->aParams[2]));
        $this->assertEquals('8,00 EUR', trim($aCache[6]->aParams[2]));*/
    }

    /**
     * Testing method _setWrappingInfo
     */
    public function testPdfArticleSummary_setWrappingInfo_WithGiftCardOnly()
    {
        $oMyOrder = $this->getTestInvoicepdfOxOrder();
        $oMyOrder->oxorder__oxwrapvat = new oxField('0', oxField::T_RAW);
        $oMyOrder->oxorder__oxwrapcost = new oxField('0', oxField::T_RAW);
        $oMyOrder->oxorder__oxgiftcardvat = new oxField('19', oxField::T_RAW);
        $oMyOrder->oxorder__oxgiftcardcost = new oxField('8', oxField::T_RAW);

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($oMyOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setWrappingInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('GIFTCARD_COSTS ORDER_OVERVIEW_PDF_BRUTTO', $aCache[0]->aParams[2]);
        $this->assertEquals('8,00 EUR', trim($aCache[1]->aParams[2]));
    }

    /**
     * Testing method _setPaymentInfo
     */
    public function testInvoicepdfArticleSummary_setPaymentInfo()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setPaymentInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_PAYMENTIMPACT', $aCache[0]->aParams[2]);
        $this->assertEquals('6,00 EUR', trim($aCache[1]->aParams[2]));
       /* $this->assertEquals('ORDER_OVERVIEW_PDF_ZZGLVAT19ORDER_OVERVIEW_PDF_PERCENTSUM', $aCache[2]->aParams[2]);
        $this->assertEquals('0,96 EUR', trim($aCache[3]->aParams[2]));
        $this->assertEquals('ORDER_OVERVIEW_PDF_PAYMENTIMPACT', trim($aCache[4]->aParams[2]));
        $this->assertEquals('6,00 EUR', trim($aCache[5]->aParams[2]));*/
    }

    /**
     * Testing method _setGrandTotalPriceInfo
     */
    public function testInvoicepdfArticleSummary_setGrandTotalPriceInfo()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setGrandTotalPriceInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_ALLSUM', $aCache[1]->aParams[2]);
        $this->assertEquals('25,00 EUR', trim($aCache[2]->aParams[2]));
    }

    /**
     * Testing method _setPaymentMethodInfo
     */
    public function testInvoicepdfArticleSummary_setPaymentMethodInfo()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setPaymentMethodInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_SELPAYMENTNachnahme', $aCache[1]->aParams[2]);
    }

    /**
     * Testing method _setPaymentMethodInfo in not delfault language
     */
    public function testInvoicepdfArticleSummary_setPaymentMethodInfoInOtherLang()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();
        $invoicePdfOxOrder->setNonPublicVar('_iSelectedLang', 1);

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setPaymentMethodInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_SELPAYMENTCOD (Cash on Delivery)', $aCache[1]->aParams[2]);
    }

    /**
     * Testing method _setPayUntilInfo
     */
    public function testInvoicepdfArticleSummary_setPayUntilInfo()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();
        $invoicePdfOxOrder->oxorder__oxbilldate = new oxField('2000-01-01', oxField::T_RAW);

        $oPdf = $this->getPdfTestObject();
        $oPdfArtSum = new InvoicepdfArticleSummary($invoicePdfOxOrder, $oPdf);

        $iStartPos = 1;
        $this->callProtectedMethod($oPdfArtSum, '_setPayUntilInfo', array(&$iStartPos));
        $aCache = $this->getProtectedProperty($oPdfArtSum, '_aCache');

        //checking values
        $this->assertEquals('ORDER_OVERVIEW_PDF_PAYUPTO'. '08.01.2000', $aCache[1]->aParams[2]);
    }

    /**
     * Testing method generate
     */
    public function testInvoicepdfArticleSummary_generate()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();

        $oPdf = $this->getPdfTestObject();

        $aFunctions = array('_setTotalCostsWithDiscount', '_setVoucherInfo', '_setDeliveryInfo', '_setWrappingInfo', '_setPaymentInfo', '_setGrandTotalPriceInfo', '_setPaymentMethodInfo', '_setPayUntilInfo');
        $oPdfArtSum = $this->getMock('InvoicepdfArticleSummary', $aFunctions, array($invoicePdfOxOrder, $oPdf));
        $oPdfArtSum->expects($this->once())->method('_setTotalCostsWithDiscount');
        $oPdfArtSum->expects($this->once())->method('_setVoucherInfo');
        $oPdfArtSum->expects($this->once())->method('_setDeliveryInfo');
        $oPdfArtSum->expects($this->once())->method('_setWrappingInfo');
        $oPdfArtSum->expects($this->once())->method('_setPaymentInfo');
        $oPdfArtSum->expects($this->once())->method('_setGrandTotalPriceInfo');
        $oPdfArtSum->expects($this->once())->method('_setPaymentMethodInfo');
        $oPdfArtSum->expects($this->once())->method('_setPayUntilInfo');

        $oPdfArtSum->generate(1);
    }

    /**
     * Testing method generate when order is without discount
     */
    public function testInvoicepdfArticleSummary_generateWithoutDiscount()
    {
        $invoicePdfOxOrder = $this->getTestInvoicepdfOxOrder();
        $invoicePdfOxOrder->oxorder__oxdiscount->value = null;
        //$invoicePdfOxOrder->setNonPublicVar('_oData', $oData);

        $oPdf = $this->getPdfTestObject();

        $aFunctions = array('_setTotalCostsWithoutDiscount', '_setTotalCostsWithDiscount');
        $oPdfArtSum = $this->getMock('InvoicepdfArticleSummary', $aFunctions, array($invoicePdfOxOrder, $oPdf));

        $oPdfArtSum->expects($this->once())->method('_setTotalCostsWithoutDiscount');
        $oPdfArtSum->expects($this->never())->method('_setTotalCostsWithDiscount');

        $oPdfArtSum->generate(1);
    }

    /**
     * Testing muOrder class
     * Admin mode is off, so while testing generated pdf, there will be no translations,
     * only translation constatns
     */

    /**
     * Testing method _getActShop
     */
    public function testInvoicepdfOxOrder_getActShop()
    {
        $sShopId = $this->getTestConfig()->getShopEdition() == 'EE' ? '1' : 'oxbaseshop';

        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");
        $oShop = $invoicePdfOxOrder->UNITgetActShop();

        $this->assertEquals($sShopId, $oShop->getId());
    }

    /**
     * Testing translate method
     */
    public function testInvoicepdfOxOrder_translate()
    {
        $invoicePdfOxOrder =  new InvoicepdfOxOrder();

        $invoicePdfOxOrder->setSelectedLang(1);
        $this->setAdminMode(true);

        $this->assertEquals('phone: ', $invoicePdfOxOrder->translate('ORDER_OVERVIEW_PDF_PHONE'));
    }

    /**
     * Testing genPdf method - generating standart pdf
     */
    public function testInvoicepdfOxOrder_genPdfStandart()
    {
        $this->insertTestOrder();

        oxTestModules::addFunction("oxPdf", "output", "{return '';}");

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('pdfHeader', 'exportStandart', 'pdfFooter'));
        $invoicePdfOxOrder->expects($this->once())->method('pdfHeader');
        $invoicePdfOxOrder->expects($this->once())->method('exportStandart');
        $invoicePdfOxOrder->expects($this->once())->method('pdfFooter');

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->genPdf('testfilename', 1);
    }

    /**
     * Testing genPdf method - generating standart pdf and counting number of generated pages.
     */
    public function testInvoicepdfOxOrder_genPdfStandartCountingNumberOfGeneratedPages()
    {
        $this->insertTestOrder();

        for ($i = 0; $i < 80; $i++) {
            $this->insertTestOrderArticle();

            $oOrderArticle = oxNew('oxOrderArticle');
            if ($oOrderArticle->load('_testOrderArticleId')) {
                $oOrderArticle->setId('_testOrderArticleId'.$i);
                $oOrderArticle->save();
            }
        }

        oxTestModules::addFunction("oxPdf", "output", "{return '';}");

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('pdfHeader'));
        $invoicePdfOxOrder->expects($this->exactly(3))->method('pdfHeader');
        $invoicePdfOxOrder->load('_testOrderId');

        $invoicePdfOxOrder->genPdf('testfilename', 1);
    }

    /**
     * Testing genPdf method - generating delivery note pdf
     */
    public function testInvoicepdfOxOrder_genPdfDeliveryNote()
    {
        $this->insertTestOrder();

        oxTestModules::addFunction("oxPdf", "output", "{return '';}");

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('pdfHeader', 'exportDeliveryNote', 'pdfFooter'));
        $invoicePdfOxOrder->expects($this->once())->method('pdfHeader');
        $invoicePdfOxOrder->expects($this->once())->method('exportDeliveryNote');
        $invoicePdfOxOrder->expects($this->once())->method('pdfFooter');

        $invoicePdfOxOrder->load('_testOrderId');
        $this->setRequestParameter('pdftype', 'dnote');
        $invoicePdfOxOrder->genPdf('testfilename', 1);
    }

    /**
     * Testing genPdf method - adding invoice number
     */
    public function testInvoicepdfOxOrder_genPdfSettingInvoiceNr()
    {
        $this->insertTestOrder();

        oxTestModules::addFunction("oxPdf", "output", "{return '';}");

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('getNextBillNum'));
        $invoicePdfOxOrder->expects($this->once())->method('getNextBillNum')->will($this->returnValue('testInvoiceNr'));

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->genPdf('testfilename', 1);

        $this->assertEquals('testInvoiceNr', $invoicePdfOxOrder->oxorder__oxbillnr->value);
    }

    /**
     * Testing exportStandart method - calling needed methods
     */
    public function testInvoicepdfOxOrder_exportStandart()
    {
        $this->insertTestOrder();

        $oPdf = new oxPdf;

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setDeliveryAddressToPdf', '_setOrderArticlesToPdf'));
        $invoicePdfOxOrder->expects($this->once())->method('_setBillingAddressToPdf');
        $invoicePdfOxOrder->expects($this->never())->method('_setDeliveryAddressToPdf');
        $invoicePdfOxOrder->expects($this->once())->method('_setOrderArticlesToPdf');

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->exportStandart($oPdf);
    }

    /**
     * Testing exportStandart method - when order is canceled
     */
    public function testInvoicepdfOxOrder_exportStandartWhenOrderIsCanceled()
    {
        // marking order article as variant ..
        $oSelVariantField = $this->getMock('oxfield', array('__get'));
        $oSelVariantField->expects($this->once())->method('__get');

        $this->insertTestOrder();
        $oArticle = $this->insertTestOrderArticle();
        $oArticle->oxorderarticles__oxtitle = new oxField("testtitle");
        $oArticle->oxorderarticles__oxselvariant = $oSelVariantField;

        $oPdf = new oxPdf;

        $invoicePdfOxOrder = $this->getMock("InvoicepdfOxOrder", array("getOrderArticles"));
        $invoicePdfOxOrder->expects($this->any())->method('getOrderArticles')->will($this->returnValue(array($oArticle->getId() => $oArticle)));
        $invoicePdfOxOrder->load('_testOrderId');

        //
        $invoicePdfOxOrder->oxorder__oxdelcost = $this->getMock('oxfield', array('setValue'));
        $invoicePdfOxOrder->oxorder__oxdelcost->expects($this->once())->method('setValue')->with($this->equalTo(0));

        $invoicePdfOxOrder->oxorder__oxpaycost = $this->getMock('oxfield', array('setValue'));
        $invoicePdfOxOrder->oxorder__oxpaycost->expects($this->once())->method('setValue')->with($this->equalTo(0));

        $invoicePdfOxOrder->oxorder__oxordernr = $this->getMock('oxfield', array('setValue'));
        $invoicePdfOxOrder->oxorder__oxordernr->expects($this->once())->method('setValue')->with($this->equalTo('   ORDER_OVERVIEW_PDF_STORNO'), $this->equalTo(2));

        // marking as canceled
        $invoicePdfOxOrder->oxorder__oxstorno = new oxField(1);

        $invoicePdfOxOrder->exportStandart($oPdf);
    }

    /**
     * Testing exportStandart method - calling needed methods when delivery address is setted
     */
    public function testInvoicepdfOxOrder_exportStandart_WithDeliveryAddress()
    {
        $this->insertTestOrder();

        $oPdf = new oxPdf;

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setDeliveryAddressToPdf', '_setOrderArticlesToPdf'));
        $invoicePdfOxOrder->expects($this->once())->method('_setBillingAddressToPdf');
        $invoicePdfOxOrder->expects($this->once())->method('_setDeliveryAddressToPdf');
        $invoicePdfOxOrder->expects($this->once())->method('_setOrderArticlesToPdf');

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->oxorder__oxdelsal = new oxField('1', oxField::T_RAW);
        $invoicePdfOxOrder->exportStandart($oPdf);
    }

    /**
     * Testing exportStandart method - setting order currency
     */
    public function testInvoicepdfOxOrder_exportStandart_SettingCurrency()
    {
        $this->insertTestOrder();

        $oPdf = new oxPdf;
        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->oxorder__oxdelsal = new oxField("testSal");

        $oCur = $invoicePdfOxOrder->getConfig()->getCurrencyObject('EUR');
        $invoicePdfOxOrder->exportStandart($oPdf);

        $this->assertEquals($oCur, $invoicePdfOxOrder->getNonPublicVar('_oCur'));
    }

    /**
     * Testing exportDeliveryNote method - calling needed methods
     */
    public function testInvoicepdfOxOrder_exportDeliveryNote()
    {
        $this->insertTestOrder();

        $oPdf = new oxPdf;

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setOrderArticlesToPdf'));
        $invoicePdfOxOrder->expects($this->never())->method('_setBillingAddressToPdf');
        $invoicePdfOxOrder->expects($this->once())->method('_setOrderArticlesToPdf');

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->oxorder__oxdelsal = new oxField('1', oxField::T_RAW);
        $invoicePdfOxOrder->exportDeliveryNote($oPdf);
    }

    /**
     * Testing exportDeliveryNote method - when order is canceled.
     */
    public function testInvoicepdfOxOrder_exportDeliveryNoteWhenOrderIsCanceled()
    {
        $this->insertTestOrder();

        $oPdf = new oxPdf;

        $invoicePdfOxOrder = new InvoicepdfOxOrder();
        $invoicePdfOxOrder->load('_testOrderId');

        //
        $invoicePdfOxOrder->oxorder__oxdelcost = $this->getMock('oxfield', array('setValue'));
        $invoicePdfOxOrder->oxorder__oxdelcost->expects($this->never())->method('setValue');

        $invoicePdfOxOrder->oxorder__oxpaycost = $this->getMock('oxfield', array('setValue'));
        $invoicePdfOxOrder->oxorder__oxpaycost->expects($this->never())->method('setValue');

        $invoicePdfOxOrder->oxorder__oxordernr = $this->getMock('oxfield', array('setValue'));
        $invoicePdfOxOrder->oxorder__oxordernr->expects($this->once())->method('setValue')->with($this->equalTo('   ORDER_OVERVIEW_PDF_STORNO'), $this->equalTo(2));

        // marking as canceled
        $invoicePdfOxOrder->oxorder__oxstorno = new oxField(1);

        $invoicePdfOxOrder->exportDeliveryNote($oPdf);
    }

    /**
     * Testing exportDeliveryNote method - uses billing address info
     * if delivery address is not setted
     */
    public function testInvoicepdfOxOrder_exportDeliveryNote_WithoutDeliveryAddress()
    {
        $this->insertTestOrder();

        $oPdf = new oxPdf;

        $invoicePdfOxOrder = $this->getMock('InvoicepdfOxOrder', array('_setBillingAddressToPdf', '_setOrderArticlesToPdf'));
        $invoicePdfOxOrder->expects($this->once())->method('_setBillingAddressToPdf');
        $invoicePdfOxOrder->expects($this->once())->method('_setOrderArticlesToPdf');

        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->oxorder__oxdelsal = new oxField(null, oxField::T_RAW);
        $invoicePdfOxOrder->exportDeliveryNote($oPdf);
    }

    /**
     * Testing _replaceExtendedChars method
     */
    public function testInvoicepdfOxOrder_replaceExtendedChars()
    {
        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");

        $sInput = " &euro; &copy; &quot; &#039; &#97; &#98; some text";
        $sStr = $invoicePdfOxOrder->UNITreplaceExtendedChars($sInput, true);
        $this->assertEquals( " € © \" ' a b some text", $sStr );
    }

    /**
     * Testing getProductVats method
     */
    public function testInvoicepdfOxOrder_getProductVats()
    {
        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");

        $invoicePdfOxOrder->oxorder__oxartvat1 = new oxField('19', oxField::T_RAW);
        $invoicePdfOxOrder->oxorder__oxartvatprice1 = new oxField('9', oxField::T_RAW);

        $this->assertEquals(array("19"=>9), $invoicePdfOxOrder->getProductVats(false));
    }

    /**
     * Testing getCurrency method
     */
    public function testInvoicepdfOxOrder_getCurrency()
    {
        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");
        $invoicePdfOxOrder->setNonPublicVar('_oCur', 5);

        $this->assertEquals(5, $invoicePdfOxOrder->getCurrency());
    }

    /**
     * Testing getSelectedLang method
     */
    public function testInvoicepdfOxOrder_getSelectedLang()
    {
        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");
        $invoicePdfOxOrder->setNonPublicVar('_iSelectedLang', 1);

        $this->assertEquals(1, $invoicePdfOxOrder->getSelectedLang());
    }

    /**
     * Data provider for testInvoicepdfOxOrder_getPaymentTerm
     *
     * @return array
     */
    public function getPaymentDataProvider()
    {
        return array(array(null, 7), array(10, 10), array(0, 0));
    }

    /**
     * Testing getPaymentTerm() method
     * getPaymentTerm() method returns config param iPaymentTerm, default value is 7;
     *
     * @dataProvider getPaymentDataProvider
     *
     * @param int $param
     * @param int $expect
     */
    public function testInvoicepdfOxOrder_getPaymentTerm($param, $expect)
    {
        $invoicePdfOxOrder = new InvoicepdfOxOrder();
        $this->getConfig()->setConfigParam('iPaymentTerm', $param);

        $this->assertEquals($expect, $invoicePdfOxOrder->getPaymentTerm());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPdfTestObject()
    {
        $pdf = $this->getMock('testPdfClass', array('getStringWidth'));
        $pdf->expects($this->any())->method('getStringWidth')->will($this->returnValue(1));

        return $pdf;
    }

    /**
     * Calls private or protected method.
     *
     * @param object      $object
     * @param string      $methodName
     * @param array|mixed $arguments
     *
     * @return mixed Called method return value.
     */
    protected function callProtectedMethod($object, $methodName, $arguments)
    {
        $class = new ReflectionClass($object);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, (array) $arguments);
    }

    /**
     * Returns value of private or protected object property.
     *
     * @param object $object
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getProtectedProperty($object, $propertyName)
    {
        $class = new ReflectionClass($object);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * Get test InvoicePdfOxOrder object.
     *
     * @return InvoicePdfOxOrder
     */
    private function getTestInvoicepdfOxOrder()
    {
        $this->insertTestOrder();
        $invoicePdfOxOrder = $this->getProxyClass("InvoicepdfOxOrder");
        $invoicePdfOxOrder->load('_testOrderId');
        $invoicePdfOxOrder->setNonPublicVar("_oCur", $invoicePdfOxOrder->getConfig()->getCurrencyObject('EUR'));

        return $invoicePdfOxOrder;
    }

    /**
     * Inserts test order.
     */
    private function insertTestOrder()
    {
        $config = $this->getConfig();

        //set order
        $order = oxNew("oxOrder");
        $order->setId('_testOrderId');
        $order->oxorder__oxshopid = new oxField($config->getShopId(), oxField::T_RAW);
        $order->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);
        $order->oxorder__oxbillcountryid = new oxField('10', oxField::T_RAW);
        $order->oxorder__oxdelcountryid = new oxField('11', oxField::T_RAW);
        $order->oxorder__oxdeltype = new oxField('_testDeliverySetId', oxField::T_RAW);
        $order->oxorder__oxdelvat = new oxField('19', oxField::T_RAW);
        $order->oxorder__oxdelcost = new oxField('21', oxField::T_RAW);
        $order->oxorder__oxpaymentid = new oxField('_testPaymentId', oxField::T_RAW);
        $order->oxorder__oxpaymenttype = new oxField('oxidcashondel', oxField::T_RAW);
        $order->oxorder__oxpayvat = new oxField('19', oxField::T_RAW);
        $order->oxorder__oxpaycost = new oxField('6', oxField::T_RAW);
        $order->oxorder__oxcardid = new oxField('_testWrappingId', oxField::T_RAW);
        $order->oxorder__oxtotalnetsum = new oxField('12', oxField::T_RAW);
        $order->oxorder__oxtotalbrutsum = new oxField('13', oxField::T_RAW);
        $order->oxorder__oxartvat1  = new oxField('19', oxField::T_RAW);
        $order->oxorder__oxartvatprice1  = new oxField('7', oxField::T_RAW);
        $order->oxorder__oxcurrency = new oxField('1', oxField::T_RAW);
        $order->oxorder__oxdiscount = new oxField('5', oxField::T_RAW);
        $order->oxorder__oxvoucherdiscount = new oxField('6', oxField::T_RAW);
        $order->oxorder__oxwrapvat = new oxField('19', oxField::T_RAW);
        $order->oxorder__oxwrapcost = new oxField('8', oxField::T_RAW);
        $order->oxorder__oxtotalordersum = new oxField('25', oxField::T_RAW);
        $order->save();
    }

    /**
     * Inserts test order articles.
     *
     * @param int $storno canceled product
     *
     * @return oxOrderArticle
     */
    private function insertTestOrderArticle($storno = 0)
    {
        $orderArticle = oxNew("oxOrderArticle");
        $orderArticle->setId('_testOrderArticleId');
        $orderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId', oxField::T_RAW);
        $orderArticle->oxorderarticles__oxartid = new oxField('_testArticleId', oxField::T_RAW);
        $orderArticle->oxorderarticles__oxamount = new oxField(5, oxField::T_RAW);
        $orderArticle->oxorderarticles__oxvat = new oxField(19, oxField::T_RAW);
        $orderArticle->oxorderarticles__oxvatprice = new oxField(7, oxField::T_RAW);
        $orderArticle->oxorderarticles__oxstorno = new oxField($storno, oxField::T_RAW);
        $orderArticle->save();

        return $orderArticle;
    }
}
