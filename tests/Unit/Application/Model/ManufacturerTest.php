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
namespace Unit\Application\Model;

use \OxidEsales\EshopCommunity\Application\Model\Manufacturer;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxmanufacturer class
 */
class ManufacturerTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxTestModules::addFunction('oxManufacturer', 'cleanRootManufacturer', '{oxManufacturer::$_aRootManufacturer = array();}');
        oxNew('oxManufacturer')->cleanRootManufacturer();

        parent::tearDown();
    }

    public function testGetBaseSeoLinkForPage()
    {
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerUrl", "{return 'sManufacturerUrl';}");
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerPageUrl", "{return 'sManufacturerPageUrl';}");

        $oManufacturer = oxNew('oxManufacturer');
        $this->assertEquals("sManufacturerPageUrl", $oManufacturer->getBaseSeoLink(0, 1));
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerUrl", "{return 'sManufacturerUrl';}");
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerPageUrl", "{return 'sManufacturerPageUrl';}");

        $oManufacturer = oxNew('oxManufacturer');
        $this->assertEquals("sManufacturerUrl", $oManufacturer->getBaseSeoLink(0));
    }

    public function testGetBaseStdLink()
    {
        $iLang = 0;

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("testManufacturerId");

        $sTestUrl = $this->getConfig()->getShopHomeUrl($iLang, false) . 'cl=manufacturerlist&amp;mnid=' . $oManufacturer->getId();
        $this->assertEquals($sTestUrl, $oManufacturer->getBaseStdLink($iLang));
    }

    public function testGetContentCats()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $this->assertNull($oManufacturer->getContentCats());
    }

    public function testMagicGetter()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->oxmanufacturers__oxicon = new oxField('big_matsol_1_mico.png');

        $this->assertEquals('big_matsol_1_mico.png', basename($oManufacturer->getIconUrl()));


        $oManufacturer = $this->getMock('oxManufacturer', array('getLink', 'getNrOfArticles', 'getIsVisible', 'getHasVisibleSubCats'));

        $oManufacturer->expects($this->exactly(4))->method('getLink')->will($this->returnValue('Link'));
        $oManufacturer->expects($this->once())->method('getNrOfArticles')->will($this->returnValue('NrOfArticles'));
        $oManufacturer->expects($this->once())->method('getIsVisible')->will($this->returnValue('IsVisible'));
        $oManufacturer->expects($this->once())->method('getHasVisibleSubCats')->will($this->returnValue('HasVisibleSubCats'));

        $this->assertEquals('Link', $oManufacturer->oxurl);
        $this->assertEquals('Link', $oManufacturer->openlink);
        $this->assertEquals('Link', $oManufacturer->closelink);
        $this->assertEquals('Link', $oManufacturer->link);
        $this->assertEquals('NrOfArticles', $oManufacturer->iArtCnt);
        $this->assertEquals('IsVisible', $oManufacturer->isVisible);
        $this->assertEquals('HasVisibleSubCats', $oManufacturer->hasVisibleSubCats);
    }

    public function testAssignWithoutArticleCnt()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $oManufacturer = oxNew('oxManufacturer');
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '"';
        $sManufacturerId = $myDB->getOne($sQ);
        $oManufacturer->setShowArticleCnt(false);
        $oManufacturer->load($sManufacturerId);

        $iArticleCount = -1;

        $this->assertEquals($iArticleCount, $oManufacturer->oxmanufacturers__oxnrofarticles->value);
    }

    public function testAssignWithArticleCnt()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '"';
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = "select count(*) from oxarticles where oxmanufacturerid = '$sManufacturerId' ";
        $iCnt = $myDB->getOne($sQ);

        $oManufacturer = $this->getMock('oxManufacturer', array('isAdmin'));
        $oManufacturer->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oManufacturer->setShowArticleCnt(true);
        $oManufacturer->load($sManufacturerId);


        $this->assertEquals($oManufacturer->oxmanufacturers__oxnrofarticles->value, $oManufacturer->iArtCnt);
        $this->assertEquals($iCnt, $oManufacturer->iArtCnt);
    }

    public function testGetStdLink()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx', $oManufacturer->getStdLink());
    }

    public function testGetLinkSeoDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';

        $myDB = oxDb::getDB();
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setLanguage(0);
        $oManufacturer->load($sManufacturerId);

        $this->assertEquals($this->getConfig()->getShopUrl() . 'Nach-Hersteller/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink());
    }

    public function testGetLinkSeoEng()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle_1 from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->loadInLang(1, $sManufacturerId);

        $this->assertEquals($this->getConfig()->getShopUrl() . 'en/By-manufacturer/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink());
    }

    public function testGetLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx', $oManufacturer->getLink());
    }

    public function testGetStdLinkWithLangParam()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx&amp;lang=1', $oManufacturer->getStdLink(1));
    }

    public function testGetLinkSeoDeWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';

        $myDB = oxDb::getDB();
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setLanguage(1);
        $oManufacturer->load($sManufacturerId);

        $this->assertEquals($this->getConfig()->getShopUrl() . 'Nach-Hersteller/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink(0));
    }

    public function testGetLinkSeoEngWithLangParam()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle_1 from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->loadInLang(0, $sManufacturerId);

        $this->assertEquals($this->getConfig()->getShopUrl() . 'en/By-manufacturer/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink(1));
    }

    public function testGetLinkWithLangParam()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx&amp;lang=1', $oManufacturer->getLink(1));
    }

    public function testLoadRootManufacturer()
    {
        $oV = oxNew('oxManufacturer');
        $oV->load('root');
        $this->assertTrue($oV instanceof Manufacturer);
        $this->assertEquals('root', $oV->getId());

        $oV = oxNew('oxManufacturer');
        $oV->loadInLang(0, 'root');
        $this->assertEquals(0, $oV->getLanguage());

        $oV = oxNew('oxManufacturer');
        $oV->loadInLang(1, 'root');
        $this->assertEquals(1, $oV->getLanguage());

        $oV = oxNew('oxManufacturer');
        $oV->load('root');
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oV->getLanguage());
    }

    public function testGetNrOfArticles()
    {
        $sManufacturerId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sManufacturerId = '88a996f859f94176da943f38ee067984';
        }

        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->setNonPublicVar("_blShowArticleCnt", true);
        $oManufacturer->load($sManufacturerId);

        $this->assertEquals(oxRegistry::get("oxUtilsCount")->getManufacturerArticleCount($sManufacturerId), $oManufacturer->getNrOfArticles());
    }

    public function testGetNrOfArticlesDonotShow()
    {
        $sManufacturerId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sManufacturerId = '88a996f859f94176da943f38ee067984';
        }

        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->load($sManufacturerId);
        $oManufacturer->setNonPublicVar("_blShowArticleCnt", false);

        $this->assertEquals(-1, $oManufacturer->getNrOfArticles());
    }

    public function testSetGetIsVisible()
    {
        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->setIsVisible(true);

        $this->assertTrue($oManufacturer->getIsVisible());
    }

    public function testSetGetHasVisibleSubCats()
    {
        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->setHasVisibleSubCats(true);

        $this->assertTrue($oManufacturer->getHasVisibleSubCats());
    }

    public function testGetHasVisibleSubCatsNotSet()
    {
        $oManufacturer = $this->getProxyClass("oxManufacturer");

        $this->assertFalse($oManufacturer->getHasVisibleSubCats());
    }

    // #M366: Upload of manufacturer and categories icon does not work
    public function testGetIconUrl()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->oxmanufacturers__oxicon = new oxField('big_matsol_1_mico.png');

        $this->assertEquals('big_matsol_1_mico.png', basename($oManufacturer->getIconUrl()));
    }

    /**
     * Test case for new folder structure icon getter
     *
     * @return null
     */
    public function testGetIconUrlAccordingToNewFilesStructure()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with('sManufacturerIconsize')->will($this->returnValue(false));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with('sIconsize')->will($this->returnValue('87*87'));

        $oManufacturer = $this->getMock("oxManufacturer", array("getConfig"), array(), '', false);
        $oManufacturer->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConfig));
        $oManufacturer->oxmanufacturers__oxicon = new oxField('big_matsol_1_mico.png');

        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/manufacturer/icon/87_87_75/big_matsol_1_mico.png";

        $this->assertEquals($sUrl, $oManufacturer->getIconUrl());
    }

    public function testDelete()
    {
        oxTestModules::addFunction('oxSeoEncoderManufacturer', 'onDeleteManufacturer', '{$this->onDelete[] = $aA[0];}');
        oxRegistry::get("oxSeoEncoderManufacturer")->onDelete = array();

        $obj = oxNew('oxManufacturer');
        $this->assertEquals(false, $obj->delete());
        $this->assertEquals(0, count(oxRegistry::get("oxSeoEncoderManufacturer")->onDelete));
        $this->assertEquals(false, $obj->exists());

        $obj->save();
        $this->assertEquals(true, $obj->delete());
        $this->assertEquals(false, $obj->exists());
        $this->assertEquals(1, count(oxRegistry::get("oxSeoEncoderManufacturer")->onDelete));
        $this->assertSame($obj, oxRegistry::get("oxSeoEncoderManufacturer")->onDelete[0]);
    }

    public function testGetStdLinkWithParams()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx&amp;foo=bar', $oManufacturer->getStdLink(0, array('foo' => 'bar')));
    }

    public function testGetThumbUrl()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $this->assertFalse($oManufacturer->getThumbUrl());
    }

    /**
     * Title getter test
     *
     * @return null
     */
    public function testGetTitle()
    {
        $sTitle = "testtitle";
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->oxmanufacturers__oxtitle = new oxField("testtitle");
        $this->assertEquals($sTitle, $oManufacturer->getTitle());
    }
}
