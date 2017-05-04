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

class Unit_Views_suggestTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();
        $sDelete = 'delete from oxrecommlists where oxid like "testlist%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxobject2list where oxlistid like "testlist%" ';
        $myDB->execute($sDelete);

        parent::tearDown();
    }

    public function testGetProduct()
    {
        modConfig::setRequestParameter('anid', '2000');
        $oSuggest = $this->getProxyClass("suggest");

        $this->assertEquals('2000', $oSuggest->getProduct()->getId());
    }

    public function testGetCrossSelling()
    {
        $oSuggest = $this->getProxyClass("suggest");
        $oArticle = oxNew("oxarticle");
        $oArticle->load("1849");
        $oSuggest->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oSuggest->getCrossSelling();
        $this->assertTrue($oList instanceof oxList);
        $iCount = 3;
        $iCount = 2;
        $this->assertEquals($iCount, $oList->count());
    }

    public function testGetSimilarProducts()
    {
        $oSuggest = $this->getProxyClass("suggest");
        $oArticle = oxNew("oxarticle");
        $oArticle->load("2000");
        $oSuggest->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oSuggest->getSimilarProducts();
        $this->assertTrue($oList instanceof oxList);
        $iCount = 4;
        $iCount = 5;
        $this->assertEquals($iCount, count($oList));
    }

    public function testGetRecommList()
    {
        $myDB = oxDb::getDB();
        $sShopId = oxRegistry::getConfig()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "2000", "testlist", "test" ) ';
        $myDB->Execute($sQ);

        $oSuggest = $this->getProxyClass("suggest");
        $oArticle = oxNew("oxarticle");
        $oArticle->load('2000');
        $oSuggest->setNonPublicVar("_oProduct", $oArticle);
        $aLists = $oSuggest->getRecommList();
        $this->assertTrue($aLists instanceof oxList);
        $this->assertTrue($aLists instanceof oxList);
        $this->assertEquals(1, $aLists->count());
        $this->assertEquals('testlist', $aLists['testlist']->getId());
        $this->assertTrue(in_array($aLists['testlist']->getFirstArticle()->getId(), array('2000')));
    }

    public function testGetSuggestData()
    {
        oxTestModules::addFunction('oxCaptcha', 'pass', '{return true;}');
        modConfig::setRequestParameter('editval', array('name' => 'test', 'value' => 'testvalue'));

        /** @var Suggest $oSuggest */
        $oSuggest = $this->getProxyClass("suggest");
        $oSuggest->send();

        $oParam = $oSuggest->getSuggestData();

        $this->assertEquals('test', $oParam->name);
        $this->assertEquals('testvalue', $oParam->value);
    }

    public function testSendSuggestWithoutCaptcha()
    {
        modConfig::setRequestParameter('editval', array('name' => 'test', 'value' => 'testvalue'));

        /** @var Suggest $oSuggest */
        $oSuggest = $this->getProxyClass("suggest");
        $this->assertFalse($oSuggest->send());
    }

    public function testGetLink()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oCfg = oxRegistry::getConfig();
        $oV = $this->getMock('suggest', array('_getRequestParams', '_getSeoRequestParams'));
        $oV->expects($this->any())->method('_getRequestParams')->will($this->returnValue('cl=suggest'));
        $oV->expects($this->any())->method('_getSeoRequestParams')->will($this->returnValue('cl=suggest'));

        $sCnid = '8a142c3e60a535f16.78077188';
        modConfig::setRequestParameter('anid', '2000');
        modConfig::setRequestParameter('cnid', $sCnid);
        $this->assertEquals($oCfg->getShopURL() . 'empfehlen/?cnid=' . $sCnid . '&amp;anid=2000', $oV->getLink());
        $this->assertEquals($oCfg->getShopURL() . 'empfehlen/?cnid=' . $sCnid . '&amp;anid=2000', $oV->getLink(0));
        $this->assertEquals($oCfg->getShopURL() . 'en/recommend/?cnid=' . $sCnid . '&amp;anid=2000', $oV->getLink(1));
    }

    public function testGetCaptcha()
    {
        $oSuggest = $this->getProxyClass('suggest');
        $this->assertEquals(oxNew('oxCaptcha'), $oSuggest->getCaptcha());
    }


    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testgetRecommListsIfOff()
    {
        $oCfg = $this->getMock("stdClass", array("getShowListmania"));
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        $oSuggest = $this->getMock("suggest", array("getViewConfig", 'getArticleList'));
        $oSuggest->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oSuggest->expects($this->never())->method('getArticleList');

        $this->assertSame(false, $oSuggest->getRecommList());
    }

    public function testRender()
    {
        $oSuggest = $this->getProxyClass("suggest");
        $this->assertSame('page/info/suggest.tpl', $oSuggest->render());
    }

    public function testSendNoEditval()
    {
        modConfig::setRequestParameter('editval', null);

        /** @var Suggest $oSuggest */
        $oSuggest = oxnew('Suggest');
        $this->assertSame(null, $oSuggest->send());
    }

    public function testSendPass()
    {
        modConfig::setRequestParameter(
            'editval',
            array(
                'name'         => 'test',
                'value'        => 'testvalue',
                'rec_name'     => 'test1',
                'rec_email'    => 'recmail@oxid.lt',
                'send_name'    => 'test3',
                'send_email'   => 'sendmail@oxid.lt',
                'send_message' => 'test5',
                'send_subject' => 'test6',
            )
        );

        $oEmail = $this->getMock("stdclass", array('sendSuggestMail'));
        $oEmail->expects($this->once())->method('sendSuggestMail')
            ->will($this->returnValue(1));

        oxTestModules::addModuleObject('oxemail', $oEmail);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock("oxarticle", array('getId'));
        $oProduct->expects($this->once())->method('getId')->will($this->returnValue('XProduct'));

        $oCaptcha = $this->getMock("stdclass", array('pass'));
        $oCaptcha->expects($this->once())->method('pass')->will($this->returnValue(true));

        /** @var Suggest|PHPUnit_Framework_MockObject_MockObject $oSuggest */
        $oSuggest = $this->getMock("suggest", array("getProduct", 'getCaptcha'));
        $oSuggest->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));
        $oSuggest->expects($this->once())->method('getCaptcha')->will($this->returnValue($oCaptcha));

        modConfig::setRequestParameter('searchparam', "searchparam&&A");
        modConfig::setRequestParameter('searchcnid', "searchcnid&&A");
        modConfig::setRequestParameter('searchvendor', "searchvendor&&A");
        modConfig::setRequestParameter('searchmanufacturer', "searchmanufacturer&&A");
        modConfig::setRequestParameter('listtype', "listtype&&A");

        $sExpected = 'details?anid=XProduct&searchparam=searchparam%26%26A&searchcnid=searchcnid&amp;&amp;A&searchvendor=searchvendor&amp;&amp;A&searchmanufacturer=searchmanufacturer&amp;&amp;A&listtype=listtype&amp;&amp;A';
        $this->assertEquals($sExpected, $oSuggest->send());
    }

    public function testSendPassInvalidMail()
    {
        modConfig::setRequestParameter(
            'editval',
            array(
                'name'         => 'test',
                'value'        => 'testvalue',
                'rec_name'     => 'test1',
                'rec_email'    => 'test2',
                'send_name'    => 'test3',
                'send_email'   => 'test4',
                'send_message' => 'test5',
                'send_subject' => 'test6',
            )
        );

        $oEmail = $this->getMock("stdclass", array('sendSuggestMail'));
        $oEmail->expects($this->never())->method('sendSuggestMail');

        oxTestModules::addModuleObject('oxemail', $oEmail);

        $oProduct = $this->getMock("stdclass", array('getId'));
        $oProduct->expects($this->never())->method('getId');

        $oCaptcha = $this->getMock("stdclass", array('pass'));
        $oCaptcha->expects($this->once())->method('pass')->will($this->returnValue(true));

        /** @var Suggest|PHPUnit_Framework_MockObject_MockObject $oSuggest */
        $oSuggest = $this->getMock("suggest", array("getProduct", 'getCaptcha'));
        $oSuggest->expects($this->never())->method('getProduct')->will($this->returnValue($oProduct));
        $oSuggest->expects($this->once())->method('getCaptcha')->will($this->returnValue($oCaptcha));

        $oUtilsView = $this->getMock("stdclass", array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay');

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $this->assertEquals('', $oSuggest->send());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oSuggest = $this->getProxyClass("suggest");
        $aResults = array();
        $aResult = array();

        $aResult["title"] = oxRegistry::getLang()->translateString('RECOMMEND_PRODUCT', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oSuggest->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oSuggest->getBreadCrumb());
    }
}
