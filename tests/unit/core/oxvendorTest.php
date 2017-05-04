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
 * Testing oxvendor class
 */
class Unit_Core_oxvendorTest extends OxidTestCase
{

    //
    protected $_sVndIcon = "/vendor/icon/big_matsol_1_mico.png";
    protected $_sManIcon = "/manufacturer/icon/big_matsol_1_mico.png";

    /**
     * Test setup
     *
     * @return null
     */
    protected function setUp()
    {
        // test require icon for vendors
        if ($this->getName() == "testGetIconUrlNewPath" || $this->getName() == "testGetIconUrl") {
            $oConfig = $this->getConfig();
            $oConfig->init();
            $sTarget = $oConfig->getPicturePath("") . "master";
            if (file_exists($sTarget . $this->_sManIcon)) {
                copy($sTarget . $this->_sManIcon, $sTarget . $this->_sVndIcon);
            }
        }

        return parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // removing folder
        if ($this->getName() == "testGetIconUrlNewPath" || $this->getName() == "testGetIconUrl") {
            $sTarget = oxRegistry::getConfig()->getPicturePath("") . "master";
            if (file_exists($sTarget . $this->_sVndIcon)) {
                unlink($sTarget . $this->_sVndIcon);
            }
        }

        oxTestModules::addFunction('oxVendor', 'cleanRootVendor', '{oxVendor::$_aRootVendor = array();}');
        oxNew('oxvendor')->cleanRootVendor();

        parent::tearDown();
    }

    public function testGetBaseSeoLinkForPage()
    {
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorUrl", "{return 'sVendorUrl';}");
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorPageUrl", "{return 'sVendorPageUrl';}");

        $oVendor = new oxvendor();
        $this->assertEquals("sVendorPageUrl", $oVendor->getBaseSeoLink(0, 1));
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorUrl", "{return 'sVendorUrl';}");
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorPageUrl", "{return 'sVendorPageUrl';}");

        $oVendor = new oxvendor();
        $this->assertEquals("sVendorUrl", $oVendor->getBaseSeoLink(0));
    }

    public function testGetBaseStdLink()
    {
        $iLang = 0;

        $oVendor = new oxvendor();
        $oVendor->setId("testVendorId");

        $sTestUrl = oxRegistry::getConfig()->getConfig()->getShopHomeUrl($iLang, false) . "cl=vendorlist&amp;cnid=v_" . $oVendor->getId();
        $this->assertEquals($sTestUrl, $oVendor->getBaseStdLink($iLang));
    }

    public function testGetContentCats()
    {
        $oVendor = new oxvendor();
        $this->assertNull($oVendor->getContentCats());
    }

    // #M366: Upload of manufacturer and categories icon does not work
    public function testGetIconUrl()
    {
        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->oxvendor__oxicon = new oxField('big_matsol_1_mico.png');

        $this->assertEquals('big_matsol_1_mico.png', basename($oVendor->getIconUrl()));
    }

    public function testAssignWithoutArticleCnt()
    {
        $myConfig = oxRegistry::getConfig();
        $myDB = oxDb::getDB();

        $oVendor = oxNew('oxvendor');
        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $sVendorId = $myDB->getOne($sQ);
        $oVendor->setShowArticleCnt(false);
        $oVendor->load($sVendorId);

        $iArticleCount = -1;

        $this->assertEquals($iArticleCount, $oVendor->oxvendor__oxnrofarticles->value);
    }

    public function testAssignWithArticleCnt()
    {
        $myConfig = oxRegistry::getConfig();
        $myDB = oxDb::getDB();

        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $sVendorId = $myDB->getOne($sQ);

        $sQ = "select count(*) from oxarticles where oxvendorid = '$sVendorId' ";
        $iCnt = $myDB->getOne($sQ);

        $oVendor = $this->getMock('oxvendor', array('isAdmin'));
        $oVendor->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oVendor->setShowArticleCnt(true);
        $oVendor->load($sVendorId);


        $this->assertEquals($oVendor->oxvendor__oxnrofarticles->value, $oVendor->getNrOfArticles());
        $this->assertEquals($iCnt, $oVendor->getNrOfArticles());
    }

