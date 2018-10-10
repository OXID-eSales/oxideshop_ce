<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxField;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\Vendor;
use oxRegistry;
use oxTestModules;

/**
 * Testing oxvendor class
 */
class VendorTest extends \OxidTestCase
{
    protected $_sVndIcon = "/vendor/icon/big_matsol_1_mico.png";

    protected $_sManIcon = "/manufacturer/icon/big_matsol_1_mico.png";

    /**
     * OXID of the test vendor record
     */
    protected $testVendorId = '_vendorTestId';

    /**
     * oxtitle of the test vendor record for language 0
     */
    protected $testVendorTitle_0 = 'test vendor title lang 0';

    /**
     * oxtitle of the test vendor record for language 1
     */
    protected $testVendorTitle_1 = 'test vendor title lang 1';

    /**
     * oxshortdesc of the test vendor record for language 0
     */
    protected $testVendorShortDesc_0 = 'test vendor title lang 0';

    /**
     * oxShortDesc of the test vendor record for language 1
     */
    protected $testVendorShortDesc_1 = 'test vendor title lang 1';

    /**
     * Test setup
     */
    protected function setUp()
    {
        // test require icon for vendors
        if (in_array($this->getName(), ['testGetIconUrlNewPath', 'testGetIconUrl'])) {
            $oConfig = $this->getConfig();
            $sTarget = $oConfig->getPicturePath("") . "master";
            if (file_exists($sTarget . $this->_sManIcon)) {
                copy($sTarget . $this->_sManIcon, $sTarget . $this->_sVndIcon);
            }
        }

        $this->insertTestVendor();

        parent::setUp();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        // removing folder
        if (in_array($this->getName(), ['testGetIconUrlNewPath', 'testGetIconUrl'])) {
            $sTarget = $this->getConfig()->getPicturePath("") . "master";
            if (file_exists($sTarget . $this->_sVndIcon)) {
                unlink($sTarget . $this->_sVndIcon);
            }
        }

        oxTestModules::addFunction('oxVendor', 'cleanRootVendor', '{oxVendor::$_aRootVendor = array();}');
        oxNew('oxvendor')->cleanRootVendor();

        $this->addTableForCleanup('oxvendor');

        parent::tearDown();
    }

    public function testGetBaseSeoLinkForPage()
    {
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorUrl", "{return 'sVendorUrl';}");
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorPageUrl", "{return 'sVendorPageUrl';}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $this->assertEquals("sVendorPageUrl", $vendor->getBaseSeoLink(0, 1));
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorUrl", "{return 'sVendorUrl';}");
        oxTestModules::addFunction("oxSeoEncoderVendor", "getVendorPageUrl", "{return 'sVendorPageUrl';}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $this->assertEquals("sVendorUrl", $vendor->getBaseSeoLink(0));
    }

    public function testGetBaseStdLink()
    {
        $iLang = 0;

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId("testVendorId");

        $sTestUrl = $this->getConfig()->getConfig()->getShopHomeUrl($iLang, false) . "cl=vendorlist&amp;cnid=v_" . $vendor->getId();
        $this->assertEquals($sTestUrl, $vendor->getBaseStdLink($iLang));
    }

    public function testGetContentCats()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $this->assertNull($vendor->getContentCats());
    }

    // #M366: Upload of manufacturer and categories icon does not work
    public function testGetIconUrl()
    {
        $vendor = $this->getProxyClass("oxvendor");
        $vendor->oxvendor__oxicon = new oxField('big_matsol_1_mico.png');

        $this->assertEquals('big_matsol_1_mico.png', basename($vendor->getIconUrl()));
    }

    public function testAssignWithoutArticleCnt()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setShowArticleCnt(false);
        $vendor->load($this->testVendorId);

        $expectedArticleCount = -1;
        $actualArticleCount = $vendor->getNrOfArticles();

