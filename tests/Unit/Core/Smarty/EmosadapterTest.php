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
namespace Unit\Core\Smarty;

use \oxField;
use OxidEsales\EshopCommunity\Core\Smarty\Plugin\Emos;
use \stdClass;
use \oxDb;
use \oxRegistry;

class EmosadapterTest extends \OxidTestCase
{

    protected function setUp()
    {
        parent::setUp();
        oxDb::getDb()->execute("delete from oxuserbasketitems");
        oxDb::getDb()->execute("delete from oxuserbaskets");
    }

    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxuserbasketitems");
        oxDb::getDb()->execute("delete from oxuserbaskets");
        parent::tearDown();
    }

    /**
     * Test for function _convProd2EmosItem. testing with umlauts
     */
    public function testConvProd2EmosItem()
    {
        $oCurr = new \stdClass;
        $oCurr->rate = 2;

        $oPrice = $this->getMock('oxPrice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(10));

        $oProduct = $this->getMock('oxPrice', array('getPrice', 'getVendor', 'getManufacturer', 'getId'));
        $oProduct->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));
        $oProduct->expects($this->once())->method('getVendor')->will($this->returnValue(false));
        $oProduct->expects($this->once())->method('getManufacturer')->will($this->returnValue(false));
        $oProduct->expects($this->once())->method('getId')->will($this->returnValue(1));
        $oProduct->oxarticles__oxartnum = new oxField('123');
        $oProduct->oxarticles__oxtitle = new oxField('oxütitle');
        $oProduct->oxarticles__oxvarselect = new oxField('oxüvarselect');

        $sContent = "SHOP/oxütitle";
        $sCharset = oxRegistry::getLang()->translateString('charset');
        $sResult = iconv($sCharset, 'UTF-8', $sContent);

        $oConfig = $this->getMock('oxConfig', array('isUtf', 'getActShopCurrencyObject'));
        $oConfig->expects($this->any())->method('isUtf')->will($this->returnValue(false));
        $oConfig->expects($this->once())->method('getActShopCurrencyObject')->will($this->returnValue($oCurr));

        $oEmosAdapter = $this->getMock('oxEmosAdapter', array('getConfig'));
        $oEmosAdapter->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmosItem = $oEmosAdapter->UNITconvProd2EmosItem($oProduct, 'SHOP');

        $this->assertEquals($sResult, $oEmosItem->productGroup);
        $this->assertEquals(5, $oEmosItem->price);
    }

    public function testPrepareProductTitle()
    {
        $oProduct = new \stdClass;
        $oProduct->oxarticles__oxtitle = new oxField('oxütitle');
        $oProduct->oxarticles__oxvarselect = new oxField('oxüvarselect');

        $sContent = "oxütitle oxüvarselect";
        $sCharset = oxRegistry::getLang()->translateString('charset');
        $sConverted = iconv($sCharset, 'UTF-8', $sContent);

        $oConfig = $this->getMock('oxConfig', array('isUtf'));
        $oConfig->expects($this->once())->method('isUtf')->will($this->returnValue(false));

        $oEmosAdapter = $this->getMock('oxEmosAdapter', array('getConfig'));
        $oEmosAdapter->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals($sConverted, $oEmosAdapter->UNITprepareProductTitle($oProduct));
    }

    /**
     * Test for function _convertToUtf - check wether this function
     * returns string converted to Utf8, when shop is not in Utf mode
     */
    public function testConvertToUtf()
    {
        $oConfig = $this->getMock('oxConfig', array('isUtf'));
        $oConfig->expects($this->once())->method('isUtf')->will($this->returnValue(false));

        $oEmosAdapter = $this->getMock('oxEmosAdapter', array('getConfig'));
        $oEmosAdapter->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $sContent = "Zurück zum Shop";
        $sCharset = oxRegistry::getLang()->translateString('charset');

        $sConverted = iconv($sCharset, 'UTF-8', $sContent);
        $sResult = $oEmosAdapter->UNITconvertToUtf($sContent);

        $this->assertEquals($sConverted, $sResult);
    }

    //
    public function testGetCodeForChangeBasket()
    {
        $aParams = null;
        $oSmarty = null;

        $oProduct = oxNew('oxArticle');
        $oProduct->load('1126');

        $oEmosItem = oxNew('EMOS_Item');
        $oEmosItem->productId = '1126';
        $oEmosItem->productName = 'Bar-Set ABSINTH';
        $oEmosItem->price = 34;
        $oEmosItem->productGroup = 'Bar-Equipment/Bar-Set ABSINTH';
        $oEmosItem->quantity = 10;
        $oEmosItem->variant1 = 'NULL';
        $oEmosItem->variant2 = 'NULL';
        $oEmosItem->variant3 = 'NULL';

        $aLastCall = array('changebasket' => array('1126' => array('oldam' => 15, 'am' => 5, 'aid' => '1126')));
        $this->getSession()->setVariable('aLastcall', $aLastCall);

        $oFormatter = $this->getMock('EMOS', array('removeFromBasket', 'appendPreScript'));
        $oFormatter->expects($this->once())->method('removeFromBasket')->with($this->equalTo($oEmosItem));
        //$oFormatter->expects( $this->at( 2 ) )->method( 'appendPreScript')->with( $this->equalTo( "15->5:".(true) ) );

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getBasketProductCatPath', '_convProd2EmosItem', '_getEmosCl'));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue(false));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getBasketProductCatPath')->will($this->returnValue('DeepestCategoryPath'));
        $oEmos->expects($this->once())->method('_convProd2EmosItem')->with($this->isInstanceOf(oxarticle), $this->equalTo('DeepestCategoryPath'), $this->equalTo(10))->will($this->returnValue($oEmosItem));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetEmos()
    {
        $oEmosAdapter = oxNew('oxEmosAdapter');
        $oEmos = $oEmosAdapter->getEmos();
        $this->assertTrue($oEmos instanceof Emos);

    }

    public function testGetEmosPageTitle()
    {
        $oEmosCode = oxNew('oxEmosAdapter');
        $this->assertNull($oEmosCode->UNITgetEmosPageTitle(array()));
        $this->assertEquals('testGetEmosPageTitle', $oEmosCode->UNITgetEmosPageTitle(array('title' => 'testGetEmosPageTitle')));
    }

    public function testGetEmosCatPath()
    {
        $aCat1 = array('title' => '1ü', 'link' => 'http://one');
        $aCat2 = array('title' => '2ü', 'link' => 'http://two');
        $aCat3 = array('title' => '3ü', 'link' => 'http://three');

        $oActiveView = $this->getMock('oxview', array('getBreadCrumb'));
        $oActiveView->expects($this->once())->method('getBreadCrumb')->will($this->returnValue(array($aCat1, $aCat2, $aCat3)));

        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'isUtf'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oActiveView));
        $oConfig->expects($this->once())->method('isUtf')->will($this->returnValue(false));

        $oEmosCode = $this->getMock('oxEmosAdapter', array('getConfig'));
        $oEmosCode->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $sContent = "1ü/2ü/3ü";
        $sCharset = oxRegistry::getLang()->translateString('charset');

        $sConverted = iconv($sCharset, 'UTF-8', $sContent);

        $this->assertEquals($sConverted, $oEmosCode->UNITgetEmosCatPath());
    }

    public function testGetTplNameSetInRequest()
    {
        $this->setRequestParameter('tpl', 'getTemplateName');

        $oEmos = oxNew('oxEmosAdapter');
        $this->assertEquals('getTemplateName', $oEmos->UNITgetTplName());
    }

    public function testGetTplNameNotSetInRequest()
    {
        $oActiveView = $this->getMock('oxview', array('getTemplateName'));
        $oActiveView->expects($this->once())->method('getTemplateName')->will($this->returnValue('getTemplateName'));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oActiveView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getConfig'));
        $oEmos->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('getTemplateName', $oEmos->UNITgetTplName());
    }

    public function testGetCodeForStart()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Start'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('start'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForBasket()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Warenkorb'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('1_Warenkorb'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('basket'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForUser()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Kundendaten'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('2_Kundendaten'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('user'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForUserOption1()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('option', '1');

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Kundendaten/OhneReg'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('2_Kundendaten/OhneReg'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('user'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForUserOption2()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('option', '2');

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Kundendaten/BereitsKunde'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('2_Kundendaten/BereitsKunde'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('user'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForUserOption3()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('option', '3');

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Kundendaten/NeuesKonto'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('2_Kundendaten/NeuesKonto'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('user'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForPayment()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Zahlungsoptionen'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('3_Zahlungsoptionen'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('payment'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForPaymentAfterRegistrationSuccess()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('new_user', 1);
        $this->setRequestParameter('success', 1);
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess', 'addRegister'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Zahlungsoptionen'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('3_Zahlungsoptionen'));
        $oFormatter->expects($this->once())->method('addRegister')->with($this->equalTo('oxdefaultadmin'), $this->equalTo(0));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('payment'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForPaymentAfterRegistrationError()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('new_user', 1);
        $this->setRequestParameter('newslettererror', -1);

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess', 'addRegister'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Zahlungsoptionen'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('3_Zahlungsoptionen'));
        $oFormatter->expects($this->once())->method('addRegister')->with($this->equalTo('NULL'), $this->equalTo(1));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('payment'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForOrder()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Bestelluebersicht'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('4_Bestelluebersicht'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('order'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForThankyou()
    {
        $aParams = null;
        $oSmarty = null;

        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');

        $oEmosItem = oxNew('EMOS_Item');
        $oEmosItem->productId = '1126';
        $oEmosItem->productName = 'Bar-Set ABSINTH';
        $oEmosItem->price = 34;
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $oEmosItem->productGroup = 'Party/Bar-Equipment/Bar-Set ABSINTH';
        } else {
            $oEmosItem->productGroup = 'Geschenke/Bar-Equipment/Bar-Set ABSINTH';
        }
        $oEmosItem->quantity = 10;
        $oEmosItem->variant1 = 'NULL';
        $oEmosItem->variant2 = 'NULL';
        $oEmosItem->variant3 = '1126';

        $aBasketArray = array($oEmosItem);

        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxordernr = new oxfield('999');
        $oOrder->oxorder__oxbillcountry = new oxfield('999');
        $oOrder->oxorder__oxbillzip = new oxfield('999');
        $oOrder->oxorder__oxbillcity = new oxfield('999');

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket('1126', 10);
        $oBasket->calculateBasket(false);

        $oCurr = (object) (array('id' => '0', 'name' => 'EUR', 'rate' => 1.00, 'dec' => ',', 'thousand' => '.', 'sign' => 'EUR', 'decimal' => 2, 'selected' => 0));

        $oView = $this->getMock('order', array('getOrder', 'getBasket'));
        $oView->expects($this->once())->method('getOrder')->will($this->returnValue($oOrder));
        $oView->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'getActShopCurrencyObject'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->any())->method('getActShopCurrencyObject')->will($this->returnValue($oCurr));

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess', 'addEmosBillingPageArray', 'addEmosBasketPageArray'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Kaufprozess/Bestaetigung'));
        $oFormatter->expects($this->once())->method('addOrderProcess')->with($this->equalTo('5_Bestaetigung'));
        $oFormatter->expects($this->once())->method('addEmosBillingPageArray')->with($this->equalTo('999'), $this->equalTo(oxADMIN_LOGIN), $this->equalTo($oBasket->getPrice()->getBruttoPrice() * (1 / $oCurr->rate)), $this->equalTo('999'), $this->equalTo('999'), $this->equalTo('999'));
        $oFormatter->expects($this->once())->method('addEmosBasketPageArray')->with($this->equalTo($aBasketArray));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('thankyou'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    //#1311: Shop offline error when product is out of stock and Econda is active
    public function testGetCodeForThankyouIfArtOutOfStock()
    {
        $aParams = null;
        $oSmarty = null;

        $this->getSession()->setVariable('usr', 'oxdefaultadmin');

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(0);
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->save();

        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxordernr = new oxfield('999');
        $oOrder->oxorder__oxbillcountry = new oxfield('999');
        $oOrder->oxorder__oxbillzip = new oxfield('999');
        $oOrder->oxorder__oxbillcity = new oxfield('999');

        $oBasketItem = $this->getProxyClass('oxbasketitem');
        $oBasketItem->setNonPublicVar('_sProductId', '_testArt');
        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));

        $oCurr = array('id' => '0', 'name' => 'EUR', 'rate' => 1.00, 'dec' => ',', 'thousand' => '.', 'sign' => 'EUR', 'decimal' => 2, 'selected' => 0);

        $oView = $this->getMock('order', array('getOrder', 'getBasket'));
        $oView->expects($this->once())->method('getOrder')->will($this->returnValue($oOrder));
        $oView->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'getActShopCurrencyObject'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->any())->method('getActShopCurrencyObject')->will($this->returnValue((object) $oCurr));

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addOrderProcess', 'addEmosBillingPageArray', 'addEmosBasketPageArray'));
        $oFormatter->expects($this->once())->method('addContent');
        $oFormatter->expects($this->once())->method('addOrderProcess');
        $oFormatter->expects($this->once())->method('addEmosBillingPageArray');
        $oFormatter->expects($this->once())->method('addEmosBasketPageArray');

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('thankyou'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForDetails()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->load('1126');

        $aParams = array('product' => $oProduct);
        $oSmarty = null;

        $oEmosItem = oxNew('EMOS_Item');
        $oEmosItem->productId = '1126';
        $oEmosItem->productName = 'Bar-Set ABSINTH';
        $oEmosItem->price = 34;
        $oEmosItem->productGroup = 'Bar-Equipment/Bar-Set ABSINTH';
        $oEmosItem->quantity = 10;
        $oEmosItem->variant1 = 'NULL';
        $oEmosItem->variant2 = 'NULL';
        $oEmosItem->variant3 = 'NULL';

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addDetailView'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Bar-Equipment/Bar-Set ABSINTH'));
        $oFormatter->expects($this->once())->method('addDetailView')->with($this->equalTo($oEmosItem));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', '_convProd2EmosItem', '_getBasketProductCatPath'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('oxwarticledetails'));
        $oEmos->expects($this->once())->method('_convProd2EmosItem')->will($this->returnValue($oEmosItem));
        $oEmos->expects($this->once())->method('_getBasketProductCatPath')->will($this->returnValue('Bar-Equipment'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForSearch()
    {
        $aParams = null;
        $oSmarty = new stdClass();

        $oMock = $this->getMock('stdClass', array('getArticleCount'));
        $oMock->expects($this->any())->method('getArticleCount')->will($this->returnValue(100));
        $oSmarty->_tpl_vars['oView'] = $oMock;

        $this->setRequestParameter('searchparam', 'searchParam');

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addSearch'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/Suche'));
        $oFormatter->expects($this->once())->method('addSearch')->with($this->equalTo('searchParam'), $this->equalTo(100));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('search'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForList()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Shop/_getEmosCatPath'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', '_getEmosCatPath'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('alist'));
        $oEmos->expects($this->once())->method('_getEmosCatPath')->will($this->returnValue('_getEmosCatPath'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountWishlist()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Wunschzettel'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account_wishlist'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContactNoStatus()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Kontakt/Form'));

        $oView = $this->getMock('contact', array('getContactSendStatus'));
        $oView->expects($this->once())->method('getContactSendStatus')->will($this->returnValue(0));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('contact'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContactAnyStatus()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addContact'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Kontakt/Success'));
        $oFormatter->expects($this->once())->method('addContact')->with($this->equalTo('Kontakt'));

        $oView = $this->getMock('contact', array('getContactSendStatus'));
        $oView->expects($this->once())->method('getContactSendStatus')->will($this->returnValue(1));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('contact'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForHelp()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Hilfe'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('help'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForNewsletterAnyStatus()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Newsletter/Success'));

        $oView = $this->getMock('newsletter', array('getNewsletterStatus'));
        $oView->expects($this->once())->method('getNewsletterStatus')->will($this->returnValue(1));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('newsletter'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForNewsletterNoStatus()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Newsletter/Form'));

        $oView = $this->getMock('newsletter', array('getNewsletterStatus'));
        $oView->expects($this->once())->method('getNewsletterStatus')->will($this->returnValue(0));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('newsletter'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForLinks()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Links'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('links'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForImpressumInfo()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'impressum.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Impressum'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('info'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAgbInfo()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'agb.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/AGB'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('info'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForBestellinfoInfo()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'order_info.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Bestellinfo'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('info'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForVersandinfoInfo()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'delivery_info.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Versandinfo'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('info'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForSicherheitInfo()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'security_info.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Sicherheit'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('info'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForDefault()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'default.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Content/default'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('info'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccount()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Formular/Login'));

        $oView = $this->getMock('oxubase', array('getFncName'));
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue(null));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForShowLogin()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Uebersicht'));

        $oView = $this->getMock('oxubase', array('getFncName'));
        $oView->expects($this->exactly(2))->method('getFncName')->will($this->returnValue('showLogin'));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(3))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountFncLogout()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Formular/Logout'));

        $oView = $this->getMock('oxubase', array('getFncName'));
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue('logout'));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountFncSomefnc()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Uebersicht'));

        $oView = $this->getMock('oxubase', array('getFncName'));
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue('somefnc'));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountUser()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Kundendaten'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account_user'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountOrder()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Bestellungen'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account_order'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountNoticelist()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Merkzettel'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account_noticelist'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountNewsletter()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Newsletter'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account_newsletter'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForAccountWhishlist()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/Wunschzettel'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('account_whishlist'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForForgotpassword()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Login/PW vergessen'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('forgotpassword'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContent()
    {
        $aParams = null;
        $oSmarty = null;

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Content/testContent'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', '_getEmosPageTitle'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('content'));
        $oEmos->expects($this->once())->method('_getEmosPageTitle')->will($this->returnValue('testContent'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContentImpressum()
    {
        $aParams = null;
        $oSmarty = null;

        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxloadid = new oxfield('oximpressum');

        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Impressum'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('content'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContentAgb()
    {
        $aParams = null;
        $oSmarty = null;

        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxloadid = new oxfield('oxagb');

        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/AGB'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('content'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContentOrderinfo()
    {
        $aParams = null;
        $oSmarty = null;

        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxloadid = new oxfield('oxorderinfo');

        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Bestellinfo'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('content'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContentDeliveryinfo()
    {
        $aParams = null;
        $oSmarty = null;

        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxloadid = new oxfield('oxdeliveryinfo');

        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Versandinfo'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('content'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForContentSecurityinfo()
    {
        $aParams = null;
        $oSmarty = null;

        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxloadid = new oxfield('oxsecurityinfo');

        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->exactly(2))->method('getActiveView')->will($this->returnValue($oView));

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Info/Sicherheit'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('content'));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForRegisterError()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('newslettererror', -1);

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addRegister'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Register'));
        $oFormatter->expects($this->once())->method('addRegister')->with($this->equalTo('NULL'), $this->equalTo(1));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('register'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForRegisterSuccess()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('success', 1);
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');

        $oFormatter = $this->getMock('EMOS', array('addContent', 'addRegister'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Service/Register'));
        $oFormatter->expects($this->once())->method('addRegister')->with($this->equalTo('oxdefaultadmin'), $this->equalTo(0));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('register'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForLoginNoRedirect()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('lgn_usr', 'userlogin');

        $oFormatter = $this->getMock('EMOS', array('addLogin'));
        $oFormatter->expects($this->once())->method('addLogin')->with($this->equalTo('userlogin'), $this->equalTo(1));

        $oView = $this->getMock('oxubase', array('getFncName'));
        $oView->expects($this->exactly(2))->method('getFncName')->will($this->returnValue('login_noredirect'));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->any())->method('getActiveView')->will($this->returnValue($oView));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', 'getConfig'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForChangeBasketDecreaseAmount()
    {
        $aParams = null;
        $oSmarty = null;

        $oProduct = oxNew('oxArticle');
        $oProduct->load('1126');

        $oEmosItem = oxNew('EMOS_Item');
        $oEmosItem->productId = '1126';
        $oEmosItem->productName = 'Bar-Set ABSINTH';
        $oEmosItem->price = 34;
        $oEmosItem->productGroup = 'Bar-Equipment/Bar-Set ABSINTH';
        $oEmosItem->quantity = 10;
        $oEmosItem->variant1 = 'NULL';
        $oEmosItem->variant2 = 'NULL';
        $oEmosItem->variant3 = 'NULL';

        $aLastCall = array('changebasket' => array('1126' => array('oldam' => 5, 'am' => 15, 'aid' => '1126')));
        $this->getSession()->setVariable('aLastcall', $aLastCall);

        $oFormatter = $this->getMock('EMOS', array('addToBasket'));
        $oFormatter->expects($this->once())->method('addToBasket')->with($this->equalTo($oEmosItem));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getBasketProductCatPath', '_convProd2EmosItem'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getBasketProductCatPath')->will($this->returnValue('DeepestCategoryPath'));
        $oEmos->expects($this->once())->method('_convProd2EmosItem')->with($this->isInstanceOf(oxarticle), $this->equalTo('DeepestCategoryPath'), $this->equalTo(10))->will($this->returnValue($oEmosItem));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetCodeForFinalDefault()
    {
        $aParams = null;
        $oSmarty = null;

        $this->setRequestParameter('tpl', 'default.tpl');

        $oFormatter = $this->getMock('EMOS', array('addContent'));
        $oFormatter->expects($this->once())->method('addContent')->with($this->equalTo('Content/default'));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getEmosCl'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getEmosCl')->will($this->returnValue('default'));
        $oEmos->getCode($aParams, $oSmarty);
    }

    public function testGetScriptPath()
    {
        $oEmos = oxNew('oxEmosAdapter');
        $this->assertEquals($this->getConfig()->getShopUrl() . 'modules/econda/out/', $oEmos->UNITgetScriptPath());
    }

    public function testGetCodeForToBasket()
    {
        $aParams = null;
        $oSmarty = null;

        $oProduct = oxNew('oxArticle');
        $oProduct->load('1126');

        $oEmosItem = oxNew('EMOS_Item');
        $oEmosItem->productId = '1126';
        $oEmosItem->productName = 'Bar-Set ABSINTH';
        $oEmosItem->price = 34;
        $oEmosItem->productGroup = 'Bar-Equipment/Bar-Set ABSINTH';
        $oEmosItem->quantity = 10;
        $oEmosItem->variant1 = 'NULL';
        $oEmosItem->variant2 = 'NULL';
        $oEmosItem->variant3 = 'NULL';

        $aLastCall = array('tobasket' => array('1126' => array('am' => 1)));
        $this->getSession()->setVariable('aLastcall', $aLastCall);

        $oFormatter = $this->getMock('EMOS', array('addToBasket'));
        $oFormatter->expects($this->once())->method('addToBasket')->with($this->equalTo($oEmosItem));

        $oEmos = $this->getMock('oxEmosAdapter', array('getEmos', '_getBasketProductCatPath', '_convProd2EmosItem'));
        $oEmos->expects($this->once())->method('getEmos')->will($this->returnValue($oFormatter));
        $oEmos->expects($this->once())->method('_getBasketProductCatPath')->will($this->returnValue('DeepestCategoryPath'));
        $oEmos->expects($this->once())->method('_convProd2EmosItem')->with($this->equalTo($oProduct), $this->equalTo('DeepestCategoryPath'), $this->equalTo(1))->will($this->returnValue($oEmosItem));
        $oEmos->getCode($aParams, $oSmarty);
    }
}
