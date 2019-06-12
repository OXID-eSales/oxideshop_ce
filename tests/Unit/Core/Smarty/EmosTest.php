<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use OxidEsales\EshopCommunity\Core\Smarty\Plugin\Emos;
use \stdClass;

/**
 * Exposes protected methods for EMOS class
 *
 * @author Tomas Liubinas
 *
 */
class EmosHelper extends Emos
{

    /**
     * Returns protected property value
     *
     * @param mixed $mVar
     *
     * @return mixed
     */
    public function getProtected($mVar)
    {
        return $this->$mVar;
    }

    public function call_emos_ItemFormat($oItem)
    {
        return $this->_emos_ItemFormat($oItem);
    }

    public function call_emos_DataFormat($sStrPre)
    {
        return $this->_emos_DataFormat($sStrPre);
    }

    public function call_prepareScript()
    {
        return $this->_prepareScript();
    }

    public function call_setEmosECPageArray($oItem, $sEvent)
    {
        return $this->_setEmosECPageArray($oItem, $sEvent);
    }

    public function call_setEmosBillingArray($sBillingId, $sCustomerNr, $iTotal, $sCountry, $sCipt, $sCity, $sArrayName)
    {
        return $this->_setEmosBillingArray($sBillingId, $sCustomerNr, $iTotal, $sCountry, $sCipt, $sCity, $sArrayName);
    }
}

/**
 * Testing emos class
 */
class EmosTest extends \OxidTestCase
{

    /**
     * Test constructor.
     *
     * @return null
     */
    public function testConstruct()
    {
        $oEmos = new EmosHelper("xxx", "yyy");

        $this->assertEquals('xxx', $oEmos->getProtected("_sPathToFile"));
        $this->assertEquals('yyy', $oEmos->getProtected("_sScriptFileName"));
    }

    /**
     * Test item formating.
     *
     * @return null
     */
    public function testEmosItemFormat()
    {
        $oItem = new stdClass;
        $oItem->productId = 'prodid';
        $oItem->productName = 'prodname';
        $oItem->productGroup = 'prodgrp';
        $oItem->variant1 = 'var1';
        $oItem->variant2 = 'var2';
        $oItem->variant3 = 'var3';

        $oEmos = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty\EmosHelper::class, array('_emos_DataFormat'));
        $oEmos->expects($this->at(0))->method('_emos_DataFormat')->with($this->equalTo('prodid'))->will($this->returnValue('prodid'));
        $oEmos->expects($this->at(1))->method('_emos_DataFormat')->with($this->equalTo('prodname'))->will($this->returnValue('prodname'));
        $oEmos->expects($this->at(2))->method('_emos_DataFormat')->with($this->equalTo('prodgrp'))->will($this->returnValue('prodgrp'));
        $oEmos->expects($this->at(3))->method('_emos_DataFormat')->with($this->equalTo('var1'))->will($this->returnValue('var1'));
        $oEmos->expects($this->at(4))->method('_emos_DataFormat')->with($this->equalTo('var2'))->will($this->returnValue('var2'));
        $oEmos->expects($this->at(5))->method('_emos_DataFormat')->with($this->equalTo('var3'))->will($this->returnValue('var3'));