        $this->assertEquals($expectedArticleCount, $vendor->oxvendor__oxnrofarticles->value);
        $this->assertEquals($expectedArticleCount, $actualArticleCount);
    }

    public function testAssignWithArticleCnt()
    {

        /**
         * Insert an article for this vendor
         *
         * @var \OxidEsales\Eshop\Application\Controller\Admin\ArticleController $article
         */
        $article = oxNew('oxArticle');
        $article->setId('_vendorTestArticleId');
        $article->oxarticles__oxvendorid = new oxField($this->testVendorId, oxField::T_RAW);
        $article->save();

        /** @var Vendor|\PHPUnit\Framework\MockObject\MockObject $vendor */
        $vendor = $this->getMock(\OxidEsales\Eshop\Application\Model\Vendor::class, array('isAdmin'));
        $vendor->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $vendor->setShowArticleCnt(true);
        $vendor->load($this->testVendorId);

        $expectedArticleCount = 1;
        $actualArticleCount = $vendor->getNrOfArticles();

        /** Delete the article before the assertion */
        $article->delete();

        $this->assertEquals($expectedArticleCount, $vendor->oxvendor__oxnrofarticles->value);
        $this->assertEquals($expectedArticleCount, $actualArticleCount);
    }

    public function testGetStdLink()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx', $vendor->getStdLink());
    }

    public function testGetLinkSeoDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        /** Load vendor in lang 0*/
        $vendor->setLanguage(0);
        $vendor->load($this->testVendorId);

        /** Expect title also in lang 0, as getLink() is called without parameters*/
        $expectedUrl = $this->getConfig()->getShopUrl() . 'Nach-Lieferant/' . str_replace(' ', '-', $this->testVendorTitle_0) . '/';
        $actualUrl = $vendor->getLink();

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    public function testGetLinkSeoEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        /** Load vendor in lang 1*/
        $vendor->loadInLang(1, $this->testVendorId);

        /** Expect title also in lang 1, as getLink() is called without parameters*/
        $part = 'en/By-distributor/';
        $expectedUrl = $this->getConfig()->getShopUrl() . $part . str_replace(' ', '-', $this->testVendorTitle_1) . '/';
        $actualUrl = $vendor->getLink();

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    public function testGetLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx', $vendor->getLink());
    }

    public function testGetStdLinkWithLangParam()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx&amp;lang=1', $vendor->getStdLink(1));
    }

    public function testGetLinkSeoDeWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        /** Load record in lang 1 */
        $vendor->setLanguage(1);
        $vendor->load($this->testVendorId);

        /** Expect title not in lang 1, but in lang 0, as getLink() is called with parameter 0*/
        $expectedUrl = $this->getConfig()->getShopUrl() . 'Nach-Lieferant/' . str_replace(' ', '-', $this->testVendorTitle_0) . '/';
        $actualUrl = $vendor->getLink(0);

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    public function testGetLinkSeoEngWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        /** Load record in lang 0 */
        $vendor->setLanguage(0);
        $vendor->load($this->testVendorId);

        /** Expect title not in lang 0, but in lang 1, as getLink() is called with parameter 1*/
        $part = 'en/By-distributor/';
        $expectedUrl = $this->getConfig()->getShopUrl() . $part . str_replace(' ', '-', $this->testVendorTitle_1) . '/';
        $actualUrl = $vendor->getLink(1);

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    public function testGetLinkWithLangParam()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx&amp;lang=1', $vendor->getLink(1));
    }

    public function testLoadRootVendor()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxVendor');
        $vendor->load('root');
        $this->assertTrue($vendor instanceof Vendor);
        $this->assertEquals('root', $vendor->getId());

        $vendor = oxNew('oxVendor');
        $vendor->loadInLang(0, 'root');
        $this->assertEquals(0, $vendor->getLanguage());

        $vendor = oxNew('oxVendor');
        $vendor->loadInLang(1, 'root');
        $this->assertEquals(1, $vendor->getLanguage());

        $vendor = oxNew('oxVendor');
        $vendor->load('root');
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $vendor->getLanguage());
    }

    public function testGetNrOfArticles()
    {
        /**
         * Insert an article for this vendor
         *
         * @var \OxidEsales\Eshop\Application\Controller\Admin\ArticleController $article
         */
        $article = oxNew('oxArticle');
        $article->setId('_vendorTestArticleId');
        $article->oxarticles__oxvendorid = new oxField($this->testVendorId, oxField::T_RAW);
        $article->save();

        $vendor = $this->getProxyClass("oxvendor");
        /** To have the desired effect it is important to set _blShowArticleCnt before calling load() */
        $vendor->setNonPublicVar("_blShowArticleCnt", true);
        $vendor->load($this->testVendorId);

        $actualArticleCount = $vendor->getNrOfArticles();
        $expectedArticleCount = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getVendorArticleCount($this->testVendorId);

        $article->delete();

        $this->assertEquals($expectedArticleCount, $actualArticleCount);
    }

    public function testGetNrOfArticlesDonotShow()
    {
        $vendor = $this->getProxyClass("oxvendor");
        /** To have the desired effect it is important to set _blShowArticleCnt before calling load() */
        $vendor->setNonPublicVar("_blShowArticleCnt", false);
        $vendor->load($this->testVendorId);

        $actualArticleCount = $vendor->getNrOfArticles();
        $expectedArticleCount = -1;

        $this->assertEquals($expectedArticleCount, $actualArticleCount);
    }

    public function testSetGetIsVisible()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setIsVisible(true);

        $this->assertTrue($vendor->getIsVisible());
    }

    public function testSetGetHasVisibleSubCats()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setHasVisibleSubCats(true);

        $this->assertTrue($vendor->getHasVisibleSubCats());
    }

    public function testGetHasVisibleSubCatsNotSet()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');

        $this->assertFalse($vendor->getHasVisibleSubCats());
    }

    /**
     * Testing icon url getter with new path solution
     *
     * @covers \OxidEsales\EshopCommunity\Application\Model\Vendor::getIconUrl
     */
    public function testGetIconUrlNewPath()
    {
        $vendor = oxNew(Vendor::class);
        $vendor->getConfig()->setConfigParam('sManufacturerIconsize', '100*100');
        $vendor->oxvendor__oxicon = new oxField('big_matsol_1_mico.png');

        $sUrl = $vendor->getConfig()->getOutUrl() . basename($vendor->getConfig()->getPicturePath(''));
        $sUrl .= '/generated/vendor/icon/100_100_75/big_matsol_1_mico.png';

        $this->assertEquals($sUrl, $vendor->getIconUrl());
    }

    public function testDelete()
    {
        oxTestModules::addFunction('oxSeoEncoderVendor', 'onDeleteVendor', '{$this->onDelete[] = $aA[0];}');
        oxRegistry::get("oxSeoEncoderVendor")->onDelete = array();

        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $this->assertEquals(false, $vendor->delete());
        $this->assertEquals(0, count(oxRegistry::get("oxSeoEncoderVendor")->onDelete));
        $this->assertEquals(false, $vendor->exists());

        $vendor->save();
        $this->assertEquals(true, $vendor->delete());
        $this->assertEquals(false, $vendor->exists());
        $this->assertEquals(1, count(oxRegistry::get("oxSeoEncoderVendor")->onDelete));
        $this->assertSame($vendor, oxRegistry::get("oxSeoEncoderVendor")->onDelete[0]);
    }

    public function testGetStdLinkWithParams()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=vendorlist&amp;cnid=v_xxx&amp;foo=bar&amp;lang=1', $vendor->getStdLink(1, array('foo' => 'bar')));
    }

    public function testGetThumbUrl()
    {
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->setId('xxx');

        $this->assertFalse($vendor->getThumbUrl());
    }

    /**
     * Title getter test
     */
    public function testGetTitle()
    {
        $sTitle = "testtitle";
        /** @var Vendor $vendor */
        $vendor = oxNew('oxvendor');
        $vendor->oxvendor__oxtitle = new oxField("testtitle", oxField::T_RAW);
        $this->assertEquals($sTitle, $vendor->getTitle());
    }

    protected function insertTestVendor()
    {
        $shopId = $this->getConfig()->getShopId();

        /** @var Vendor $vendor */
        $vendor = oxNew('oxVendor');
        $vendor->setLanguage(0);
        $vendor->setId($this->testVendorId);

        $vendor->oxvendor__oxshopid = $shopId;
        $vendor->oxvendor__oxtitle = new oxField($this->testVendorTitle_0, oxField::T_RAW);
        $vendor->save();

        $product = oxNew(Article::class);
        $product->setId('_test');
        $product->oxarticles__oxparentid = new oxField('', oxField::T_RAW);
        $product->oxarticles__oxvendorid = new oxField($this->testVendorId, oxField::T_RAW);
        $product->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $product->oxarticles__oxhidden = new oxField(0, oxField::T_RAW);
        $product->oxarticles__oxstock = new oxField(1, oxField::T_RAW);
        $product->save();

        $vendor->setLanguage(1);
        $vendor->load($this->testVendorId);
        $vendor->oxvendor__oxtitle = new oxField($this->testVendorTitle_1, oxField::T_RAW);
        $vendor->save();
    }
}