    public function testGetStdLink()
    {
        $oVendor = new oxvendor();
        $oVendor->setId('xxx');
        $this->assertEquals(oxRegistry::getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx', $oVendor->getStdLink());
    }

    public function testGetLinkSeoDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first vendor from db
        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . oxRegistry::getConfig()->getShopID() . '"';

        $myDB = oxDb::getDB();
        $sVendorId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle from oxvendor where oxvendor.oxshopid = "' . oxRegistry::getConfig()->getShopID() . '"';
        $sVendorTitle = $myDB->getOne($sQ);

        $oVendor = new oxvendor();
        $oVendor->setLanguage(0);
        $oVendor->load($sVendorId);

        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'Nach-Lieferant/' . str_replace(' ', '-', $sVendorTitle) . '/', $oVendor->getLink());
    }

    public function testGetLinkSeoEng()
    {
        $myConfig = oxRegistry::getConfig();
        $myDB = oxDb::getDB();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first vendor from db
        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $sVendorId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle_1 from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $sVendorTitle = $myDB->getOne($sQ);

        $oVendor = new oxvendor();
        $oVendor->loadInLang(1, $sVendorId);

        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'en/By-Distributor/' . str_replace(' ', '-', $sVendorTitle) . '/', $oVendor->getLink());
    }

    public function testGetLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oVendor = new oxvendor();
        $oVendor->setId('xxx');

        $this->assertEquals(oxRegistry::getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx', $oVendor->getLink());
    }

    public function testGetStdLinkWithLangParam()
    {
        $oVendor = new oxvendor();
        $oVendor->setId('xxx');
        $this->assertEquals(oxRegistry::getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx&amp;lang=1', $oVendor->getStdLink(1));
    }

    public function testGetLinkSeoDeWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        // fetching first vendor from db
        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . oxRegistry::getConfig()->getShopID() . '"';

        $myDB = oxDb::getDB();
        $sVendorId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle from oxvendor where oxvendor.oxshopid = "' . oxRegistry::getConfig()->getShopID() . '"';
        $sVendorTitle = $myDB->getOne($sQ);

        $oVendor = new oxvendor();
        $oVendor->setLanguage(1);
        $oVendor->load($sVendorId);

        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'Nach-Lieferant/' . str_replace(' ', '-', $sVendorTitle) . '/', $oVendor->getLink(0));
    }

    public function testGetLinkSeoEngWithLangParam()
    {
        $myConfig = oxRegistry::getConfig();
        $myDB = oxDb::getDB();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first vendor from db
        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $sVendorId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle_1 from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $sVendorTitle = $myDB->getOne($sQ);

        $oVendor = new oxvendor();
        $oVendor->loadInLang(0, $sVendorId);

        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'en/By-Distributor/' . str_replace(' ', '-', $sVendorTitle) . '/', $oVendor->getLink(1));
    }

    public function testGetLinkWithLangParam()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oVendor = new oxvendor();
        $oVendor->setId('xxx');

        $this->assertEquals(oxRegistry::getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx&amp;lang=1', $oVendor->getLink(1));
    }

    public function testLoadRootVendor()
    {
        $oV = new oxVendor();
        $oV->load('root');
        $this->assertTrue($oV instanceof oxVendor);
        $this->assertEquals('root', $oV->getId());

        $oV = new oxVendor();
        $oV->loadInLang(0, 'root');
        $this->assertEquals(0, $oV->getLanguage());

        $oV = new oxVendor();
        $oV->loadInLang(1, 'root');
        $this->assertEquals(1, $oV->getLanguage());

        $oV = new oxVendor();
        $oV->load('root');
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oV->getLanguage());
    }

    public function testGetNrOfArticles()
    {
        $sVendorId = '68342e2955d7401e6.18967838';

        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->setNonPublicVar("_blShowArticleCnt", true);
        $oVendor->load($sVendorId);

        $this->assertEquals(oxRegistry::get("oxUtilsCount")->getVendorArticleCount($sVendorId), $oVendor->getNrOfArticles());
    }

    public function testGetNrOfArticlesDonotShow()
    {
        $sVendorId = '68342e2955d7401e6.18967838';

        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->load($sVendorId);
        $oVendor->setNonPublicVar("_blShowArticleCnt", false);

        $this->assertEquals(-1, $oVendor->getNrOfArticles());
    }

    public function testSetGetIsVisible()
    {
        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->setIsVisible(true);

        $this->assertTrue($oVendor->getIsVisible());
    }

    public function testSetGetHasVisibleSubCats()
    {
        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->setHasVisibleSubCats(true);

        $this->assertTrue($oVendor->getHasVisibleSubCats());
    }

    public function testGetHasVisibleSubCatsNotSet()
    {
        $oVendor = $this->getProxyClass("oxvendor");

        $this->assertFalse($oVendor->getHasVisibleSubCats());
    }

    /**
     * Testing icon url getter with new path solution
     *
     * @return null
     */
    public function testGetIconUrlNewPath()
    {
        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->oxvendor__oxicon = new oxField('big_matsol_1_mico.png');

        $sUrl = oxRegistry::getConfig()->getOutUrl() . basename(oxRegistry::getConfig()->getPicturePath(""));
        $sUrl .= "/generated/vendor/icon/100_100_75/big_matsol_1_mico.png";

        $this->assertEquals($sUrl, $oVendor->getIconUrl());
    }

    public function testDelete()
    {
        oxTestModules::addFunction('oxSeoEncoderVendor', 'onDeleteVendor', '{$this->onDelete[] = $aA[0];}');
        oxRegistry::get("oxSeoEncoderVendor")->onDelete = array();

        $obj = new oxvendor();
        $this->assertEquals(false, $obj->delete());
        $this->assertEquals(0, count(oxRegistry::get("oxSeoEncoderVendor")->onDelete));
        $this->assertEquals(false, $obj->exists());

        $obj->save();
        $this->assertEquals(true, $obj->delete());
        $this->assertEquals(false, $obj->exists());
        $this->assertEquals(1, count(oxRegistry::get("oxSeoEncoderVendor")->onDelete));
        $this->assertSame($obj, oxRegistry::get("oxSeoEncoderVendor")->onDelete[0]);
    }

    public function testGetStdLinkWithParams()
    {
        $oVendor = new oxvendor();
        $oVendor->setId('xxx');
        $this->assertEquals(oxRegistry::getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx&amp;foo=bar&amp;lang=1', $oVendor->getStdLink(1, array('foo' => 'bar')));
    }

    public function testGetThumbUrl()
    {
        $oVendor = new oxvendor();
        $oVendor->setId('xxx');

        $this->assertFalse($oVendor->getThumbUrl());
    }

    /**
     * Title getter test
     *
     * @return null
     */
    public function testGetTitle()
    {
        $sTitle = "testtitle";
        $oVendor = new oxvendor();
        $oVendor->oxvendor__oxtitle = new oxField("testtitle");
        $this->assertEquals($sTitle, $oVendor->getTitle());
    }
}