        $this->assertEquals($oItem, $oEmos->call_emos_ItemFormat($oItem));
    }

    /**
     * Test data formating.
     *
     * @return null
     */
    public function testEmosDataFormat()
    {
        $sStrPre = '  &amp;&quot;&gt;<a href="">ggg</a>\'"%;   / /';
        $sStrPos = '&>ggg//';

        $oEmos = new EmosHelper;
        $this->assertEquals($sStrPos, $oEmos->call_emos_DataFormat($sStrPre));
    }

    /**
     * Test pretty print.
     *
     * @return null
     */
    public function testPrettyPrint()
    {
        $oEmos = new EmosHelper();
        $this->assertEquals("", $oEmos->getProtected("_br"));
        $this->assertEquals("", $oEmos->getProtected("_tab"));
        $oEmos->prettyPrint();
        $this->assertEquals("\n", $oEmos->getProtected("_br"));
        $this->assertEquals("\t", $oEmos->getProtected("_tab"));
    }

    /**
     * Test EMOS::_prepareScript() method.
     *
     * @return null
     */
    public function testPrepareScript()
    {
        $oEmos = new EmosHelper("xxx", "yyy");
        $oEmos->prettyPrint();
        $oEmos->call_prepareScript();

        $sRes = $oEmos->getProtected("_sIncScript");
        $sExpt = "<script type=\"text/javascript\" src=\"xxxyyy\"></script>\n";

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test to string.
     *
     * @return null
     */
    public function testToString()
    {
        $oEmos = new Emos("xxx", "yyy");
        //$oEmos->appendPreScript( 'pre' );
        //$oEmos->appendPostScript( 'post' );
        //$oEmos->jsFormatPrescript = "__JSPreScript__";
        //$oEmos->jsFormatScript = "__JSScript__";

        //$sExpt = "pre<script type=\"text/javascript\">window.emosTrackVersion = 2;</script>\n<script type=\"text/javascript\" src=\"xxxyyy\"></script>\n<script type=\"text/javascript\"><!--\n\tvar emospro = {};\n\twindow.emosPropertiesEvent(emospro);\n//-->\n</script>\npost";
        $sExpt = "<script type=\"text/javascript\">window.emosTrackVersion = 2;</script>\n<script type=\"text/javascript\" src=\"xxxyyy\"></script>\n<script type=\"text/javascript\"><!--\n\tvar emospro = {};\n\twindow.emosPropertiesEvent(emospro);\n//-->\n</script>\n";
        $oEmos->prettyPrint();
        $this->assertEquals($sExpt, $oEmos->toString());
    }

    /**
     * Test get emos EC page array.
     *
     * @return null
     */
    public function testSetEmosECPageArray()
    {
        $oItem = oxNew('EMOS_Item');
        $oItem->productId = 'productId';
        $oItem->productName = 'product Name';
        $oItem->price = 'price';
        $oItem->productGroup = 'product\Group';
        $oItem->quantity = 'quantity';
        $oItem->variant1 = 'variant1';
        $oItem->variant2 = null;
        $oItem->variant3 = 'variant3';

        $oSubj = $this->getProxyClass("EMOS");
        $oSubj->UNITsetEmosECPageArray($oItem, "testEvent");
        $aExpt = array(array("testEvent", 'productId', 'product Name', 'price', 'product\Group', 'quantity', 'variant1', '', 'variant3'));
        $this->assertEquals($aExpt, $oSubj->getNonPublicVar("_ecEvent"));
    }

    /**
     * tests EMOS::prepareJsFormat() method. Sets internal params.
     *
     * @return null
     */
    public function testPrepareScriptExt()
    {
        $oEmos = $this->getProxyClass("EMOS");
        $oEmos->setNonPublicVar("_content", "testContents");
        $oEmos->UNITprepareScript();
        $sRes1 = $oEmos->getNonPublicVar("_sPrescript");
        $sRes2 = $oEmos->getNonPublicVar("_sPostscript");

        $this->assertContains("window.emosTrackVersion", $sRes1);
        $this->assertContains("window.emosPropertiesEvent(emospro)", $sRes2);
        $this->assertContains("var emospro = {};", $sRes2);
        $this->assertContains("content = \"testContents\"", $sRes2);
    }

    /**
     * tests EMOS::prepareJsFormat() method.
     *
     * @return null
     */
    public function testPrepareScriptNotContains()
    {
        $oEmos = $this->getProxyClass("EMOS");
        $oEmos->UNITprepareScript();
        $sRes1 = $oEmos->getNonPublicVar("_sPrescript");
        $sRes2 = $oEmos->getNonPublicVar("_sPostscript");

        $this->assertContains("window.emosTrackVersion", $sRes1);
        $this->assertContains("window.emosPropertiesEvent(emospro)", $sRes2);
        $this->assertContains("var emospro = {};", $sRes2);
        $this->assertNotContains("contents", $sRes2);
    }

    /**
     * Tests EMOS::_addJsFormat method.
     *
     * @return null
     */
    public function testAddJsFormat()
    {
        $oSubj = $this->getProxyClass("EMOS");
        $sRes = $oSubj->UNITaddJsFormat("contents", '111');
        $this->assertContains("emospro.contents = \"111\"", $sRes);

        $sRes = $oSubj->UNITaddJsFormat("contents", 111, true);
        $this->assertContains("emospro.contents = 111", $sRes);
    }

    /**
     * Tests EMOS::_addJsFormat method.
     *
     * @return null
     */
    public function testAddJsFormatArray()
    {
        $oSubj = $this->getProxyClass("EMOS");
        $sRes = $oSubj->UNITaddJsFormat("contents", array('111'));
        $this->assertContains("emospro.contents = [\"111\"]", $sRes);

        $sRes = $oSubj->UNITaddJsFormat("contents", array('111', '222'), true);
        $this->assertContains("emospro.contents = [\"111\",\"222\"]", $sRes);
    }

    /**
     * Tests EMOS::_addJsFormat method.
     *
     * @return null
     */
    public function testAddJsFormatNoQuotes()
    {
        $oSubj = $this->getProxyClass("EMOS");
        $sRes = $oSubj->UNITaddJsFormat("contents", 111);
        $this->assertContains("emospro.contents = 111", $sRes);

        $sRes = $oSubj->UNITaddJsFormat("contents", array(111, 222), true);
        $this->assertContains("emospro.contents = [111,222]", $sRes);
    }

    /**
     * Tests EMOS::_addJsFormat method.
     *
     * @return null
     */
    public function testAddJsFormatEmpty()
    {
        $oSubj = $this->getProxyClass("EMOS");
        $sRes = $oSubj->UNITaddJsFormat("contents", null);
        $this->assertNull($sRes);
    }


    /**
     * Tests EMOS::_addJsFormat method. Zero suplied
     *
     * @return null
     */
    public function testAddJsFormatZero()
    {
        $oSubj = $this->getProxyClass("EMOS");
        $sRes = $oSubj->UNITaddJsFormat("contents", '0');
        $this->assertContains("emospro.contents = \"0\"", $sRes);

        $sRes = $oSubj->UNITaddJsFormat("contents", 0);
        $this->assertContains("emospro.contents = 0", $sRes);
    }

    /**
     * Tests EMOS::_addJsFormat method. Special char test for bug #3105
     *
     * @return null
     */
    public function testAddJsSpaceChar()
    {
        $oSubj = $this->getProxyClass("EMOS");
        $sRes = $oSubj->UNITaddJsFormat("contents", '0 0');
        $this->assertContains("emospro.contents = \"0 0\"", $sRes);
    }

    /**
     * Tests EMOS::addContent() method. Checking refactored behaviour
     *
     * @return null
     */
    public function testAddContentRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addContent("Test test");
        $sRes = $oSubj->toString();

        $this->assertContains("emospro.content = \"Test test\";", $sRes);
    }

    /**
     * Tests EMOS::addContact() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddContactRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addContact("Test test");
        $sRes = $oSubj->toString();

        $this->assertContains("emospro.scontact = \"Test test\";", $sRes);
    }

    /**
     * Tests EMOS::addCountryId() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddCountryIdRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addCountryId(15);
        $sRes = $oSubj->toString();

        $this->assertContains("emospro.countryid = 15;", $sRes);
    }

    /**
     * Tests EMOS::addPageId() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddPageIdRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addPageId("123");
        $sRes = $oSubj->toString();

        $this->assertContains("emospro.pageId = \"123\";", $sRes);
    }

    /**
     * Tests EMOS::addRegister() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddRegisterRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addRegister("testUser", 1);
        $sRes = $oSubj->toString();
        $this->assertContains("emospro.register = [[\"33ef37db24f3a27fb520847dcd549e9f\",1]];", $sRes);
    }

    /**
     * Tests EMOS::addRegister() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddLoginRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addLogin("testUser", 1);
        $sRes = $oSubj->toString();
        $this->assertContains("emospro.login = [[\"33ef37db24f3a27fb520847dcd549e9f\",1]];", $sRes);
    }

    /**
     * Tests EMOS::addSearch() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddSearchRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addSearch("Test search", 15);
        $sRes = $oSubj->toString();
        $this->assertContains("emospro.search = [[\"Test search\",15]];", $sRes);
    }

    /**
     * Tests EMOS::addSiteId() method. Checking refactored behaviour
     *
     * @return null
     */
    public function testAddSiteIdRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addSiteId(1);
        $sRes = $oSubj->toString();

        $this->assertContains("emospro.siteid = 1;", $sRes);
    }

    /**
     * Tests EMOS::addDownload() method. Checking refactored behaviour
     *
     * @return null
     */
    public function testAddDownloadRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oSubj->addDownload("testDownlod");
        $sRes = $oSubj->toString();

        $this->assertContains('emospro.download = "testDownlod";', $sRes);
    }

    /**
     * Tests EMOS::addToBasket() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddToBasketRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oItem = oxNew('EMOS_Item');
        $oItem->productId = "123";
        $oItem->productName = "Test product";
        $oItem->price = 46.50;
        $oItem->productGroup = "Test/Category/";
        $oItem->quantity = 13;
        $oItem->variant1 = "var1";
        $oItem->variant2 = null;
        $oItem->variant3 = "var3";

        $oSubj->addToBasket($oItem);
        $sRes = $oSubj->toString();

        $sExpt = 'emospro.ec_Event = [["c_add","123","Test product",46.5,"Test\/Category\/",13,"var1",null,"var3"]];';
        $this->assertContains($sExpt, $sRes);
    }

    /**
     * Tests remove item from basket event.
     *
     * @return null
     */
    public function testRemoveFromBasketRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oItem = oxNew('EMOS_Item');
        $oItem->productId = "123";
        $oItem->productName = "Test product";
        $oItem->price = 46.50;
        $oItem->productGroup = "Test/Category/";
        $oItem->quantity = 13;
        $oItem->variant1 = "var1";
        $oItem->variant2 = null;
        $oItem->variant3 = "var3";

        $oSubj->removeFromBasket($oItem);
        $sRes = $oSubj->toString();

        $sExpt = 'emospro.ec_Event = [["c_rmv","123","Test product",46.5,"Test\/Category\/",13,"var1",null,"var3"]];';
        $this->assertContains($sExpt, $sRes);
    }

    /**
     * Tests buy items event.
     *
     * @return null
     */
    public function testAddEmosBasketPageArrayRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oItem1 = oxNew('EMOS_Item');
        $oItem1->productId = "1";
        $oItem1->productName = "Prod 1";
        $oItem1->price = 46.50;
        $oItem1->productGroup = "Test/Cat 1/";
        $oItem1->quantity = 13;
        $oItem1->variant1 = "var11";
        $oItem1->variant2 = null;
        $oItem1->variant3 = "var13";

        $oItem2 = oxNew('EMOS_Item');
        $oItem2->productId = "2";
        $oItem2->productName = "Prod 2";
        $oItem2->price = 46.51;
        $oItem2->productGroup = "Test/Cat 2/";
        $oItem2->quantity = 13;
        $oItem2->variant1 = null;
        $oItem2->variant2 = null;
        $oItem2->variant3 = "var3";

        $aBasket = array($oItem1, $oItem2);

        $oSubj->addEmosBasketPageArray($aBasket);
        $sRes = $oSubj->toString();

        $sExpt = 'emospro.ec_Event = [["buy","1","Prod 1",46.5,"Test\/Cat 1\/",13,"var11",null,"var13"],["buy","2","Prod 2",46.51,"Test\/Cat 2\/",13,null,null,"var3"]];';
        $this->assertContains($sExpt, $sRes);
    }

    /**
     * Tests EMOS::addDetailView() method. Checking refactored behaviour.
     *
     * @return null
     */
    public function testAddDetailViewRefactored()
    {
        $oSubj = oxNew('EMOS');

        $oItem = oxNew('EMOS_Item');
        $oItem->productId = "123";
        $oItem->productName = "Test product";
        $oItem->price = 46.50;
        $oItem->productGroup = "Test/Category/";
        $oItem->quantity = 13;
        $oItem->variant1 = "var1";
        $oItem->variant2 = null;
        $oItem->variant3 = "var3";

        $oSubj->addDetailView($oItem);
        $sRes = $oSubj->toString();
        $sExpt = 'emospro.ec_Event = [["view","123","Test product",46.5,"Test\/Category\/",13,"var1",null,"var3"]];';
        $this->assertContains($sExpt, $sRes);
    }

    /**
     * Tests EMOS::_jsEncode() method. For String.
     *
     * @return null
     */
    public function testJsEncodeString()
    {
        $oSubj = $this->getProxyClass("EMOS");

        $mInput = "Test";
        $sExpt = '"Test"';
        $sRes = $oSubj->UNITjsEncode($mInput);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Tests EMOS::_jsEncode() method.
     *
     * @return null
     */
    public function testJsEncodeStringSpecialChars()
    {
        $oSubj = $this->getProxyClass("EMOS");

        $mInput = "Test test";
        $sExpt = '"Test test"';
        $sRes = $oSubj->UNITjsEncode($mInput);
        $this->assertEquals($sExpt, $sRes);

        $mInput = "Test/test";
        $sExpt = '"Test\/test"';
        $sRes = $oSubj->UNITjsEncode($mInput);
        $this->assertEquals($sExpt, $sRes);

        $mInput = "Test\"test";
        $sExpt = '"Test\"test"';
        $sRes = $oSubj->UNITjsEncode($mInput);
        $this->assertEquals($sExpt, $sRes);

        $mInput = "Test'test";
        $sExpt = '"Test\'test"';
        $sRes = $oSubj->UNITjsEncode($mInput);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Tests EMOS::_jsEncode() method. For arrays.
     *
     * @return null
     */
    public function testJsEncodeArray()
    {
        $oSubj = $this->getProxyClass("EMOS");

        $mInput = array("one", 2, 3 => "four",);
        $sExpt = '{"0":"one","1":2,"3":"four"}';
        $sRes = $oSubj->UNITjsEncode($mInput);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Tests EMOS::_jsEncode() method. For associative arrays.
     *
     * @return null
     */
    public function testJsEncodeArrayAssoc()
    {
        $oSubj = $this->getProxyClass("EMOS");

        $mInput = array("a" => "one", 2, "three" => "four");
        $sExpt = '{"a":"one","0":2,"three":"four"}';
        $sRes = $oSubj->UNITjsEncode($mInput);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Tests EMOS::_jsEncode() method. Multidimensional arrays
     *
     * @return null
     */
    public function testJsEncodeArrayArray()
    {
        $oSubj = $this->getProxyClass("EMOS");

        $mInput = array(array("one", 23), array("four five", 6), 7);
        $sExpt = '[["one",23],["four five",6],7]';
        $sRes = $oSubj->UNITjsEncode($mInput);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Tests EMOS::_jsEncode() method. Null case.
     *
     * @return null
     */
    public function testJsEncodeNull()
    {
        $oSubj = $this->getProxyClass("EMOS");

        $mInput = null;
        $sExpt = 'null';
        $sRes = $oSubj->UNITjsEncode($mInput);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Tests EMOS::addEmosBillingPageArray() method.
     *
     * @return null
     */
    public function testAddEmosBillingPageArray()
    {
        $oSubj = oxNew('EMOS');
        $oSubj->addEmosBillingPageArray("sBillingId", "sCustomerNumber", 0, "de", "cip", "Halle");

        $sRes = $oSubj->toString();
        $sExpt = 'emospro.billing = [["sBillingId","4b6f45defafe0ed53345cad1b77205bd","de\/c\/ci\/Halle\/cip",0]];';

        $this->assertContains($sExpt, $sRes);
    }

    /**
     * Tests EMOS::addToBasket() method. Ensures, that "view" event is not exported together with c_add event.
     * #3139 case.
     *
     * return null;
     */
    public function testShowAddOnly()
    {
        $oSubj = oxNew('EMOS');

        $oItem = oxNew('EMOS_Item');
        $oItem->productId = "123";
        $oItem->productName = "Test product";
        $oItem->price = 46.50;
        $oItem->productGroup = "Test/Category/";
        $oItem->quantity = 13;
        $oItem->variant1 = "var1";
        $oItem->variant2 = null;
        $oItem->variant3 = "var3";

        $oSubj->addDetailView($oItem);
        $oSubj->addToBasket($oItem);
        $sRes = $oSubj->toString();

        $sExpt = '["c_add","123","Test product",46.5,"Test\/Category\/",13,"var1",null,"var3"]';
        $this->assertContains($sExpt, $sRes);
        $this->assertNotContains("view", $sRes);
    }
}
