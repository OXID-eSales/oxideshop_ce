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

use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Eshop\Core\Database;
use \oxList;
use \oxSimpleVariant;
use \oxArticle;
use oxArticleHelper;
use \oxField;
use \Exception;
use \StdClass;
use \oxbasket;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

if (!defined('OX_IS_ADMIN')) {
    define('OX_IS_ADMIN', false);
}

require_once TEST_LIBRARY_HELPERS_PATH . 'oxArticleHelper.php';

/**
 * Testing oxArticle class.
 */
class ArticleTest extends \OxidTestCase
{
    /**
     * A object of a test article 1
     *
     * @var object
     */
    public $oArticle = null;

    /**
     * A object of a test article 2
     *
     * @var object
     */
    public $oArticle2 = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cleanUpTable('oxobject2category');

        $this->getConfig()->setConfigParam('blUseRightsRoles', 3);
        $this->getConfig()->setConfigParam('blUseTimeCheck', true);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->getConfig()->setGlobalParameter('listtype', null);

        oxRemClassModule('oxCacheHelper');
        $this->cleanUpTable('oxobject2attribute');

        // ensure modules detached
        oxTestModules::cleanAllModules();

        $oDB = oxDb::getDB();
        $oDB->execute('delete from oxaccessoire2article where oxarticlenid="_testArt" ');
        $oDB->execute("update oxattribute set oxdisplayinbasket = 0 where oxid = '8a142c3f0b9527634.96987022' ");

        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxartextends');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxprice2article', 'oxartid');
        $this->cleanUpTable('oxobject2category');
        $this->cleanUpTable('oxobject2category', 'oxobjectid');
        $this->cleanUpTable('oxobject2category', 'oxcatnid');
        $this->cleanUpTable('oxreviews');
        $this->cleanUpTable('oxdiscount');

        $oDB->Execute('delete from oxselectlist where oxid = "_testoxsellist" ');
        $oDB->Execute('delete from oxobject2selectlist where oxselnid = "_testoxsellist" ');

        $this->setProtectedClassProperty(Database::getInstance(), 'tblDescCache', []);

        parent::tearDown();
    }

    /**
     * @param string       $sId
     * @param string|false $sVariantId
     *
     * @return oxArticle
     */
    private function _createArticle($sId = '_testArt', $sVariantId = false)
    {
        $oArticle = $this->getProxyClass('oxArticle');
        $oArticle->setAdminMode(null);
        $oArticle->setId($sId);
        $oArticle->oxarticles__oxprice = new oxField(15.5, oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("test", oxField::T_RAW);
        $oArticle->save();

        if ($sVariantId) {
            $this->_createVariant($sVariantId, $sId);
        }

        return $oArticle;
    }

    /**
     * @param string $sId
     * @param string $sParentId
     *
     * @return oxArticle
     */
    private function _createVariant($sId = '_testVar', $sParentId = '_testArt')
    {
        $oVariant = $this->getProxyClass('oxarticle');
        $oVariant->setEnableMultilang(false);
        $oVariant->setAdminMode(null);
        $oVariant->setId($sId);
        $oVariant->oxarticles__oxprice = new oxField(12.2, oxField::T_RAW);
        $oVariant->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oVariant->oxarticles__oxparentid = new oxField($sParentId, oxField::T_RAW);
        $oVariant->oxarticles__oxtitle = new oxField("test", oxField::T_RAW);
        $oVariant->oxarticles__oxtitle_1 = new oxField("testEng", oxField::T_RAW);

        $oVariant->save();

        return $oVariant;
    }

    /**
     * Test case for #0003393: getSqlActiveSnippet(true) does not force core table usage
     *
     * @return null
     */
    public function testGetViewName()
    {
        $oProduct = oxNew('oxArticle');
        $this->assertEquals("oxarticles", $oProduct->getViewName(true));
        $this->assertNotEquals("oxarticles", $oProduct->getViewName());

        $oCategory = oxNew('oxCategory');
        $this->assertEquals("oxcategories", $oCategory->getViewName(true));
        $this->assertNotEquals("oxcategories", $oCategory->getViewName());

        $oAddress = oxNew('oxAddress');
        $this->assertEquals("oxaddress", $oAddress->getViewName(true));
        $this->assertEquals("oxaddress", $oAddress->getViewName());
    }


    /**
     * Test case for bugtrack report #1887
     *
     * @return null
     */
    public function testForBugReport1887()
    {
        $oParent = oxNew('oxArticle');
        $oParent->setId("_testParentId");
        $oParent->oxarticles__oxstock = new oxField(0);
        $oParent->oxarticles__oxstockflag = new oxField(3);
        $oParent->oxarticles__oxactive = new oxField(1);
        $oParent->save();

        $oVar1 = oxNew('oxArticle');
        $oVar1->setId("_testVar1");
        $oVar1->oxarticles__oxparentid = new oxField("_testParentId");
        $oVar1->oxarticles__oxstock = new oxField(10);
        $oVar1->oxarticles__oxstockflag = new oxField(3);
        $oVar1->oxarticles__oxactive = new oxField(1);
        $oVar1->save();

        $oVar2 = oxNew('oxArticle');
        $oVar2->setId("_testVar2");
        $oVar2->oxarticles__oxparentid = new oxField("_testParentId");
        $oVar2->oxarticles__oxstock = new oxField(10);
        $oVar2->oxarticles__oxstockflag = new oxField(3);
        $oVar2->oxarticles__oxactive = new oxField(1);
        $oVar2->save();

        $oProduct = oxNew('oxArticle');
        $this->assertTrue($oProduct->load("_testParentId"));
        $this->assertFalse($oProduct->isNotBuyable());
    }

    /**
     * Test case for bugtrack report #1782
     *
     * @return null
     */
    public function testForBugReport1782()
    {
        $sIconUrl = $this->getConfig()->getConfigParam("sShopURL") . "out/pictures/generated/product/1/87_87_75/nopic.jpg";
        $this->assertEquals($sIconUrl, $this->_createArticle('_testArt')->getIconUrl());
    }

    /**
     * Test get price with price modifier based on amount.
     * Tests unit price with no price modifier and with 2 different modifiers
     *
     * @return null
     */
    public function testGetPriceWithGivenAmount()
    {
        $oPrice2Prod = oxNew('oxBase');
        $oPrice2Prod->init('oxprice2article');
        $oPrice2Prod->setId('_testPrice2article');
        $oPrice2Prod->oxprice2article__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oPrice2Prod->oxprice2article__oxartid = new oxField("1126");
        $oPrice2Prod->oxprice2article__oxaddabs = new oxField(17);
        $oPrice2Prod->oxprice2article__oxamount = new oxField(2);
        $oPrice2Prod->oxprice2article__oxamountto = new oxField(5);
        $oPrice2Prod->save();

        $oPrice2Prod = oxNew('oxBase');
        $oPrice2Prod->init('oxprice2article');
        $oPrice2Prod->setId('_testPrice2article2');
        $oPrice2Prod->oxprice2article__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oPrice2Prod->oxprice2article__oxartid = new oxField("1126");
        $oPrice2Prod->oxprice2article__oxaddabs = new oxField(15);
        $oPrice2Prod->oxprice2article__oxamount = new oxField(6);
        $oPrice2Prod->oxprice2article__oxamountto = new oxField(10);
        $oPrice2Prod->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        $this->assertEquals(17, $oProduct->getPrice(5)->getBruttoPrice());
        $this->assertEquals(15, $oProduct->getPrice(8)->getBruttoPrice());
        $this->assertEquals(34, $oProduct->getPrice(1)->getBruttoPrice());
    }

    /**
     * Test set base seo and main links.
     *
     * @return null
     */
    public function testSetBaseSeoLinkMainLink()
    {
        oxTestModules::addFunction("oxSeoEncoderArticle", "getArticleUrl", "{return 'sArticleUrl';}");
        oxTestModules::addFunction("oxSeoEncoderArticle", "getArticleMainUrl", "{return 'sArticleMainUrl';}");

        $oProduct = oxNew('oxArticle');
        $this->assertEquals("sArticleMainUrl", $oProduct->getBaseSeoLink(0, true));
    }

    /**
     * Test set base seo link.
     *
     * @return null
     */
    public function testSetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderArticle", "getArticleUrl", "{return 'sArticleUrl';}");
        oxTestModules::addFunction("oxSeoEncoderArticle", "getArticleMainUrl", "{return 'sArticleMainUrl';}");

        $oProduct = oxNew('oxArticle');
        $this->assertEquals("sArticleUrl", $oProduct->getBaseSeoLink(0));
    }

    /**
     * Test get base standard link.
     *
     * @return null
     */
    public function testGetBaseStdLink()
    {
        $iLang = 0;

        $oProduct = oxNew('oxArticle');
        $oProduct->setId("testProdId");

        $sTestUrl = $this->getConfig()->getShopHomeUrl($iLang, false) . "cl=details&amp;anid=" . $oProduct->getId();
        $this->assertEquals($sTestUrl, $oProduct->getBaseStdLink($iLang));
    }

    /**
     * Test append standard link.
     *
     * @return null
     */
    public function testAppendStdLink()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("testArticleId");

        $oArticle->appendStdLink("param1=value1&amp;param2=value2");
        $this->assertEquals($this->getConfig()->getShopHomeURL(0, false) . "cl=details&amp;anid=testArticleId&amp;param1=value1&amp;param2=value2", $oArticle->getStdLink());
    }

    /**
     * Test get main link with seo on.
     *
     * @return null
     */
    public function testGetMainLinkSeoOn()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        $sUrl = $this->getConfig()->getShopUrl();

        $sMainLink = $sUrl . "Geschenke/Bar-Equipment/Bar-Set-ABSINTH.html";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sMainLink = $sUrl . "Party/Bar-Equipment/Bar-Set-ABSINTH.html";
        }

        $oArticle = oxNew('oxArticle');
        $oArticle->load("1126");
        $this->assertEquals($sMainLink, $oArticle->getMainLink());
    }

    /**
     * Test get main link with seo off.
     *
     * @return null
     */
    public function testGetMainLinkSeoOff()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $sUrl = $this->getConfig()->getShopUrl();
        $sMainLink = $sUrl . "index.php?cl=details&amp;anid=1126";

        $oArticle = oxNew('oxArticle');
        $oArticle->load("1126");
        $this->assertEquals($sMainLink, $oArticle->getMainLink());
    }

    /**
     * Test get active check query.
     *
     * @return null
     */
    public function testGetActiveCheckQuery()
    {
        $this->getConfig()->setConfigParam('blUseTimeCheck', true);

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue(0));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $sDate = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getRequestTime());

        $oArticle = oxNew('oxArticle');
        $sTable = $oArticle->getViewName();

        $sQ = " (   $sTable.oxactive = 1  and $sTable.oxhidden = 0  or  ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) ) ";
        $this->assertEquals($sQ, $oArticle->getActiveCheckQuery());
    }

    /**
     * Test get stock check query.
     *
     * @return null
     */
    public function testGetStockCheckQuery()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $this->getConfig()->setConfigParam('blUseTimeCheck', true);

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue(0));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $sDate = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getRequestTime());

        $oArticle = oxNew('oxArticle');
        $sTable = $oArticle->getViewName();

        $sTimeCheckQ = " or ( art.oxactivefrom < '$sDate' and art.oxactiveto > '$sDate' )";
        $sQ = " and ( $sTable.oxstockflag != 2 or ( $sTable.oxstock + $sTable.oxvarstock ) > 0  ) ";
        $sQ = " $sQ and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and ( art.oxactive = 1 $sTimeCheckQ ) and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";

        $this->assertEquals(str_replace(array(" ", "\n", "\t", "\r"), "", $sQ), str_replace(array(" ", "\n", "\t", "\r"), "", $oArticle->getStockCheckQuery()));
    }

    /**
     * Test get stock check query.
     *
     * @ticket #4822
     *
     */
    public function testGetSqlActiveSnippetParentProductStockIsNegativeVariantsWithPositiveStockAreBuyable()
    {
        $sArticleId = '_testArticleId';
        $sShopId = $this->getConfig()->getShopId();

        $oArticle = oxNew('oxArticle');

        $sTable = $oArticle->getViewName();

        $oDb = oxDb::getDb();

        $this->getConfig()->setConfigParam("blVariantParentBuyable", false);

        $oArticle = oxNew('oxArticle');
        $oArticle->setId($sArticleId);
        $oArticle->oxarticles__oxshopid = new oxField($sShopId);
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->oxarticles__oxvarstock = new oxField(2);
        $oArticle->save();

        $oVar = oxNew('oxArticle');
        $oVar->setId('_testVariant1');
        $oVar->oxarticles__oxshopid = new oxField($sShopId);
        $oVar->oxarticles__oxactive = new oxField(1);
        $oVar->oxarticles__oxstockflag = new oxField(3);
        $oVar->oxarticles__oxparentid = new oxField($sArticleId);
        $oVar->save();

        $sQ = "SELECT `oxid` FROM {$sTable} WHERE `oxid` = '{$sArticleId}' AND " . $oArticle->getSqlActiveSnippet();

        $oArticle->oxarticles__oxstock = new oxField(0);
        $oArticle->save();
        $oVar->oxarticles__oxstock = new oxField(1);
        $oVar->save();

        $oArticle->load($sArticleId);
        $this->assertEquals(1, $oArticle->oxarticles__oxvarstock->value);
        $this->assertEquals($sArticleId, $oDb->getOne($sQ), "Article must be buyable");


        $oArticle->oxarticles__oxstock = new oxField(0);
        $oArticle->save();
        $oVar->oxarticles__oxstock = new oxField(0);
        $oVar->save();
        $oArticle->load($sArticleId);
        $this->assertEquals(0, $oArticle->oxarticles__oxvarstock->value);
        $this->assertFalse($oDb->getOne($sQ), "Article mustn't be buyable");
    }

    /**
     * Test get variants query with disabled stock usage
     *
     * @return null
     */
    public function testGetVariantsQueryNoStockUsage()
    {
        $this->getConfig()->setConfigParam('blUseStock', false);

        $oArticle = oxNew('oxArticle');
        $sTable = $oArticle->getViewName();

        $sQ = " and $sTable.oxparentid = '" . $oArticle->getId() . "' ";
        $this->assertEquals($sQ, $oArticle->getVariantsQuery(true));
    }

    /**
     * Test get variants query , hide non orderable.
     *
     * @return null
     */
    public function testGetVariantsQueryHideNonorderable()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);

        $oArticle = oxNew('oxArticle');
        $sTable = $oArticle->getViewName();

        $sQ = " and $sTable.oxparentid = '" . $oArticle->getId() . "' ";
        $sQ .= " and ( $sTable.oxstock > 0 or ( $sTable.oxstock <= 0 and $sTable.oxstockflag != 2  and $sTable.oxstockflag != 3  ) ) ";
        $this->assertEquals($sQ, $oArticle->getVariantsQuery(true));
    }

    /**
     * Test get variants query, show non orderable.
     *
     * @return null
     */
    public function testGetVariantsQueryShowNonorderable()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);

        $oArticle = oxNew('oxArticle');
        $sTable = $oArticle->getViewName();

        $sQ = " and $sTable.oxparentid = '" . $oArticle->getId() . "' ";
        $sQ .= " and ( $sTable.oxstock > 0 or ( $sTable.oxstock <= 0 and $sTable.oxstockflag != 2  ) ) ";
        $this->assertEquals($sQ, $oArticle->getVariantsQuery(false));
    }

    /**
     * Test if has any variant.
     *
     * @return null
     */
    public function testHasAnyVariant()
    {
        $this->_createArticle('_testArt', '_testVar');

        $oA = oxNew('oxArticle');
        $oA->load('_testArt');

        $this->assertTrue($oA->UNIThasAnyVariant());

        $oA->load('_testVar');
        $this->assertFalse($oA->UNIThasAnyVariant());
    }

    /**
     * Test get variants.
     *
     * The Parameter $blRemoveNotOrderables is ignored when the variant list is already cached in $_aVariants.
     *
     * @return null
     */
    public function testGetVariantsForUseCase()
    {
        $this->getConfig()->setConfigParam('blUseStock', 1);

        $iShopId = $this->getConfig()->getShopId();

        // parent
        $oParent = oxNew('oxArticle');
        $oParent->setId("_testParentArticleId");
        $oParent->oxarticles__oxshopid = new oxField($iShopId);
        $oParent->oxarticles__oxactive = new oxField(1);
        $oParent->save();

        // non buyable due to low stock
        $oVar1 = oxNew('oxArticle');
        $oVar1->setId("_testVar1");
        $oVar1->oxarticles__oxparentid = new oxField($oParent->getId());
        $oVar1->oxarticles__oxshopid = new oxField($iShopId);
        $oVar1->oxarticles__oxactive = new oxField(1);
        $oVar1->oxarticles__oxstock = new oxField(0);
        $oVar1->oxarticles__oxstockflag = new oxField(3);
        $oVar1->save();

        // buyable
        $oVar2 = oxNew('oxArticle');
        $oVar2->setId("_testVar2");
        $oVar2->oxarticles__oxparentid = new oxField($oParent->getId());
        $oVar2->oxarticles__oxshopid = new oxField($iShopId);
        $oVar2->oxarticles__oxactive = new oxField(1);
        $oVar2->oxarticles__oxstock = new oxField(1);
        $oVar2->save();

        $oArt = oxNew('oxArticle');
        $oArt->load('_testParentArticleId');

        $this->assertEquals(1, count($oArt->getVariants(true)));
        $this->assertEquals(2, count($oArt->getVariants(false)));
    }

    /**
     * Test get sql active snippet if parent will be loaded on special its variants setup.
     *
     * @return null
     */
    public function testGetSqlActiveSnippetIfParentWillBeLoadedOnSpecialItsVariantsSetup()
    {
        $sArticleId = '_testArticleId';
        $sShopId = $this->getConfig()->getShopId();

        $oArticle = oxNew('oxArticle');

        $sTable = $oArticle->getViewName();

        $oDb = oxDb::getdb();

        $this->getConfig()->setConfigParam("blUseTimeCheck", 0);
        $this->getConfig()->setConfigParam("blUseStock", 0);
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 0);

        // just some inactive article
        $oArticle = oxNew('oxArticle');
        $oArticle->setId($sArticleId);
        $oArticle->oxarticles__oxshopid = new oxField($sShopId);
        $oArticle->oxarticles__oxactive = new oxField(0);
        $oArticle->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertFalse($oDb->getOne($sQ));

        // regular active product
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertEquals("1", $oDb->getOne($sQ));

        $this->getConfig()->setConfigParam("blUseTimeCheck", 1);
        $this->getConfig()->setConfigParam("blUseStock", 0);
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 0);

        $iCurrTime = oxRegistry::get("oxUtilsDate")->getTime();

        // regular active product by time range
        $oArticle->oxarticles__oxactive = new oxField(0);
        $oArticle->oxarticles__oxactivefrom = new oxField(date('Y-m-d H:i:s', $iCurrTime - 3600));
        $oArticle->oxarticles__oxactiveto = new oxField(date('Y-m-d H:i:s', $iCurrTime + 3600));
        $oArticle->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertEquals("1", $oDb->getOne($sQ));

        // stock check is on
        $this->getConfig()->setConfigParam("blUseTimeCheck", 1);
        $this->getConfig()->setConfigParam("blUseStock", 1);
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 0);

        // stock = 0, stock flag = 2
        $oArticle->oxarticles__oxstock = new oxField(0);
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertFalse($oDb->getOne($sQ));

        // stock > 0, stock flag = 2
        $oArticle->oxarticles__oxstock = new oxField(1);
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertEquals("1", $oDb->getOne($sQ));

        // has 2 active variants, but parent itself is not buyable
        $oVar1 = oxNew('oxArticle');
        $oVar1->setId('_testVariant1');
        $oVar1->oxarticles__oxshopid = new oxField($sShopId);
        $oVar1->oxarticles__oxactive = new oxField(1);
        $oVar1->oxarticles__oxstock = new oxField(1);
        $oVar1->oxarticles__oxparentid = new oxField($oArticle->getId());
        $oVar1->save();

        $oVar2 = oxNew('oxArticle');
        $oVar2->setId('_testVariant2');
        $oVar2->oxarticles__oxshopid = new oxField($sShopId);
        $oVar2->oxarticles__oxactive = new oxField(1);
        $oVar2->oxarticles__oxstock = new oxField(1);
        $oVar2->oxarticles__oxparentid = new oxField($oArticle->getId());
        $oVar2->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertEquals("1", $oDb->getOne($sQ));

        // has no active variants (2 inactive)
        $oVar1->oxarticles__oxactive = new oxField(0);
        $oVar1->save();

        $oVar2->oxarticles__oxactive = new oxField(0);
        $oVar2->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertFalse($oDb->getOne($sQ));

        // has 2 active variants and parent itself is buyable
        $this->getConfig()->setConfigParam("blUseTimeCheck", 1);
        $this->getConfig()->setConfigParam("blUseStock", 1);
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 1);

        $oVar1->oxarticles__oxactive = new oxField(1);
        $oVar1->save();

        $oVar2->oxarticles__oxactive = new oxField(1);
        $oVar2->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertEquals("1", $oDb->getOne($sQ));

        // has no active variants and parent itself is buyable
        $oVar1->oxarticles__oxactive = new oxField(0);
        $oVar1->save();

        $oVar2->oxarticles__oxactive = new oxField(0);
        $oVar2->save();

        $sQ = "select 1 from ($sTable) where oxid='{$sArticleId}' and " . $oArticle->getSqlActiveSnippet();
        $this->assertEquals("1", $oDb->getOne($sQ));
    }

    /**
     * Test if Is variant.
     *
     * @return null
     */
    public function testIsVariant()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertFalse($oArticle->isVariant());

        $oArticle->oxarticles__oxparentid = new oxField(null);
        $this->assertFalse($oArticle->isVariant());

        $oArticle->oxarticles__oxparentid = new oxField('xxx');
        $this->assertTrue($oArticle->isVariant());
    }

    /**
     * Test get fprice for test case.
     *
     * 1. parent article is buyable
     * 2. "from" should not be included in fprice getter value
     *
     * @return null
     */
    public function testGetFPriceForTestCase()
    {
        $this->getConfig()->setConfigParam("blVariantParentBuyable", true);

        if ($this->getConfig()->getEdition() === 'EE') {
            $sArtId = '1661';
            $sFPrice = '13,90';
        } else {
            $sArtId = '2077';
            $sFPrice = '19,00';
        }

        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArtId);

        $this->assertEquals($sFPrice, $oArticle->getFPrice());
        $this->assertEquals(-1, $oArticle->getStockStatus());
    }

    /**
     * Test get netto fprice for test case.
     *
     * @return null
     */
    public function testGetFNetPriceForTestCase()
    {
        $this->getConfig()->setConfigParam("blVariantParentBuyable", true);

        if ($this->getConfig()->getEdition() === 'EE') {
            $sArtId = '1661';
            $sFNPrice = '11,68';
        } else {
            $sArtId = '2077';
            $sFNPrice = '15,97';
        }

        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArtId);

        $this->assertEquals($sFNPrice, $oArticle->getFNetPrice());
    }

    /**
     * Test if is order article.
     *
     * @return null
     */
    public function testIsOrderArticle()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertFalse($oArticle->isOrderArticle());
    }

    /**
     * Test get product parent id.
     *
     * @return null
     */
    public function testGetParentId()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxparentid = new oxField('sTestParentId');
        $this->assertEquals('sTestParentId', $oArticle->getParentId());
    }

    /**
     * Test get product id.
     *
     * @return null
     */
    public function testGetProductId()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("someArticleId");
        $this->assertEquals("someArticleId", $oArticle->getProductId());
    }

    /**
     * Test set buyable state.
     *
     * @return null
     */
    public function testSetBuyableState()
    {
        $oArticle = $this->getProxyClass('oxarticle');
        $oArticle->setBuyableState(false);
        $this->assertTrue($oArticle->getNonPublicVar('_blNotBuyable'));

        $oArticle->setBuyableState(true);
        $this->assertFalse($oArticle->getNonPublicVar('_blNotBuyable'));
    }

    /**
     * Test get article long desc smarty processing.
     *
     * Use case:
     * Shop is productive
     *
     * Changes in Article-Longdescription aren't shown in Frontend due to caching
     * if the Option "Process Description of Articles and Categories with Smarty" is enabled.
     *
     * @return null
     */
    public function testGetLongDescriptionSmartyProcessing()
    {
        $this->getConfig()->setConfigParam('blExport', 1);
        $this->getConfig()->setConfigParam('blProductive', 1);
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', 1);

        $myConfig = $this->getConfig();
        $sLink = $myConfig->getImageUrl($myConfig->isAdmin());
        $sRes1 = "test {$sLink} test";
        $sRes2 = "best {$sLink} best";

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArt');
        $oArticle->setArticleLongDesc('test [{ $oViewConf->getImageUrl() }] test');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $this->assertEquals(trim($sRes1), trim($oArticle->getLongDesc()));
        $oArticle->setArticleLongDesc('best [{ $oViewConf->getImageUrl() }] best');
        $oArticle->save();

        oxRegistry::getUtils()->oxResetFileCache();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $this->assertEquals(trim($sRes2), trim($oArticle->getLongDesc()));
    }

    /**
     * Test assign parent field value when field is not set in parent.
     *
     * @return null
     */
    public function testAssignParentFieldValueWhenFieldIsNotSetInParent()
    {
        $oParent = oxNew('oxArticle');

        $oVariant = $this->getMock('oxarticle', array('getParentArticle', '_isFieldEmpty'));
        $oVariant->expects($this->once())->method('getParentArticle')->will($this->returnValue($oParent));
        $oVariant->expects($this->never())->method('_isFieldEmpty');
        $this->assertNull($oVariant->UNITassignParentFieldValue('xxx'));
    }

    /**
     * Test price after global discount applied.
     *
     * Test data:
     * Qty  : 0 - 999999
     * Price: 0 - 50
     *
     * Discount: 50%
     *
     * @return null
     */
    public function testPriceAfterGlobalDiscountApplied()
    {
        oxRegistry::get("oxDiscountList")->forceReload();

        // creating discount for test
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId('_testdiscount');
        $oDiscount->oxdiscount__oxactive = new oxField(1);
        $oDiscount->oxdiscount__oxtitle = new oxField('Test discount');
        $oDiscount->oxdiscount__oxamount = new oxField(0);
        $oDiscount->oxdiscount__oxamountto = new oxField(99999);
        $oDiscount->oxdiscount__oxprice = new oxField(0);
        $oDiscount->oxdiscount__oxpriceto = new oxField(10);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%');
        $oDiscount->oxdiscount__oxaddsum = new oxField(50);
        $oDiscount->save();

        $oArticle1 = oxNew('oxArticle');
        $oArticle1->load('1126');

        $this->assertEquals(34, $oArticle1->getPrice()->getBruttoPrice());

        $oArticle2 = oxNew('oxArticle');
        $oArticle2->load('1127');

        $this->assertTrue($oDiscount->isForArticle($oArticle2));

        $this->assertEquals(4, $oArticle2->getPrice()->getBruttoPrice());
    }

    /**
     * Test get picture gallery when no pictures are set.
     *
     * Bug: when article is created having no real pictures,
     * first picture path is not set
     *
     * @return null
     */
    public function testGetPictureGalleryWhenNoPicturesAreSet()
    {
        $oArticle = oxNew('oxArticle');
        $aGallery = $oArticle->getPictureGallery();

        $sUrl = $this->getConfig()->getPictureUrl("") . 'generated/product/1/380_340_75/nopic.jpg';
        $this->assertEquals($sUrl, $aGallery['ActPic']);
    }

    /**
     * Test set link type.
     *
     * @return null
     */
    public function testSetLinkType()
    {
        $oArticle = $this->getProxyClass('oxarticle');
        $oArticle->setNonPublicVar('oxdetaillink', 'http://www.oxid-esales.com/');
        $oArticle->setLinkType(999);

        // testing
        $this->assertEquals(999, $oArticle->getNonPublicVar('_iLinkType'));
        $this->assertNull($oArticle->getNonPublicVar('_sDetailLink'));
    }

    /**
     * Test Get Media if media object is loaded in same language as article
     *
     * @return null
     */
    public function testGetMediaUrlsLanguageTest()
    {
        $this->cleanUpTable('oxmediaurls');
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc) values ('_test1', '1126', '/test.jpg', 'test1')";
        oxDb::getDb()->execute($sQ);

        $oArt = oxNew('oxArticle');
        $oArt->loadInLang(1, '1126');

        $oMediaUrls = $oArt->getMediaUrls();

        $this->assertEquals(1, count($oMediaUrls));
        $this->assertEquals(1, $oMediaUrls->current()->getLanguage());
        $this->cleanUpTable('oxmediaurls');
    }

    /**
     * Testing article url modifier functionality.
     *
     * @return null
     */
    public function testAppendLink()
    {
        $sParams = 'param1=value1&amp;param2=value2';

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');
        $oArticle->appendLink($sParams);
        $this->assertTrue((bool) strpos($oArticle->getLink(), $sParams));
    }

    /**
     * Testing how amount price chooses correct price value.
     *
     * @return null
     */
    public function testGetAmountPriceWhenPassingLowerPrice()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');

        // some data for test
        $oP2A = oxNew('oxBase');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oP2A->oxprice2article__oxartid = new oxField($oArticle->getId());
        $oP2A->oxprice2article__oxaddabs = new oxField(33);
        $oP2A->oxprice2article__oxamount = new oxField(2);
        $oP2A->oxprice2article__oxamountto = new oxField(10);
        $oP2A->save();

        $oP2A = oxNew('oxBase');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oP2A->oxprice2article__oxartid = new oxField($oArticle->getId());
        $oP2A->oxprice2article__oxaddabs = new oxField(32);
        $oP2A->oxprice2article__oxamount = new oxField(11);
        $oP2A->oxprice2article__oxamountto = new oxField(9999999);
        $oP2A->save();

        $oArticle->setNonPublicVar("_oAmountPriceList", null);
        $oArticle->oxarticles__oxprice = new oxField(50);
        // testing article
        $this->assertEquals($oArticle->oxarticles__oxprice->value, $oArticle->UNITgetAmountPrice(1));

        $oArticle->setNonPublicVar("_oAmountPriceList", null);
        // testing article
        $this->assertEquals(33, $oArticle->UNITgetAmountPrice(2));

        $oArticle->setNonPublicVar("_oAmountPriceList", null);
        // testing article
        $dPrice = 35;
        $this->assertEquals(32, $oArticle->UNITgetAmountPrice(12));

        $oArticle->setNonPublicVar("_oAmountPriceList", null);
        $oArticle->oxarticles__oxprice->value = 30;
        $this->assertEquals(30, $oArticle->UNITgetAmountPrice(12));

        $oArticle = $this->getMock("oxarticle", array("skipDiscounts"));
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(true));
        $oArticle->load($oArticle->getId());
        $oArticle->oxarticles__oxprice = new oxField(50);
        $this->assertEquals($oArticle->oxarticles__oxprice->value, $oArticle->UNITgetAmountPrice(1));
        $this->assertEquals($oArticle->oxarticles__oxprice->value, $oArticle->UNITgetAmountPrice(2));
        $this->assertEquals($oArticle->oxarticles__oxprice->value, $oArticle->UNITgetAmountPrice(12));
    }

    /**
     * Testing amount price lists.
     *
     * @return null
     */
    public function testFillAmountPriceList()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $dArticlePrice = $oArticle->UNITgetGroupPrice();

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_1');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddabs = new oxField('6');
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_2');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('7');
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oArticle = oxNew('oxArticle');
        $oArticle->getConfig()->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', 0);
        $oArticle->load('1126');

        $oAmPriceList = $oArticle->UNITfillAmountPriceList($oAmPriceList);

        $oP2A = reset($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( 5 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( 5 / ( 1 + 19 / 100 ) ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency(6), $oP2A->fbrutprice);

        $oP2A = next($oAmPriceList);
        $dPrice = oxRegistry::getUtils()->fRound($dArticlePrice - $dArticlePrice / 100 * 7);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dPrice), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dPrice / ( 1 + 19 / 100 ) ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency($dPrice), $oP2A->fbrutprice);
    }

    /**
     * Testing amount price lists calls for apply vat.
     *
     * @return null
     */
    public function testFillAmountPriceListCalls_applyVAT()
    {
        $oArticle = $this->getMock('oxarticle', array('_applyVAT') /*, array(), '', false*/);
        $oArticle->expects($this->exactly(0))->method('_applyVAT');
        $oArticle->load('1126');
        $dArticlePrice = $oArticle->UNITgetGroupPrice();

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_1');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddabs = new oxField('5');
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_2');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('5');
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oArticle = $this->getMock('oxarticle', array('_applyVAT') /*, array(), '', false*/);
        // one for main, two for am prices
        $oArticle->expects($this->exactly(1))->method('_applyVAT');
        $oArticle->getConfig()->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', 0);
        $oArticle->load('1126');

        $oArticle->UNITfillAmountPriceList($oAmPriceList);
    }

    /**
     * Test fill amount price list vat only for basket.
     *
     * @return null
     */
    public function testFillAmountPriceListVatOnlyForBasket()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $dArticlePrice = $oArticle->UNITgetGroupPrice();

        $this->getConfig()->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', 1);
        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_1');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddabs = new oxField('5');
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_2');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('5');
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');

        $oAmPriceList = $oArticle->UNITfillAmountPriceList($oAmPriceList);

        $oP2A = reset($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( 5 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( 5 ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency(5), $oP2A->fbrutprice);

        $oP2A = next($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 5 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 5 ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency($dArticlePrice - $dArticlePrice / 100 * 5), $oP2A->fbrutprice);
    }

    /**
     * Test fill amount price list first discount lower.
     *
     * @return null
     */
    public function testFillAmountPriceListFirstDiscountLower()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $dArticlePrice = $oArticle->UNITgetGroupPrice();

        $this->getConfig()->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', 1);
        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_1');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('10');
        $oP2A->oxprice2article__oxartid = new oxField('1126');
        $oP2A->oxprice2article__oxamount = new oxField('1');
        $oP2A->oxprice2article__oxamountto = new oxField('4');
        $oP2A->save();
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_2');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('5');
        $oP2A->oxprice2article__oxartid = new oxField('1126');
        $oP2A->oxprice2article__oxamount = new oxField('5');
        $oP2A->oxprice2article__oxamountto = new oxField('40');
        $oP2A->save();
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');

        $oAmPriceList = $oArticle->UNITfillAmountPriceList($oAmPriceList);

        $oP2A = reset($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 10 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 10 ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency($dArticlePrice - $dArticlePrice / 100 * 10), $oP2A->fbrutprice);


        $oP2A = next($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 5 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 5 ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency($dArticlePrice - $dArticlePrice / 100 * 5), $oP2A->fbrutprice);
    }

    /**
     * Test fill amount price list second discount lower.
     *
     * @return null
     */
    public function testFillAmountPriceListSecondDiscountLower()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $dArticlePrice = $oArticle->UNITgetGroupPrice();

        $this->getConfig()->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', 1);
        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_1');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('5');
        $oP2A->oxprice2article__oxartid = new oxField('1126');
        $oP2A->oxprice2article__oxamount = new oxField('1');
        $oP2A->oxprice2article__oxamountto = new oxField('4');
        $oP2A->save();
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oP2A = oxNew('oxBase');
        $oP2A->setId('_test_2');
        $oP2A->init('oxprice2article');
        $oP2A->oxprice2article__oxaddperc = new oxField('10');
        $oP2A->oxprice2article__oxartid = new oxField('1126');
        $oP2A->oxprice2article__oxamount = new oxField('5');
        $oP2A->oxprice2article__oxamountto = new oxField('40');
        $oP2A->save();
        $oAmPriceList[$oP2A->getId()] = $oP2A;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');

        $oAmPriceList = $oArticle->UNITfillAmountPriceList($oAmPriceList);

        $oP2A = reset($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 5 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 5 ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency($dArticlePrice - $dArticlePrice / 100 * 5), $oP2A->fbrutprice);

        $oP2A = next($oAmPriceList);
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 10 ), $oP2A->oxprice2article__oxaddabs->value );
        //$this->assertEquals( oxRegistry::getLang()->formatCurrency( $dArticlePrice - $dArticlePrice / 100 * 10 ), $oP2A->fnetprice );
        $this->assertEquals(oxRegistry::getLang()->formatCurrency($dArticlePrice - $dArticlePrice / 100 * 10), $oP2A->fbrutprice);
    }

    /**
     * Test set id.
     *
     * @return null
     */
    public function testSetId()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("test_id");
        $this->assertEquals("test_id", $oArticle->oxarticles__oxid->value);
        $this->assertEquals("test_id", $oArticle->oxarticles__oxnid->value);
    }

    /**
     * Test disable price load.
     *
     * @return null
     */
    public function testDisablePriceLoad()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->disablePriceLoad();
        $this->assertNull($oArticle->getBasePrice());

        return $oArticle;
    }

    /**
     * Test enable price load.
     *
     * @depends testDisablePriceLoad
     *
     * @param oxArticle $oArticle
     *
     * @return null
     */
    public function testEnablePriceLoad(oxArticle $oArticle)
    {
        $oArticle->enablePriceLoad();
        $this->assertNotNull($oArticle->getBasePrice());
    }

    /**
     * Test set/get item key.
     *
     * @return null
     */
    public function testSetGetItemKey()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setItemKey("test_key");
        $this->assertEquals("test_key", $oArticle->getItemKey());
    }

    /**
     * Test set no variant loading.
     *
     * @return null
     */
    public function testSetNoVariantLoading()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setNoVariantLoading(true);
        $this->assertEquals(array(), $oArticle->getVariants());
    }

    /**
     * Test if article is on comparison list.
     *
     * @return null
     */
    public function testIsOnComparisonList()
    {
        $oArticle = $this->_createArticle('_testArt');
        $this->getSession()->setVariable('aFiltcompproducts', array('_testArt' => '_testArt'));

        $oArticle->UNITassignComparisonListFlag();
        $this->assertTrue($oArticle->isOnComparisonList());
    }

    /**
     * Test set on comparison list.
     *
     * @return null
     */
    public function testSetOnComparisonList()
    {
        $oArticle = $this->_createArticle('_testArt');
        $this->getSession()->setVariable('aFiltcompproducts', array('_testArt' => '_testArt'));
        $oArticle->UNITassignComparisonListFlag();
        $this->assertTrue($oArticle->isOnComparisonList());
        $oArticle->setOnComparisonList(false);
        $this->assertFalse($oArticle->isOnComparisonList());
    }

    /**
     * Test assign get persistent parameters.
     *
     * @return null
     */
    public function testAssignGetPersParams()
    {
        $oArticle = $this->_createArticle('_testArt');
        $aParam = array('_testArt' => 'test1', '2001' => 'test2');
        $this->getSession()->setVariable('persparam', $aParam);
        $oArticle->UNITassignPersistentParam();
        $this->assertEquals('test1', $oArticle->getPersParams());
    }

    /**
     * Test get admin variants.
     *
     * @return null
     */
    public function testGetAdminVariants()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oVariants = $oArticle->getAdminVariants();
        $this->assertEquals(1, count($oVariants));
        $oVariant = $oVariants->current();
        $this->assertEquals('_testVar', $oVariant->oxarticles__oxid->value);
        $this->assertEquals('test', $oVariant->oxarticles__oxtitle->value);
    }

    /**
     * Test get admin variants in other language.
     *
     * @return null
     */
    public function testGetAdminVariantsInOtherLang()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oVariants = $oArticle->getAdminVariants(1);
        $this->assertEquals(1, count($oVariants));
        $oVariant = $oVariants->current();
        $this->assertEquals('testEng', $oVariant->oxarticles__oxtitle->value);
    }

    /**
     * Test get admin variants not buyble parent.
     *
     * @return null
     */
    public function testGetAdminVariantsNotBuybleParent()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $oVariants = $oArticle->getAdminVariants();
        $this->assertEquals(1, count($oVariants));
        $this->assertTrue($oArticle->isParentNotBuyable());
    }

    /**
     * Test article load.
     *
     * @return null
     */
    public function testLoad()
    {
        $this->_createArticle('_testArt');

        oxTestModules::addFunction('oxarticle', '_skipSaveFields', '{$this->_aSkipSaveFields=array();}');

        $oArticle = oxnew('oxarticle');
        $oArticle->load('_testArt');

        $oArticle->oxarticles__oxinsert = new oxField('2008/04/04');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');

        $sInsert = '2008-04-04';
        if ($oArticle->getLanguage() == 1) {
            $sInsert = '2008-04-04';
        }

        $this->assertEquals($sInsert, $oArticle->oxarticles__oxinsert->value);
    }

    /**
     * Test skip save fields.
     *
     * @return null
     */
    public function testSkipSaveFields()
    {
        $this->getConfig()->setConfigParam('aMultishopArticleFields', array("OXPRICE", "OXPRICEA", "OXPRICEB", "OXPRICEC", 'OXSHORTDESC'));
        $oArticle = $this->getProxyClass("oxArticle");
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxshopid = new oxField('2', oxField::T_RAW);
        if ($this->getConfig()->getEdition() === 'EE') {
            $aSkipFields = array('oxtimestamp', 'oxinsert', 'oxmapid', 'oxparentid', 'oxprice', 'oxpricea', 'oxpriceb', 'oxpricec', 'oxshortdesc', 'oxshortdesc_1');
        } else {
            $aSkipFields = array('oxtimestamp', 'oxinsert', 'oxparentid');
        }

        $oArticle->UNITskipSaveFields();

        $this->assertEquals($aSkipFields, $oArticle->getNonPublicVar('_aSkipSaveFields'));
    }

    /**
     * Test skip save fields for variant.
     *
     * @return null
     */
    public function testSkipSaveFieldsForVariant()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $aSkipFields = array('oxtimestamp', 'oxinsert', 'oxmapid');
        } else {
            $aSkipFields = array('oxtimestamp', 'oxinsert');
        }
        $oVariant = $this->_createVariant();
        $oVariant->UNITskipSaveFields();
        $this->assertEquals($aSkipFields, $oVariant->getNonPublicVar('_aSkipSaveFields'));
    }

    /**
     * Test oxarticle::ResetParent method.
     *
     * @return null
     */
    public function testResetParent()
    {
        // set enviroment
        $oParent = oxNew('oxArticle');
        $oParent->setId("_testParentId");
        $oParent->oxarticles__oxstock = new oxField(3);
        $oParent->oxarticles__oxstockflag = new oxField(3);
        $oParent->oxarticles__oxprice = new oxField(15);
        $oParent->oxarticles__oxactive = new oxField(1);
        $oParent->oxarticles__oxvarcount = new oxField(2);
        $oParent->save();

        $oVar1 = oxNew('oxArticle');
        $oVar1->setId("_testVar4");
        $oVar1->oxarticles__oxparentid = new oxField("_testParentId");
        $oVar1->oxarticles__oxstock = new oxField(10);
        $oVar1->oxarticles__oxstockflag = new oxField(3);
        $oVar1->oxarticles__oxprice = new oxField(10);
        $oVar1->oxarticles__oxactive = new oxField(1);
        $oVar1->save();

        $oVar2 = oxNew('oxArticle');
        $oVar2->setId("_testVar5");
        $oVar2->oxarticles__oxparentid = new oxField("_testParentId");
        $oVar2->oxarticles__oxstock = new oxField(10);
        $oVar2->oxarticles__oxstockflag = new oxField(3);
        $oVar2->oxarticles__oxprice = new oxField(20);
        $oVar2->oxarticles__oxactive = new oxField(1);
        $oVar2->save();

        // setting parent info for later use
        $iVariantsCount = count($oParent->getVariants());
        $aCategoryIds = $oParent->getCategoryIds();

        // changing first child to parent
        $oVar1->resetParent();

        // check if child is changed correctly
        $this->assertEquals('', $oVar1->oxarticles__oxparentid->value);
        $this->assertNull($oVar1->getParentArticle());
        $this->assertEquals($aCategoryIds, $oVar1->getCategoryIds());
        $this->assertFalse($oVar1->isNotBuyable());

        //check if parent is changed correctly
        $oParent = oxNew('oxArticle');
        $oParent->load("_testParentId");
        $this->assertEquals($iVariantsCount - 1, count($oParent->getVariants()));
        $this->assertEquals(20, $oParent->getVarMinPrice()->getBruttoPrice());
        $this->assertEquals(20, $oParent->UNITgetVarMaxPrice());
        $this->assertEquals(15, $oParent->getMinPrice()->getBruttoPrice());
        $this->assertFalse($oParent->isRangePrice());

        // changing second child to parent
        $oVar2->resetParent();

        //check if parent is changed correctly
        $oParent = oxNew('oxArticle');
        $oParent->load("_testParentId");
        $this->assertFalse($oParent->UNIThasAnyVariant());
        $this->assertEquals(0, count($oParent->getVariants()));
        $this->assertEquals(15, $oParent->getVarMinPrice()->getBruttoPrice());
        $this->assertEquals(15, $oParent->UNITgetVarMaxPrice());
        $this->assertEquals(15, $oParent->getMinPrice()->getBruttoPrice());
        $this->assertFalse($oParent->isRangePrice());

        $oParent->delete();
        $oVar1->delete();
        $oVar2->delete();
    }

    /**
     * Test article insert.
     *
     * FS#1957
     *
     * @return null
     */
    public function testInsert()
    {
        $now = date('Y-m-d H:i:s', time());
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArt2');
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oArticle->UNITinsert();
        $sOxid = oxDb::getDb()->getOne("Select oxid from oxarticles where oxid = '_testArt2'");
        $this->assertEquals('_testArt2', $sOxid);
        $this->assertTrue($oArticle->oxarticles__oxinsert->value >= $now);
        $this->assertTrue($oArticle->oxarticles__oxtimestamp->value >= $now);
        $this->assertEquals('oxarticle', $oArticle->oxarticles__oxsubclass->value);
    }

    /**
     * test Update.
     *
     * @return null
     */
    public function testUpdate()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxtitle = new oxField('test2');
        $blRet = $oArticle->UNITupdate();
        $this->assertTrue($blRet);
        $this->assertEquals('test2', $oArticle->oxarticles__oxtitle->value);
    }


    /**
     * Test assign simple article.
     *
     * @return null
     */
    public function testAssignSimpleArticle()
    {
        $sArtID = '_testArt';
        $oArticle = oxNew("oxArticle");
        $oArticle->load($sArtID);
        $oArticle->setSkipAssign(true);
        $oArticle->oxdetaillink = null;
        $this->assertNULL($oArticle->assign(null));
        $this->assertNULL($oArticle->oxdetaillink);
    }

    /**
     * Test assign.
     *
     * @return null
     */
    public function testAssign()
    {
        $sArtID = '_testArt';
        $oArticle = oxNew("oxArticle");
        $oArticle->load($sArtID);
        $dbRecord = array();
        $dbRecord['oxarticles__oxlongdesc'] = 'LongDesc';
        $dbRecord['oxarticles__oxtitle'] = 'test2';
        $oArticle->assign($dbRecord);
        $this->assertEquals('LongDesc', $oArticle->oxarticles__oxlongdesc->value);
        $this->assertEquals($oArticle->oxarticles__oxid->value, $oArticle->oxarticles__oxnid->value);
        $this->assertEquals('test2', $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * Test get variants ids.
     *
     * @return null
     */
    public function testGetVariantsIds()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oVariant->oxarticles__oxactive = new oxField(0);
        $oVariant->save();
        $aIds = $oArticle->UNITgetVariantsIds();
        $this->assertEquals(0, count($aIds));

        $oVariant->oxarticles__oxactive = new oxField(1);
        $oVariant->save();
        $aIds = $oArticle->UNITgetVariantsIds();
        $this->assertEquals('_testVar', $aIds[0]);
    }

    /**
     * Test add to rating average.
     *
     * @return null
     */
    public function testaddToRatingAverage()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxrating = new oxField(3.5, oxField::T_RAW);
        $oArticle->oxarticles__oxratingcnt = new oxField(2, oxField::T_RAW);
        $oArticle->save();
        $oArticle->addToRatingAverage(5);

        $this->assertEquals(4, $oArticle->oxarticles__oxrating->value);
        $this->assertEquals(3, $oArticle->oxarticles__oxratingcnt->value);
        $dRating = oxDb::getDB()->getOne("select oxrating from oxarticles where oxid='" . $oArticle->getId() . "'");
        $this->assertEquals(4, $dRating);
    }

    /**
     * Test get article rating average.
     *
     * @return null
     */
    public function testGetArticleRatingAverage()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxrating = new oxField(3.52345, oxField::T_RAW);
        $oArticle->oxarticles__oxratingcnt = new oxField(1, oxField::T_RAW);

        $this->assertEquals(3.5, $oArticle->getArticleRatingAverage());
        $this->assertEquals(1, $oArticle->getArticleRatingCount());

        // inserting few test records
        $oRev = oxNew('oxreview');
        $oRev->setId('_testrev1');
        $oRev->oxreviews__oxobjectid = new oxField('_testArt');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(3);
        $oRev->save();

        $oRev = oxNew('oxreview');
        $oRev->setId('_testrev2');
        $oRev->oxreviews__oxobjectid = new oxField('_testArt');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(1);
        $oRev->save();

        $oRev = oxNew('oxreview');
        $oRev->setId('_testrev3');
        $oRev->oxreviews__oxobjectid = new oxField('_testVar');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(5);
        $oRev->save();

        $this->assertEquals(3, $oArticle->getArticleRatingAverage(true));
        $this->assertEquals(3, $oArticle->getArticleRatingCount(true));

    }

    /**
     * Test get reviews.
     *
     * @return null
     */
    public function testGetReviews()
    {
        $sArtID = '_testArt';
        $sExpectedText = 'Review \n Text';

        $oArticle = $this->_createArticle('_testArt');

        oxDb::getDB()->execute("insert into oxreviews (oxid, oxcreate, oxtype, oxobjectid, oxtext) values ('_test1', '2008/04/04', 'oxarticle', '$sArtID', '$sExpectedText' )");

        $aReviews = $oArticle->getReviews();
        $this->assertTrue($aReviews instanceof oxList);
        $oReview = $aReviews->getArray();
        $this->assertEquals(1, $aReviews->count());
        $this->assertEquals("Review <br />\n Text", $oReview['_test1']->oxreviews__oxtext->value);

        $sCreate = '04.04.2008 00:00:00';
        if ($oArticle->getLanguage() == 1) {
            $sCreate = '2008-04-04 00:00:00';
        }

        $this->assertEquals($sCreate, $oReview['_test1']->oxreviews__oxcreate->value);
    }

    /**
     * Test get reviews with variants.
     *
     * @return null
     */
    public function testGetReviewsWithVariants()
    {
        $sExpectedText = 'ReviewText';
        $sExpectedTextVar = 'ReviewTextVar';

        $oArticle = $this->_createArticle('_testArt', '_testVar');

        oxDb::getDB()->execute("insert into oxreviews (oxid, oxtype, oxobjectid, oxtext) values ('_test1', 'oxarticle', '_testArt', '$sExpectedText' )");
        oxDb::getDB()->execute("insert into oxreviews (oxid, oxtype, oxobjectid, oxtext) values ('_test2', 'oxarticle', '_testVar', '$sExpectedTextVar' )");

        $this->getConfig()->setConfigParam('blShowVariantReviews', true);
        $aReviews = $oArticle->getReviews();
        $this->assertTrue($aReviews instanceof oxList);
        $oReview = $aReviews->getArray();
        $this->assertEquals(2, $aReviews->count());
        $this->assertEquals($sExpectedText, $oReview['_test1']->oxreviews__oxtext->value);
        $this->assertEquals($sExpectedTextVar, $oReview['_test2']->oxreviews__oxtext->value);
    }

    /**
     * Test get reviews with guestbook moderation.
     *
     * @return null
     */
    public function testGetReviewsWithGBModeration()
    {
        $sExpectedText = 'ReviewText';
        $oOriginalArticle = $this->_createArticle('_testArt', '_testVar');
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        oxDb::getDB()->execute("insert into oxreviews (oxid, oxtype, oxobjectid, oxuserid, oxtext) values ('_test1', 'oxarticle', '_testArt', 'oxdefaultadmin', '$sExpectedText' )");
        $oArticle = $this->getMock('oxarticle', array('getUser'));
        $oArticle->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oArticle->load('_testArt');
        $this->getConfig()->setConfigParam('blGBModerate', true);
        $this->assertNull($oArticle->getReviews());
        oxDb::getDB()->execute("update oxreviews set oxactive =1 where oxobjectid='_testArt'");
        $aReviews = $oOriginalArticle->getReviews();
        $this->assertTrue($aReviews instanceof oxList);
        $this->assertEquals(1, $aReviews->count());
    }

    /**
     * Test get reviews with guestbook moderation and no user.
     *
     * @return null
     */
    public function testGetReviewsWithGBModerationNoUser()
    {
        $sExpectedText = 'ReviewText';
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        oxDb::getDB()->execute("insert into oxreviews (oxid, oxtype, oxobjectid, oxtext) values ('test1', 'oxarticle', '_testArt', '$sExpectedText' )");
        $this->getConfig()->setConfigParam('blGBModerate', true);
        $this->assertNull($this->_createArticle('_testArt', '_testVar')->getReviews());
    }

    /**
     * Test get accessoires.
     *
     * @return null
     */
    public function testGetAccessoires()
    {
        $oArticle = $this->_createArticle('_testArt');

        $oNewGroup = oxNew("oxBase");
        $oNewGroup->init("oxaccessoire2article");
        $oNewGroup->oxaccessoire2article__oxobjectid = new oxField("1651", oxField::T_RAW);
        $oNewGroup->oxaccessoire2article__oxarticlenid = new oxField('_testArt', oxField::T_RAW);
        $oNewGroup->oxaccessoire2article__oxsort = new oxField(0, oxField::T_RAW);
        $oNewGroup->save();

        $oArticle->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->save();
        $aAccess = $oArticle->getAccessoires();

        $this->assertEquals(count($aAccess), 1);
    }

    /**
     * Test get accessoires not allowed.
     *
     * @return null
     */
    public function testGetAccessoiresNotAllowed()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadAccessoires', false);
        $this->assertNull($this->_createArticle('_testArt')->getAccessoires());
    }

    /**
     * Test get accessoires empty.
     *
     * @return null
     */
    public function testGetAccessoiresEmpty()
    {
        $this->assertNull($this->_createArticle('_testArt')->getAccessoires());
    }

    /**
     * Test get crossselling when loading is not allowed so empty list is returned.
     *
     * @return null
     */
    public function testGetCrossSellingLoadingIsNotAllowedSoEmptyListIsReturned()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadCrossselling', false);
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");
        $this->assertNull($oArticle->getCrossSelling());
    }

    /**
     * Test get crossselling should return empty list because of non existing article.
     *
     * @return null
     */
    public function testGetCrossSellingShouldReturnEmptyListBecauseOfNonExistingArticle()
    {
        $oArticle = oxNew("oxArticle");
        $oArticle->load('_testArt');
        $this->assertNull($oArticle->getCrossSelling());
    }

    /**
     * Test get crossselling.
     *
     * @return null
     */
    public function testGetCrossSelling()
    {
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");
        $oList = $oArticle->getCrossSelling();
        if ($this->getConfig()->getEdition() === 'EE') {
            $iCount = 3;
        } else {
            $iCount = 2;
        }
        $this->assertTrue($oList instanceof oxList);
        $this->assertEquals($iCount, $oList->count());
    }

    /**
     * Test get bidirectionall cross selling.
     *
     * In case of fault this test may fail only randomly
     * for more precise test check oxArticleList::getCrosselingArticles()
     *
     * @return null
     */
    public function testGetBiCrossSelling()
    {
        $this->getConfig()->setConfigParam('blBidirectCross', true);
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");
        $aAccess = $oArticle->getCrossSelling();

        $this->assertEquals(4, count($aAccess));
    }

    /**
     * Test get customer also bought this products.
     *
     * @return null
     */
    public function testGetCustomerAlsoBoughtThisProducts()
    {
        $oArticle = $this->_createArticle('_testArt');

        $sShopId = $this->getConfig()->getShopId();
        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setId('_testId');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArt', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('51', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxordershopid = new oxField($sShopId, oxField::T_RAW);
        $oOrderArticle->save();
        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setId('_testId2');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('51', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxordershopid = new oxField($sShopId, oxField::T_RAW);
        $oOrderArticle->save();
        $aArticles = $oArticle->getCustomerAlsoBoughtThisProducts();

        $this->assertEquals(1, count($aArticles));
        $this->assertEquals('1651', $aArticles['1651']->oxarticles__oxid->value);
    }

    /**
     * Test get customer also bought this products disabled.
     *
     * @return null
     */
    public function testGetCustomerAlsoBoughtThisProductsDisabled()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadCustomerWhoBoughtThis', false);
        $aArticles = $this->_createArticle('_testArt')->getCustomerAlsoBoughtThisProducts();

        $this->assertNull($aArticles);
    }

    /**
     * Test generate search str for customer bought.
     *
     * @return null
     */
    public function testGenerateSearchStrForCustomerBought()
    {
        $this->setTime(0);

        $oArticle = $this->_createArticle('_testArt', '_testVar');

        $sSelect = $oArticle->UNITgenerateSearchStrForCustomerBought();

        $sArtTable = $oArticle->UNITgetObjectViewName('oxarticles');
        $sOrderArtTable = getViewName('oxorderarticles');

        $sExpSelect = "select distinct {$sArtTable}.* from (
                   select d.oxorderid as suborderid from {$sOrderArtTable} as d use index ( oxartid ) where d.oxartid in ( '_testArt', '_testVar' ) limit 50
               ) as suborder
               left join {$sOrderArtTable} force index ( oxorderid ) on suborder.suborderid = {$sOrderArtTable}.oxorderid
               left join {$sArtTable} on {$sArtTable}.oxid = {$sOrderArtTable}.oxartid
               where {$sArtTable}.oxid not in ( '_testArt', '_testVar')
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' ) and " . $oArticle->getSqlActiveSnippet();

        $sExpSelect = str_replace(array("\n", "\r", "\t", " "), "", $sExpSelect);
        $sSelect = str_replace(array("\n", "\r", "\t", " "), "", $sSelect);

        $this->assertEquals($sExpSelect, $sSelect);
    }

    /**
     * Test generate search str for customer bought for variants.
     *
     * @return null
     */
    public function testGenerateSearchStrForCustomerBoughtForVariants()
    {
        $this->setTime(0);

        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $sSelect = $oVariant->UNITgenerateSearchStrForCustomerBought();

        $sArtTable = $oVariant->UNITgetObjectViewName('oxarticles');
        $sOrderArtTable = getViewName('oxorderarticles');

        $sExpSelect = "select distinct {$sArtTable}.* from (
                   select d.oxorderid as suborderid from {$sOrderArtTable} as d use index ( oxartid ) where d.oxartid in ( '_testVar', '_testArt' )  limit 50
               ) as suborder
               left join {$sOrderArtTable} force index ( oxorderid ) on suborder.suborderid = {$sOrderArtTable}.oxorderid
               left join {$sArtTable} on {$sArtTable}.oxid = {$sOrderArtTable}.oxartid
               where {$sArtTable}.oxid not in ( '_testVar', '_testArt' )
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' ) and " . $oVariant->getSqlActiveSnippet();

        $sExpSelect = str_replace(array("\n", "\r", "\t", " "), "", $sExpSelect);
        $sSelect = str_replace(array("\n", "\r", "\t", " "), "", $sSelect);

        $this->assertEquals($sExpSelect, $sSelect);
    }

    /**
     * Test generate search str for customer bought for variants 2.
     *
     * @return null
     */
    public function testGenerateSearchStrForCustomerBoughtForVariants2()
    {
        $this->setTime(0);
        $oArticle = $this->_createArticle('_testArt', '_testVar');

        $oArticle2 = oxNew('oxArticle');
        $oArticle2->modifyCacheKey(null, false);
        $oArticle2->setId('_testArt2');
        $oArticle2->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oArticle2->oxarticles__oxparentid = new oxField($oArticle->oxarticles__oxid->value, oxField::T_RAW);
        $oArticle2->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testVar');
        $sSelect = $oArticle->UNITgenerateSearchStrForCustomerBought();

        $sArtTable = $oArticle->UNITgetObjectViewName('oxarticles');
        $sOrderArtTable = getViewName('oxorderarticles');

        $sExpSelect = "select distinct {$sArtTable}.* from (
                   select d.oxorderid as suborderid from {$sOrderArtTable} as d use index ( oxartid ) where d.oxartid in ( '_testVar', '_testArt', '_testArt2' ) limit 50
               ) as suborder
               left join {$sOrderArtTable} force index ( oxorderid ) on suborder.suborderid = {$sOrderArtTable}.oxorderid
               left join {$sArtTable} on {$sArtTable}.oxid = {$sOrderArtTable}.oxartid
               where {$sArtTable}.oxid not in ( '_testVar', '_testArt' , '_testArt2' )
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' ) and " . $oArticle->getSqlActiveSnippet();

        $sExpSelect = str_replace(array("\n", "\r", "\t", " "), "", $sExpSelect);
        $sSelect = str_replace(array("\n", "\r", "\t", " "), "", $sSelect);

        $this->assertEquals($sExpSelect, $sSelect);
    }

    /**
     * Test load amount price info.
     *
     * @return null
     */
    public function testLoadAmountPriceInfo()
    {
        oxArticleHelper::resetAmountPrice();
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('1651');
        $oArticle->setVar('blCalcPrice', true);
        $oAmPriceList = $oArticle->loadAmountPriceInfo();

        if ($this->getConfig()->getEdition() === 'EE') {
            $amount = 3;
        } else {
            $amount = 4;
        }

        $this->assertEquals($amount, count($oAmPriceList));
        $this->assertEquals(27.5, $oArticle->getPrice(6)->getBruttoPrice());

    }

    /**
     * Test if works correctly when skipping discounts.
     *
     * Fix for bug entry 0005641: Fatal Error after activating oxskipdiscounts
     *
     * @return null
     */
    public function testLoadAmountPriceInfo_skipDiscounts_noErrorThrown()
    {
        oxArticleHelper::resetAmountPrice();
        $oArticle = $this->getMock('oxArticle', array('skipDiscounts'));
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(true));
        $oArticle->load('1651');
        $oAmPriceList = $oArticle->loadAmountPriceInfo();

        $this->assertEquals(0, count($oAmPriceList));
    }

    /**
     * Test load amount price info don't calc price.
     *
     * @return null
     */
    public function testLoadAmountPriceInfoDontCalcPrice()
    {
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('1651');
        $oArticle->setVar('blCalcPrice', false);
        $oAmPriceList = $oArticle->loadAmountPriceInfo();

        $this->assertEquals(0, count($oAmPriceList));
    }

    /**
     * Test load amount price info without amount price.
     *
     * @return null
     */
    public function testLoadAmountPriceInfoWithoutAmountPrice()
    {
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('2000');
        $oArticle->setVar('blCalcPrice', true);
        $oAmPriceList = $oArticle->loadAmountPriceInfo();

        $this->assertEquals(0, count($oAmPriceList));
    }

    /**
     * Test load amount price info for variant.
     *
     * @return null
     */
    public function testLoadAmountPriceInfoForVariant()
    {
        $this->_createArticle('_testArt', '_testVar');

        $sShopId = $this->getConfig()->getShopId();
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '" . $sShopId . "', 10, 10, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $this->getConfig()->setConfigParam('blVariantInheritAmountPrice', true);

        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->setVar('blCalcPrice', true);
        $oAmPriceList = $oVariant->loadAmountPriceInfo();

        $this->assertEquals(1, count($oAmPriceList));
    }

    public function testLoadAmountPriceInfoToGetBruttoAndNetto()
    {
        /** @var oxBase $item */
        $item = oxNew('oxBase');

        /** @var oxAmountPriceList $amountPriceList */
        $amountPriceList = oxNew('oxAmountPriceList');
        $amountPriceList->assign(array($item));

        /** @var oxArticle|PHPUnit_Framework_TestCase $article */
        $article = oxNew('oxArticle');
        $article->setAmountPriceList($amountPriceList);
        $article->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $priceList = $article->loadAmountPriceInfo();

        $this->assertEquals('10,00', $priceList[0]->fbrutprice, 'Brut price was not loaded.');
        $this->assertEquals('10,00', $priceList[0]->fnetprice, 'Net price was not loaded.');
    }

    /**
     * Test get sql active snippet.
     *
     * @return null
     */
    public function testGetSqlActiveSnippet()
    {
        $this->getConfig()->setConfigParam('blUseTimeCheck', false);

        $oArticle = $this->_createArticle('_testArt');
        $sTable = $oArticle->getViewName();
        $oArticle->setAdminMode(true);
        $sInsert = "";
        if (!$this->getConfig()->getConfigParam('blVariantParentBuyable')) {
            $sInsert = " and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and  art.oxactive = 1  and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";
        }
        $sExpSelect = "(  $sTable.oxactive = 1  and $sTable.oxhidden = 0  and ( $sTable.oxstockflag != 2 or ( $sTable.oxstock + $sTable.oxvarstock ) > 0  ) $sInsert ) ";
        $sSelect = $oArticle->getSqlActiveSnippet();
        $this->assertEquals(str_replace(array(" ", "\n", "\t", "\r"), "", $sExpSelect), str_replace(array(" ", "\n", "\t", "\r"), "", $sSelect));
    }

    /**
     * Test get sql active snippet dont use stock.
     *
     * @return null
     */
    public function testGetSqlActiveSnippetDontUseStock()
    {
        $iCurrTime = 0;

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $this->getConfig()->setConfigParam('blUseStock', false);
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->setAdminMode(true);
        $sTable = $oArticle->getViewName();
        $sDate = date('Y-m-d H:i:s', $iCurrTime);
        $sExpSelect = "(  (   $sTable.oxactive = 1  and $sTable.oxhidden = 0  or  ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) )  ) ";
        $sSelect = $oArticle->getSqlActiveSnippet();
        $this->assertEquals($sExpSelect, $sSelect);
    }



    /**
     * Test get variants.
     *
     * @return null
     */
    public function testGetVariants()
    {
        $this->getConfig()->setConfigParam('blUseStock', false);
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->save();
        $oVariants = $oArticle->getVariants();
        $this->assertEquals(1, count($oVariants));
        $this->assertEquals('_testVar', $oVariants['_testVar']->oxarticles__oxid->value);
        $this->assertEquals('test', $oVariants['_testVar']->oxarticles__oxtitle->value);
    }

    /**
     * Test get variants with stock.
     *
     * @return null
     */
    public function testGetVariantsWithStock()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $this->getConfig()->setConfigParam('blUseStock', true);
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oVariant->oxarticles__oxstockflag = new oxField(3, oxField::T_RAW);
        $oVariant->save();
        $oA = oxNew('oxArticle');
        $oA->load($oArticle->getId());

        $oVariants = $oA->getVariants(false);
        $this->assertEquals(1, count($oVariants));
        $this->assertEquals('_testVar', $oVariants['_testVar']->oxarticles__oxid->value);
        $this->assertEquals('test', $oVariants['_testVar']->oxarticles__oxtitle->value);
        $oVariants = $oA->getVariants(true);
        $this->assertEquals(0, count($oVariants));
    }

    /**
     * Test get variants cached.
     *
     * @return null
     */
    public function testGetVariantsCached()
    {
        $oSubj = $this->getProxyClass('oxarticle');
        $oSubj->setId("123");
        $oSubj->oxarticles__oxvarcount = new oxField(10);
        $oSubj->setInList();
        $oSubj->setNonPublicVar("_aVariants", array('simple' => 'testval1'));
        $oSubj->setNonPublicVar("_aVariantsWithNotOrderables", array('simple' => 'testval2'));
        $this->assertEquals('testval2', $oSubj->getVariants(false));
        $this->assertEquals('testval1', $oSubj->getVariants(true));
        $this->assertEquals('testval1', $oSubj->getVariants());
    }

    /**
     * Test get variants in list.
     *
     * @return null
     */
    public function testGetVariantsInList()
    {
        $oSubj = $this->getProxyClass('oxarticle');
        $oSubj->setInList();
        if ($this->getConfig()->getEdition() === 'EE') {
            $sArtId = "2229";
        } else {
            $sArtId = "2077";
        }

        $oSubj->load($sArtId);
        $oVariants = $oSubj->getVariants();

        $this->assertTrue(count($oVariants) > 0);
        foreach ($oVariants as $oVariant) {
            $this->assertTrue($oVariant instanceof oxSimpleVariant);
        }
    }

    /**
     * Test get variants not in list.
     *
     * @return null
     */
    public function testGetVariants_NOT_InList()
    {
        $oSubj = $this->getProxyClass('oxarticle');

        if ($this->getConfig()->getEdition() === 'EE') {
            $sArtId = "2229";
        } else {
            $sArtId = "2077";
        }

        $oSubj->load($sArtId);
        $oVariants = $oSubj->getVariants();

        $this->assertTrue(count($oVariants) > 0);
        foreach ($oVariants as $oVariant) {
            $this->assertTrue($oVariant instanceof oxArticle);
        }
    }

    /**
     * Test get variants with disabled variant loading.
     *
     * @return null
     */
    public function testGetVariantsIfNoVariantLoading()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->setNonPublicVar("_blLoadVariants", false);
        $this->assertEquals(0, count($oArticle->getVariants()));
    }

    /**
     * Test get variants with empty varcount.
     *
     * @return null
     */
    public function testGetVariantsEmptyVarCount()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxvarcount = new oxField(0, oxField::T_RAW);
        $this->assertEquals(0, count($oArticle->getVariants()));
    }

    /**
     * Test get variants with selectlists enabled.
     *
     * @return null
     */
    public function testGetVariantsLoadSelectLists()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $this->getConfig()->setConfigParam('blUseStock', false);
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->save();
        $oVariants = $oArticle->getVariants();
        $this->assertEquals(1, count($oVariants));
        $this->assertEquals('_testVar', $oVariants['_testVar']->oxarticles__oxid->value);
        $this->assertEquals('test', $oVariants['_testVar']->oxarticles__oxtitle->value);
    }

    /**
     * Test get variants when not active.
     *
     * @return null
     */
    public function testGetVariantsNotActive()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oVariants = $oArticle->getVariants();
        $this->assertEquals(1, count($oVariants));
    }

    /**
     * Test get variants with disabled variant loading and varcount.
     *
     * @return null
     */
    public function testGetVariantsDoNotLoad()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $this->getConfig()->setConfigParam('blLoadVariants', false);
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oVariants = $oArticle->getVariants();
        $this->assertEquals(0, count($oVariants));
    }

    /**
     * Test remove inactive variants when not no stock.
     *
     * @return null
     */
    public function testRemoveInactiveVariantsNoStock()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $oParent = oxNew('oxArticle');
        $oParent->load($oVariant->oxarticles__oxparentid->value);

        $oVL = $oParent->getVariants(true);
        $this->assertTrue($oVL instanceof oxList);
        $this->assertEquals(1, $oVL->count());

        $oVL = $oParent->getVariants(false);
        $this->assertTrue($oVL instanceof oxList);
        $this->assertEquals(1, $oVL->count());

        // article stockflag is marked as offline
        $oVariant->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oVariant->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oVariant->save();

        // reloading - resetting cache
        $oParent = oxNew('oxArticle');
        $oParent->load($oVariant->oxarticles__oxparentid->value);
        $this->assertEquals(0, $oParent->getVariants(true)->count());
        $this->assertEquals(0, $oParent->getVariants(false)->count());

        // article stockflag is marked as noorder
        $oVariant->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oVariant->oxarticles__oxstockflag = new oxField(3, oxField::T_RAW);
        $oVariant->save();
        // reloading - resetting cache
        $oParent = oxNew('oxArticle');
        $oParent->load($oVariant->oxarticles__oxparentid->value);
        $this->assertEquals(0, $oParent->getVariants(true)->count());
        $this->assertEquals(1, $oParent->getVariants(false)->count());

    }

    /**
     * Test remove inactive variants when not no stock and not orderable.
     *
     * M:508
     *
     * @return null
     */
    public function testRemoveInactiveVariantsNoStockAndNotOrderable()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $oVariant->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oVariant->oxarticles__oxstockflag = new oxField(3, oxField::T_RAW);
        $oVariant->save();

        $oParent = oxNew('oxArticle');

        $oParent->load($oVariant->oxarticles__oxparentid->value);
        $oVarList = $oParent->getVariants(false);

        // list must contain one item
        $this->assertEquals(1, $oVarList->count());
        $this->assertEquals($oVariant->getId(), $oVarList[$oVariant->getId()]->oxarticles__oxid->value);

        // list must contain NO items
        $oVarList = $oParent->getVariants(true);
        $this->assertEquals(0, $oVarList->count());
    }

    /**
     * Test remove inactive variants from oxlist.
     *
     * @return null
     */
    public function testRemoveInactiveVariantsForeachWorksForOxList()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $oParent = oxNew('oxArticle');
        $oParent->load($oVariant->oxarticles__oxparentid->value);
        $oVL = $oParent->getVariants(true);
        $this->assertTrue($oVL instanceof oxList);
        $this->assertEquals(1, $oVL->count());

        $oVL = $oParent->getVariants(false);
        $this->assertTrue($oVL instanceof oxList);
        $this->assertEquals(1, $oVL->count());

        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $oVariant->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oVariant->save();

        $oParent = oxNew('oxArticle');
        $oParent->load($oVariant->oxarticles__oxparentid->value);
        $this->assertEquals(0, $oParent->getVariants(false)->count());
        $this->assertEquals(0, $oParent->getVariants(true)->count());
    }

    /**
     * Test remove inactive variants when not active.
     *
     * @return null
     */
    public function testRemoveInactiveVariantsNotActive()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $oVariant->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oVariant->save();

        $oParent = oxNew('oxArticle');
        $oParent->load('_testArt');
        $this->assertEquals(0, $oParent->getVariants(false)->count());
        $this->assertEquals(0, $oParent->getVariants(true)->count());
        $this->assertTrue($oParent->isNotBuyable());
    }

    /**
     * Test remove inactive variants ant check if it sets not buyable flag to parent.
     *
     * @return null
     */
    public function testRemoveInactiveVariantsSetsNotBuyableParentFlag()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);

        $oVariant->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oVariant->save();

        $oParent = oxNew('oxArticle');
        $oParent->load($oVariant->oxarticles__oxparentid->value);
        $this->assertEquals(0, $oParent->getVariants(false)->count());
        $this->assertTrue($oParent->isParentNotBuyable());

        $this->assertEquals(0, $oParent->getVariants(true)->count());
        $this->assertTrue($oParent->isParentNotBuyable());
    }

    /**
     * Test get vendor id.
     *
     * @return null
     */
    public function testGetVendorId()
    {
        $oArticle = $this->_createArticle('_testArt');

        if ($this->getConfig()->getEdition() === 'EE') {
            $sVendId = 'd2e44d9b31fcce448.08890330';
        } else {
            $sVendId = '68342e2955d7401e6.18967838';
        }

        $oArticle->oxarticles__oxvendorid = new oxField($sVendId, oxField::T_RAW);
        $this->assertEquals($sVendId, $oArticle->getVendorId(true));
        $this->assertEquals($sVendId, $oArticle->getVendorId());
    }

    /**
     * Test get vendor id when it is not set.
     *
     * @return null
     */
    public function testGetVendorIdNotSet()
    {
        $sVendorId = $this->_createArticle('_testArt')->getVendorId(true);
        $this->assertFalse($sVendorId);
    }

    /**
     * Test get manufacturer id.
     *
     * @return null
     */
    public function testGetManufacturerId()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $sManId = '88a996f859f94176da943f38ee067984';
        } else {
            $sManId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        }

        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManId, oxField::T_RAW);
        $this->assertEquals($sManId, $oArticle->getManufacturerId(true));
        $this->assertEquals($sManId, $oArticle->getManufacturerId());
    }

    /**
     * Test get manufacturer id when it is not set.
     *
     * @return null
     */
    public function testGetManufacturerIdNotSet()
    {
        $sVendorId = $this->_createArticle('_testArt')->getManufacturerId(true);
        $this->assertFalse($sVendorId);
    }

    /**
     * Test get manufacturer id for non existing vendor.
     *
     * @return null
     */
    public function testGetManufacturerIdNotExist()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvendorid = new oxField('_xxx', oxField::T_RAW);
        $oArticle->save();
        $sVendorId = $oArticle->getManufacturerId(true);
        $this->assertFalse($sVendorId);
    }

    /**
     * Test get vendor and id.
     *
     * @return null
     */
    public function testGetVendorAndId()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sVendId = 'd2e44d9b31fcce448.08890330';
        }

        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvendorid = new oxField($sVendId);
        $oArticle->save();
        $oExpVendor = oxNew('oxvendor');
        $oExpVendor->load($sVendId);

        $oVendor = $oArticle->getVendor();
        $this->assertEquals($oExpVendor->oxvendor__oxtitle->value, $oVendor->oxvendor__oxtitle->value);
    }

    /**
     * Test get vendor.
     *
     * @return null
     */
    public function testGetVendor()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sVendId = 'd2e44d9b31fcce448.08890330';
        }

        $oArticle = $this->getMock('oxarticle', array('getVendorId'));
        $oArticle->expects($this->any())->method('getVendorId')->will($this->returnValue(false));
        $oArticle->oxarticles__oxvendorid = new oxField($sVendId);

        $oExpVendor = oxNew('oxvendor');
        $oExpVendor->load($sVendId);

        $oVendor = $oArticle->getVendor(false);
        $this->assertEquals($oExpVendor->oxvendor__oxtitle->value, $oVendor->oxvendor__oxtitle->value);
    }

    /**
     * Test get vendor readonly.
     *
     * @return null
     */
    public function testGetVendorReadonly()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sVendId = 'd2e44d9b31fcce448.08890330';
        }

        $oArticle = $this->getMock('oxarticle', array('getVendorId'));
        $oArticle->expects($this->any())->method('getVendorId')->will($this->returnValue(false));
        $oArticle->oxarticles__oxvendorid = new oxField($sVendId);

        $oVendor = $oArticle->getVendor(false);
        $this->assertNotNull($oVendor);
        $this->assertTrue($oVendor->isReadOnly());
    }

    /**
     * Test get vendor when not set.
     *
     * @return null
     */
    public function testGetVendorNotSet()
    {
        $this->assertNull($this->_createArticle('_testArt')->getVendor());
    }

    /**
     * Test get manufacturer and id.
     *
     * @return null
     */
    public function testGetManufacturerAndId()
    {
        $sManId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sManId = '88a996f859f94176da943f38ee067984';
        }
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManId, oxField::T_RAW);
        $oMan = $oArticle->getManufacturer();
        $oExpMan = oxNew('oxManufacturer');
        $oExpMan->load($sManId);
        $this->assertEquals($oExpMan->oxmanufacturers__oxtitle->value, $oMan->oxmanufacturers__oxtitle->value);
    }

    /**
     * Test get manufacturer.
     *
     * @return null
     */
    public function testGetManufacturer()
    {
        $sManId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sManId = '88a996f859f94176da943f38ee067984';
        }

        $oArticle = $this->getMock('oxarticle', array('getManufacturerId'));
        $oArticle->expects($this->any())->method('getManufacturerId')->will($this->returnValue(false));
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManId, oxField::T_RAW);

        $oExpMan = oxNew('oxManufacturer');
        $oExpMan->load($sManId);

        $oMan = $oArticle->getManufacturer(false);
        $this->assertEquals($oExpMan->oxmanufacturers__oxtitle->value, $oMan->oxmanufacturers__oxtitle->value);
    }

    /**
     * Test get manufacturer when readonly.
     *
     * @return null
     */
    public function testGetManufacturerReadOnly()
    {
        $sManId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sManId = '88a996f859f94176da943f38ee067984';
        }

        $this->getConfig()->setConfigParam('bl_perfLoadManufacturerTree', false);
        $oArticle = $this->getMock('oxarticle', array('getManufacturerId'));
        $oArticle->expects($this->any())->method('getManufacturerId')->will($this->returnValue(false));
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManId, oxField::T_RAW);

        $oMan = $oArticle->getManufacturer(false);
        $this->assertNotNull($oMan);
        $this->assertTrue($oMan->isReadOnly());
    }

    /**
     * Test get manufacturer when not set.
     *
     * @return null
     */
    public function testGetManufacturerNotSet()
    {
        $this->assertNull($this->_createArticle('_testArt')->getManufacturer());
    }

    /**
     * Test get search string.
     *
     * @return null
     */
    public function testGenerateSearchStr()
    {
        $sCatView = getViewName('oxcategories');
        $sO2CView = getViewName('oxobject2category');

        $oArticle = $this->_createArticle('_testArt');

        $sAxpSelect = "select $sCatView.* from $sO2CView as oxobject2category left join $sCatView on
                        $sCatView.oxid = oxobject2category.oxcatnid
                        where oxobject2category.oxobjectid='" . $oArticle->getId() . "' and $sCatView.oxid is not null ";

        $sSelect = $oArticle->UNITgenerateSearchStr($oArticle->getId());
        $this->assertEquals(preg_replace('/\W/', '', $sAxpSelect), preg_replace('/\W/', '', $sSelect));
    }

    /**
     * Test get search string with price category.
     *
     * @return null
     */
    public function testGenerateSearchStrWithSearchPriceCat()
    {
        $sCatView = getViewName('oxcategories');
        $oArticle = $this->_createArticle('_testArt');

        $oArticle->oxarticles__oxprice = new oxField(5, oxField::T_RAW);

        $sAxpSelect = "select {$sCatView}.* from [$sCatView} where
                       '{$oArticle->oxarticles__oxprice->value}' >= {$sCatView}.oxpricefrom and
                       '{$oArticle->oxarticles__oxprice->value}' <= {$sCatView}.oxpriceto ";

        $sSelect = $oArticle->UNITgenerateSearchStr($oArticle->getId(), true);
        $this->assertEquals(preg_replace('/\W/', '', $sAxpSelect), preg_replace('/\W/', '', $sSelect));
    }

    /**
     * Test if get category ads sql limit.
     *
     * @return null
     */
    public function testgetCategoryAddsSqlLimit()
    {
        oxTestModules::addFunction('oxcategory', 'assignRecord($sql)', '{throw new Exception($sql);}');
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("123");
        try {
            $oArticle->getCategory();
        } catch (Exception $e) {
            $this->assertTrue((bool) preg_match('/limit 1$/s', rtrim($e->getMessage())), 'regexp /limit 1$/ failed for ' . $e->getMessage());

            return;
        }
        $this->fail();
    }

    /**
     * Test get assigned article category.
     *
     * @return null
     */
    public function testGetCategoryAssignedToCategory()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $oCategory = $oArticle->getCategory();

        if ($this->getConfig()->getEdition() === 'EE') {
            $sCatId = "30e44ab8593023055.23928895";
        } else {
            $sCatId = "8a142c3e49b5a80c1.23676990";
        }

        $this->assertNotNull($oCategory);
        $this->assertEquals($sCatId, $oCategory->getId());
    }

    /**
     * Tests if the "oxarticle::getCategory()" uses a cached value
     *
     * @return null
     */
    public function testGetCategoryCached()
    {
        // test variables
        $sCacheIndex = "test";
        $sCacheResult = "already cached";
        $aCache = array($sCacheIndex => $sCacheResult);

        // setting the "cached" variables
        $oArticle = $this->getProxyClass('oxarticle');
        $oArticle->setNonPublicVar('_aCategoryCache', $aCache);

        // setting the used article ID
        $oArticle->setId($sCacheIndex);

        // asserts are equals if the articles ID is in the caches index
        // and returns the cached result
        $this->assertEquals($sCacheResult, $oArticle->getCategory());
    }

    /**
     * Test get category by price.
     *
     * buglist#329 price category test
     *
     * @return null
     */
    public function testGetCategoryByPrice()
    {
        // creating price category
        $oPriceCategory = oxNew('oxcategory');
        $oPriceCategory->setId('_testcat');
        $oPriceCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oPriceCategory->oxcategories__oxleft = new oxField('1', oxField::T_RAW);
        $oPriceCategory->oxcategories__oxright = new oxField('2', oxField::T_RAW);
        $oPriceCategory->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oPriceCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCategory->oxcategories__oxhidden = new oxField(0, oxField::T_RAW);
        $oPriceCategory->oxcategories__oxpricefrom = new oxField(99, oxField::T_RAW);
        $oPriceCategory->oxcategories__oxpriceto = new oxField(101, oxField::T_RAW);
        $oPriceCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oPriceCategory->save();


        // creating not assigned article
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testprod');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(100, oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("test", oxField::T_RAW);
        $oArticle->save();

        $oCategory = $oArticle->getCategory();

        $this->assertNotNull($oCategory);
        $this->assertEquals($oPriceCategory->getId(), $oCategory->getId());
    }

    /**
     * Test get price category.
     *
     * @return null
     */
    public function testGetPriceCategory()
    {
        $oArticle = $this->_createArticle('_testArt');

        $oPriceCat = oxNew('oxcategory');
        $oPriceCat->setId('_testCat');
        $oPriceCat->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oPriceCat->oxcategories__oxextlink = new oxField('extlink', oxField::T_RAW);
        $oPriceCat->oxcategories__oxtitle = new oxField('test', oxField::T_RAW);
        $oPriceCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxhidden = new oxField(0, oxField::T_RAW);
        $oPriceCat->oxcategories__oxleft = new oxField('1', oxField::T_RAW);
        $oPriceCat->oxcategories__oxright = new oxField('2', oxField::T_RAW);
        $oPriceCat->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oPriceCat->oxcategories__oxpricefrom = new oxField('10', oxField::T_RAW);
        $oPriceCat->oxcategories__oxpriceto = new oxField('50', oxField::T_RAW);
        $oPriceCat->save();
        $oArticle->oxarticles__oxprice = new oxField(25, oxField::T_RAW);
        $oCat = $oArticle->getCategory();
        $this->assertEquals($oPriceCat->getId(), $oCat->getId());
    }

    /**
     * Test get price category for variant.
     *
     * @return null
     */
    public function testGetPriceCategoryForVar()
    {
        $oPriceCat = oxNew('oxcategory');
        $oPriceCat->setId('_testCat');
        $oPriceCat->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oPriceCat->oxcategories__oxextlink = new oxField('extlink', oxField::T_RAW);
        $oPriceCat->oxcategories__oxtitle = new oxField('test', oxField::T_RAW);
        $oPriceCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxhidden = new oxField(0, oxField::T_RAW);
        $oPriceCat->oxcategories__oxleft = new oxField('1', oxField::T_RAW);
        $oPriceCat->oxcategories__oxright = new oxField('2', oxField::T_RAW);
        $oPriceCat->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oPriceCat->oxcategories__oxpricefrom = new oxField('10', oxField::T_RAW);
        $oPriceCat->oxcategories__oxpriceto = new oxField('50', oxField::T_RAW);
        $oPriceCat->save();
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oArticle->oxarticles__oxprice = new oxField(25, oxField::T_RAW);
        $oArticle->save();
        $oVariant->oxarticles__oxprice = new oxField(75, oxField::T_RAW);
        $oCat = $oArticle->getCategory();
        $this->assertEquals($oPriceCat->oxcategories__oxid->value, $oCat->oxcategories__oxid->value);
    }

    /**
     * Test if get category returns empty result.
     *
     * @return null
     */
    public function testGetCategoryEmpty()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(75, oxField::T_RAW);
        $oCat = $oArticle->getCategory();
        $this->assertNull($oCat);
    }

    /**
     * Test if article is in category.
     *
     * @return null
     */
    public function testInCategory()
    {
        $oArticle = $this->getMock('oxarticle', array('getCategoryIds'));
        $oArticle->expects($this->any())->method('getCategoryIds')->will($this->returnValue(array('123', '234')));
        $this->assertTrue($oArticle->inCategory('123'));
    }

    /**
     * Test method isassignedtocategory when is assigned.
     *
     * @return null
     */
    public function testIsAssignedToCategoryIsAssigned()
    {
        $sCat = "8a142c3e4143562a5.46426637";
        $sSql = "insert into oxobject2category (oxid, oxobjectid, oxcatnid) values ('test', '_testArt', '$sCat' )";
        if ($this->getConfig()->getEdition() === 'EE') :
            $sCat = "30e44ab82c03c3848.49471214";
            $sSql = "insert into oxobject2category (oxid, oxobjectid, oxcatnid) values ('test', '_testArt', '$sCat')";
        endif;
        $this->addToDatabase($sSql, 'oxobject2category');
        $this->assertTrue($this->_createArticle('_testArt')->isAssignedToCategory($sCat));
    }

    /**
     * Test method isassignedtocategory when is assigned to price category.
     *
     * @return null
     */
    public function testIsAssignedToCategoryIsAssignedIfPriceCat()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(25, oxField::T_RAW);
        $oArticle->save();
        $this->addToDatabase("insert into oxcategories (oxid, oxparentid, oxshopid, oxtitle, oxactive, oxleft, oxright, oxrootid, oxpricefrom, oxpriceto, oxlongdesc, oxlongdesc_1, oxlongdesc_2, oxlongdesc_3) values ('_testCat', 'oxrootid', '1', 'test', 1, '1', '2', '_testCat', '10', '50', '', '', '', '')", 'oxcategories');
        $this->assertTrue($oArticle->isAssignedToCategory('_testCat'));
    }

    /**
     * Test method isassignedtocategory when not assigned.
     *
     * @return null
     */
    public function testIsAssignedToCategoryIsNotAssigned()
    {
        $sCat = "8a142c3e4143562a5.46426637";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sCat = "30e44ab82c03c3848.49471214";
        }
        $this->assertFalse($this->_createArticle('_testArt', '_testVar')->isAssignedToCategory($sCat));
    }

    /**
     * Test method isassignedtocategory with price = 0.
     *
     * @return null
     */
    public function testIsAssignedToCategoryWithPriceZero()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxprice = new oxField(0, oxField::T_RAW);
        $oArticle->save();
        $sCat = "8a142c3e4143562a5.46426637";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sCat = "30e44ab82c03c3848.49471214";
        }
        $this->assertFalse($oArticle->isAssignedToCategory($sCat));
    }

    /**
     * Test method isassignedtocategory with variant.
     *
     * @return null
     */
    public function testIsAssignedToCategoryVariant()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oArticle->oxarticles__oxprice = new oxField(0, oxField::T_RAW);
        $oArticle->save();
        $oVariant->oxarticles__oxprice = new oxField(25, oxField::T_RAW);
        $oVariant->save();
        $this->addToDatabase("insert into oxcategories (oxid, oxparentid, oxshopid, oxtitle, oxactive, oxleft, oxright, oxrootid, oxpricefrom, oxpriceto, oxlongdesc, oxlongdesc_1, oxlongdesc_2, oxlongdesc_3) values ('_testCat', 'oxrootid', '1', 'test', 1, '1', '2', '_testCat', '10', '50', '', '', '', '')", 'oxcategories');
        $this->assertTrue($oVariant->isAssignedToCategory('_testCat'));
    }

    /**
     * Test get old price.
     *
     * @return null
     */
    public function testGetTPrice()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $oArticle->oxarticles__oxtprice = new oxField(25, oxField::T_RAW);
        $oTPrice = $oArticle->getTPrice();
        $this->assertEquals(25, $oTPrice->getBruttoPrice());
        $this->assertEquals(7, $oTPrice->getVat());
    }

    /**
     * Test get cached old price.
     *
     * @return null
     */
    public function testGetTPriceCached()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $oArticle->oxarticles__oxtprice = new oxField(25, oxField::T_RAW);
        $oTPrice = $oArticle->getTPrice();
        $oArticle->oxarticles__oxvat = new oxField(19, oxField::T_RAW);
        $oArticle->oxarticles__oxtprice = new oxField(30, oxField::T_RAW);
        $oTPrice = $oArticle->getTPrice();
        $this->assertEquals(25, $oTPrice->getBruttoPrice());
        $this->assertEquals(7, $oTPrice->getVat());
    }

    /**
     * Test skip discounts option.
     *
     * @return null
     */
    public function testSkipDiscounts()
    {
        $oArticle = $this->_createArticle('_testArt');

        // making category
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCat');
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxtitle = new oxField('Test category 1', oxField::T_RAW);
        $oCategory->oxcategories__oxskipdiscounts = new oxField('1', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertTrue(oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories());

        // assigning article to category
        $oArt2Cat = oxNew("oxobject2category");
        $oArt2Cat->oxobject2category__oxobjectid = new oxField($oArticle->oxarticles__oxid->value, oxField::T_RAW);
        $oArt2Cat->oxobject2category__oxcatnid = new oxField('_testCat', oxField::T_RAW);
        $oArt2Cat->save();

        $this->assertTrue($oArticle->skipDiscounts());
    }

    /**
     * Test skip discounts getter.
     *
     * @return null
     */
    public function testSkipDiscountsForArt()
    {
        $oArticle = $this->_createArticle('_testArt');
        // making category
        $oArticle->oxarticles__oxskipdiscounts = new oxField(1, oxField::T_RAW);

        $this->assertTrue($oArticle->skipDiscounts());
    }

    /**
     * Test cached skip discounts option.
     *
     * @return null
     */
    public function testSkipDiscountsCached()
    {
        $oArticle = $this->_createArticle('_testArt');

        // making category
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCat');
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxtitle = new oxField('Test category 1', oxField::T_RAW);
        $oCategory->oxcategories__oxskipdiscounts = new oxField('1', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertTrue(oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories(), 'we have skip dicounts');

        // assigning article to category
        $oArt2Cat = oxNew("oxobject2category");
        $oArt2Cat->oxobject2category__oxobjectid = new oxField($oArticle->oxarticles__oxid->value, oxField::T_RAW);
        $oArt2Cat->oxobject2category__oxcatnid = new oxField('_testCat', oxField::T_RAW);
        $oArt2Cat->save();

        $oArticle->skipDiscounts();
        $this->assertTrue($oArticle->skipDiscounts(), 'after first usage');

        $oCategory->oxcategories__oxskipdiscounts = new oxField('0', oxField::T_RAW);
        $oCategory->save();

        $this->assertTrue($oArticle->skipDiscounts(), 'after removing skip discount from category');
    }

    /**
     * Test price setter.
     *
     * @return null
     */
    public function testSetPrice()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(125);
        $oArticle->setPrice($oPrice);
        $oTPrice = $oArticle->getPrice();
        $this->assertEquals(125, $oTPrice->getBruttoPrice());
    }

    /**
     * Test price setter disabled performance option.
     *
     * @return null
     */
    public function testGetPricePerformance()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadPrice', false);
        $this->assertNull($this->_createArticle('_testArt')->getPrice());
    }

    /**
     * Test price getter when parent buyable with disabled performance option.
     *
     * buglist#413 if bl_perfLoadPriceForAddList variant price shouldn't be loaded too
     *
     * @return null
     */
    public function testGetPricePerformanceIfVariantHasPrice()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);

        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oArticle->save();

        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxstock = new oxField(5, oxField::T_RAW);
        $oVariant->save();

        $oArticle = oxNew('oxArticleHelper');
        $oArticle->disablePriceLoad($oArticle);
        $oArticle->load($oArticle->getId());

        $this->assertNull($oArticle->getPrice());
    }

    /**
     * Test price getter calls base price getter only with disabled calcprice option.
     *
     * @return null
     */
    public function testGetPriceCallsGetBasePriceOnlyInNoCalcPrice()
    {
        $oArticle = $this->getMock('oxArticleHelper', array('getBasePrice', '_applyCurrency'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(123));
        $oArticle->expects($this->never())->method('_applyCurrency');

        $oArticle->setVar('blCalcPrice', false);
        $oTPrice = $oArticle->getPrice();
        $this->assertEquals(123, $oTPrice->getBruttoPrice());

    }

    /**
     * Test price getter.
     *
     * @return null
     */
    public function testGetPrice()
    {
        $oArticle = $this->getMock('oxarticle', array('getBasePrice', 'skipDiscounts'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(123));
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(false));
        $oTPrice = $oArticle->getPrice();
        $this->assertEquals(123, $oTPrice->getBruttoPrice());
    }

    /**
     * Test base price getter disabled by performance option.
     *
     * @return null
     */
    public function testGetBasePricePerformance()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadPrice', false);
        $this->assertNull($this->_createArticle('_testArt')->getBasePrice());
    }

    /**
     * Test base price getter.
     *
     * @return null
     */
    public function testGetBasePrice()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(45, oxField::T_RAW);
        $this->assertEquals(45, $oArticle->getBasePrice());
    }

    /**
     * Test article VAT getter.
     *
     * @return null
     */
    public function testGetArticleVat()
    {
        oxTestModules::addFunction('oxVatSelector', 'getArticleVat', '{return 99;}');
        $oA = oxNew('oxArticle');
        $this->assertEquals(99, $oA->getArticleVat());
        oxTestModules::addFunction('oxVatSelector', 'getArticleVat', '{return 98;}');
        // cached value, do not recalculate
        $this->assertEquals(99, $oA->getArticleVat());
        // check for new article
        $oA = oxNew('oxArticle');
        $this->assertEquals(98, $oA->getArticleVat());
    }

    /**
     * Test apply VAT.
     *
     * @return null
     */
    public function testApplyVAT()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(125);
        $oArticle = oxNew('oxArticle');
        $oArticle->UNITapplyVAT($oPrice, 7);
        $this->assertEquals(7, $oPrice->getVat());
    }

    /**
     * Test apply VAT's.
     *
     * @return null
     */
    public function testApplyVats()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(125);

        oxTestModules::addFunction('oxVatSelector', 'getArticleVat', '{return 99;}');

        $oArticle = $this->getMock('oxArticle', array('_applyVAT'));
        $oArticle->expects($this->once())->method('_applyVAT')->will($this->returnValue(null))->with($oPrice, 99);

        $oArticle->applyVats($oPrice);
    }

    /**
     * Test apply user VAT.
     *
     * @return null
     */
    public function testApplyUserVAT()
    {
        oxTestModules::addFunction('oxVatSelector', 'getUserVat', '{return 19;}');

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(125);
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        $oArticle = oxNew('oxArticle');
        $oArticle->setUser($oUser);
        $oArticle->UNITapplyVAT($oPrice, 7);
        $this->assertEquals(19, $oPrice->getVat());
    }

    /**
     * Test apply discounts for variant.
     *
     * @return null
     */
    public function testApplyDiscountsForVariant()
    {
        oxRegistry::get("oxDiscountList")->forceReload();

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId("_testDiscount");
        $oDiscount->oxdiscount__oxactive = new oxField(1, oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(13, oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxpriceto = new oxField(999, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamountto = new oxField(999, oxField::T_RAW);
        $oDiscount->save();

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(123);
        $oArticle = oxNew('oxArticle');
        $oArticle->applyDiscountsForVariant($oPrice);
        $this->assertEquals(110, $oPrice->getBruttoPrice());
    }

    /**
     * Test apply currency.
     *
     * @return null
     */
    public function testApplyCurrency()
    {
        $this->setRequestParameter('currency', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(100);
        $oArticle = oxNew('oxArticle');
        $oArticle->UNITapplyCurrency($oPrice);
        $this->assertEquals(143.26, $oPrice->getBruttoPrice());
        $this->getConfig()->setActShopCurrency(0);
    }

    /**
     * Test apply currency with optional currency object.
     *
     * @return null
     */
    public function testApplyCurrencyIfObjSet()
    {
        $oCur = new StdClass;
        $oCur->rate = 0.68;
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(100);
        $oArticle = oxNew('oxArticle');
        $oArticle->UNITapplyCurrency($oPrice, $oCur);
        $this->assertEquals(68, $oPrice->getBruttoPrice());
    }

    /**
     * Test get basket price.
     *
     * @return null
     */
    public function testGetBasketPrice()
    {
        $oArticle = $this->getMock('oxarticle', array('getBasePrice', '_applyVAT', 'skipDiscounts'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(90));
        $oArticle->expects($this->any())->method('_applyVAT');
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(true));
        $oPrice = $oArticle->getBasketPrice(2, array(), new oxbasket());
        $this->assertEquals(90, $oPrice->getBruttoPrice());
    }

    /**
     * Test if get basket price sets basket user.
     *
     * @return null
     */
    public function testGetBasketPriceSetsBasketUser()
    {
        $oArticle = $this->getMock('oxarticle', array('getBasePrice', '_applyVAT', 'skipDiscounts'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(90));
        $oArticle->expects($this->any())->method('_applyVAT');
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(true));

        $oUser = oxNew('oxUser');
        $oUser->iamtheone = 'test';
        $oBasket = $this->getMock('oxbasket', array('getBasketUser'));
        $oBasket->expects($this->any())->method('getBasketUser')->will($this->returnValue($oUser));
        $oPrice = $oArticle->getBasketPrice(2, array(), $oBasket);
        $this->assertSame($oUser, $oArticle->getArticleUser());
    }

    /**
     * Test get basket price with discount.
     *
     * @return null
     */
    public function testGetBasketPriceWithDiscount()
    {
        oxRegistry::get("oxDiscountList")->forceReload();
        $oArticle = $this->getMock('oxarticle', array('getBasePrice', '_applyVAT', 'skipDiscounts'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(90));
        $oArticle->expects($this->any())->method('_applyVAT');
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(false));
        $oPrice = $oArticle->getBasketPrice(2, array(), new oxbasket());
        $this->assertEquals(90, $oPrice->getBruttoPrice());
    }

    /**
     * Test get basket price with same discount.
     *
     * @return null
     */
    public function testGetBasketPriceWithTheSameDiscount()
    {
        oxRegistry::get("oxDiscountList")->forceReload();
        $oArticle = $this->getMock('oxarticle', array('getBasePrice', '_applyVAT', 'skipDiscounts'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(90));
        $oArticle->expects($this->any())->method('_applyVAT');
        $oArticle->expects($this->any())->method('skipDiscounts')->will($this->returnValue(false));
        $oPrice = $oArticle->getBasketPrice(2, array(), new oxbasket());
        $this->assertEquals(90, $oPrice->getBruttoPrice());
    }

    /**
     * Test article delete.
     *
     * @return null
     */
    public function testDelete()
    {
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        oxTestModules::addFunction('oxSeoEncoderArticle', 'onDeleteArticle', '{$this->onDeleteArticleCnt++;}');
        oxRegistry::get("oxSeoEncoderArticle")->onDeleteArticleCnt = 0;

        $oVariant->delete();

        $oArticle = oxNew('oxArticle');
        $this->assertFalse($oArticle->load('_testVar'));
        $this->assertEquals(1, oxRegistry::get("oxSeoEncoderArticle")->onDeleteArticleCnt);
    }

    /**
     * Test article delete also deletes variants.
     * #2339 Articles with variants are not removed from oxseo when deleted
     *
     * @return null
     */
    public function testDeleteParentArt()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $sQtedObjectId = $oArticle->getId();
        $iQtedShopId = $this->getConfig()->getBaseShopId();
        oxDb::getDB()->execute(
            "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams)
                values ( '$sQtedObjectId', '$sQtedObjectId', '$iQtedShopId', '0', 'url', 'url', 'oxarticle', '1', '0', '' )"
        );

        $oArticle->delete();

        $oArticle = oxNew('oxArticle');
        $this->assertFalse($oArticle->load('_testArt'));
        $this->assertFalse($oArticle->load('_testVar'));
        $this->assertFalse(oxDb::getDB()->getOne("select 1 from oxseo where oxobjectid = '_testArt'"));
    }

    /**
     * Test empty article delete.
     *
     * @return null
     */
    public function testDeleteEmptyArt()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertFalse($oArticle->delete());
    }

    /**
     * Test article delete with optionall id parameter.
     *
     * @return null
     */
    public function testDeleteWithId()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertTrue($oArticle->delete('_testArt'));
    }

    /**
     * Test delete article variant records.
     *
     * @return null
     */
    public function testDeleteVariantRecords()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->UNITdeleteVariantRecords($oArticle->oxarticles__oxid->value);

        $oVariant = oxNew('oxArticle');
        $this->assertFalse($oVariant->load('_testVar'));
    }

    /**
     * Test delete records.
     *
     * @return null
     */
    public function testDeleteRecords()
    {
        $oArticle = $this->_createArticle('_testArt');

        oxDb::getDB()->execute("insert into oxobject2article (oxarticlenid, oxobjectid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2attribute (oxobjectid, oxattrid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2category (oxobjectid, oxcatnid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2selectlist (oxobjectid, oxselnid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxprice2article (oxartid, oxaddabs) values ('_testArt', 25 )");
        oxDb::getDB()->execute("insert into oxreviews (oxtype, oxobjectid, oxtext) values ('oxarticle', '_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxratings (oxobjectid, oxtype, oxrating) values ('_testArt', 'oxarticle', 5 )");
        oxDb::getDB()->execute("insert into oxaccessoire2article (oxobjectid, oxarticlenid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2delivery (oxobjectid, oxtype, oxdeliveryid) values ('_testArt', 'oxarticles', 'test' )");
        oxDb::getDB()->execute("update oxartextends set oxlongdesc = 'test' where oxid = '_testArt'");
        oxDb::getDB()->execute("insert into oxactions2article (oxartid, oxactionid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2list (oxobjectid, oxlistid) values ('_testArt', 'test' )");
        if ($this->getConfig()->getEdition() === 'EE') {
            oxDb::getDB()->execute("insert into oxfield2shop (oxartid, oxprice) values ('_testArt', 25 )");
        }
        $oArticle->UNITdeleteRecords('_testArt');
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxobject2article where oxarticlenid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxobject2attribute where oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxobject2category where oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxobject2selectlist where oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxprice2article where oxartid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxreviews where oxtype = 'oxarticle' and oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxratings where oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxaccessoire2article where oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxobject2delivery where oxobjectid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxartextends where oxid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxactions2article where oxartid = '_testArt'"));
        $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxobject2list where oxobjectid = '_testArt'"));
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->assertFalse(oxDb::getDB()->getOne("select oxid from oxfield2shop where oxartid = '_testArt'"));
        }
    }

    /**
     * Test get A group price.
     *
     * @return null
     */
    public function testGetGroupPricePriceA()
    {
        $oArticle = $this->_createArticle('_testArt');

        $oArticle->oxarticles__oxpricea = new oxField(12, oxField::T_RAW);
        $oArticle->save();

        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->returnValue(true));

        $oArticle = $this->getMock('oxarticle', array('getUser'));
        $oArticle->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oArticle->load('_testArt');

        $this->assertEquals(12, $oArticle->UNITgetGroupPrice());
    }

    /**
     * Test get B group price.
     *
     * @return null
     */
    public function testGetGroupPricePriceB()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxpriceb = new oxField(12, oxField::T_RAW);
        $oArticle->save();
        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(false), $this->returnValue(true), $this->returnValue(false)));
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->setUser($oUser);
        $this->assertEquals(12, $oArticle->UNITgetGroupPrice());
    }

    /**
     * Test get C group price.
     *
     * @return null
     */
    public function testGetGroupPricePriceC()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxpricec = new oxField(12, oxField::T_RAW);
        $oArticle->save();
        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(false), $this->returnValue(false), $this->returnValue(true)));
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->setUser($oUser);
        $oArticle->oxarticles__oxprice->value = 15;
        $this->assertEquals(12, $oArticle->UNITgetGroupPrice());
    }

    /**
     * Test if zero group prices are set generic price depending on config option.
     *
     * @return null
     */
    public function testModifyGroupPricePriceAZero()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxpricea = new oxField(0, oxField::T_RAW);
        $oArticle->save();
        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->returnValue(true));
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->setUser($oUser);
        $oArticle->oxarticles__oxprice->value = 15;
        $this->getConfig()->setConfigParam('blOverrideZeroABCPrices', false);
        $dPrice = $oArticle->UNITgetGroupPrice();
        $this->assertEquals(0, $dPrice);
        $this->getConfig()->setConfigParam('blOverrideZeroABCPrices', true);
        $oArticle->oxarticles__oxprice->value = 15;
        $this->assertEquals(15, $oArticle->UNITgetGroupPrice());
    }

    /**
     * Test get amount price without modification.
     *
     * @return null
     */
    public function testGetAmountPriceNoStaffelPrice()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice->value = 15;
        $this->assertEquals(15, $oArticle->UNITgetAmountPrice(2));
    }

    /**
     * Test modify select list price.
     *
     * FS#1916
     *
     * @return null
     */
    public function testModifySelectListPrice()
    {
        $oDb = oxDb::getDB();
        $myConfig = $this->getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        $sShopId = $myConfig->getBaseShopId();
        $sVal = 'three!P!-5,99__threeValue@@two!P!-2__twoValue@@';

        $sQ = 'insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ("_testoxsellist", "' . $sShopId . '", "_testoxsellist", "_testoxsellist", "' . $sVal . '")';
        $this->addToDatabase($sQ, 'oxselectlist');

        $sQ = 'insert into oxobject2selectlist (oxid, oxobjectid, oxselnid, oxsort) values ("_testoxsellist", "1651", "_testoxsellist", 1) ';
        $this->addToDatabase($sQ, 'oxobject2selectlist');

        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $this->getConfig()->setConfigParam('bl_perfUseSelectlistPrice', true);

        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('1651');
        $oArticle->cleanup();
        $this->assertEquals(4.01, $oArticle->UNITmodifySelectListPrice(10, array(0 => 0)));
    }

    /**
     * Test amount price loading.
     *
     * @return null
     */
    public function testAmountPricesLoading()
    {
        $oArticle = $this->_createArticle('_testArt');

        $sShopId = $this->getConfig()->getShopId();
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '" . $sShopId . "', 5.5, 10, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '" . $sShopId . "', 6.5, 5, 10 )";
        oxDb::getDB()->execute($sSql);

        $dBasePrice = $oArticle->getBasePrice(12);
        $this->assertEquals(5.5, $dBasePrice);
    }

    /**
     * Test amount price loading without given amount.
     *
     * @return null
     */
    public function testAmountPricesLoadingNotSpecificAmount()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');

        $sShopId = $this->getConfig()->getShopId();
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '" . $sShopId . "', 5.5, 10, 12 )";
        oxDb::getDB()->execute($sSql);
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '" . $sShopId . "', 6.5, 5, 10 )";
        oxDb::getDB()->execute($sSql);

        $dBasePrice = $oArticle->getBasePrice(13);
        $this->assertEquals(15.5, $dBasePrice);
    }

    /**
     * Test amount price loading for variants.
     *
     * @return null
     */
    public function testAmountPricesLoadingForVariants()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $this->getConfig()->setConfigParam('blVariantInheritAmountPrice', true);
        $sShopId = $this->getConfig()->getShopId();
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '" . $sShopId . "', 10, 11, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '" . $sShopId . "', 9, 5, 10 )";
        oxDb::getDB()->execute($sSql);

        //calling getBasePrice() because can't test protected functions with passed by reference arguments
        $oVariant = $this->getProxyClass('oxarticle');
        $oVariant->setAdminMode(null);
        $oVariant->load('_testVar');

        $dBasePrice = $oVariant->getBasePrice(12);
        $this->assertEquals(10.98, $dBasePrice);
    }

    /**
     * Test amount price loading for variants without given amount.
     *
     * @return null
     */
    public function testAmountPricesLoadingForVariantsNotSpecificAmount()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $this->getConfig()->setConfigParam('blVariantInheritAmountPrice', true);
        $sShopId = $this->getConfig()->getShopId();
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '" . $sShopId . "', 10, 11, 13 )";
        oxDb::getDB()->execute($sSql);
        $sSql = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '" . $sShopId . "', 11, 5, 10 )";
        oxDb::getDB()->execute($sSql);

        //calling getBasePrice() because can't test protected functions with passed by reference arguments
        $dBasePrice = $oVariant->getBasePrice(15);
        $this->assertEquals(12.2, $dBasePrice);
    }

    /**
     * Test update sold amount without given amount.
     *
     * @return null
     */
    public function testUpdateSoldAmountNotSet()
    {
        $oArticle = $this->_createArticle('_testArt');
        $blRet = $oArticle->updateSoldAmount(null);
        $this->assertNull($blRet);
    }

    /**
     * Test update sold amount.
     *
     * @return null
     */
    public function testUpdateSoldAmount()
    {
        $oArticle = $this->_createArticle('_testArt');

        $oDb = oxDb::getDB();
        $oDb->execute("update oxarticles set oxtimestamp = '2005-03-24 14:33:53' where oxid = '_testArt'");
        $sTimeStamp = $oDb->getOne("select oxtimestamp from oxarticles where oxid = '_testArt'");

        $rs = $oArticle->updateSoldAmount(1);

        $this->assertTrue($rs->EOF);
        $this->assertEquals(1, $oDb->getOne("select oxsoldamount from oxarticles where oxid = '_testArt'"));
        $this->assertNotEquals($sTimeStamp, $oDb->getOne("select oxtimestamp from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test update sold amount for variant.
     *
     * @return null
     */
    public function testUpdateSoldAmountVariant()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->updateSoldAmount(2);
        $this->assertEquals(0, oxDb::getDB()->getOne("select oxsoldamount from oxarticles where oxid = '_testVar'"));
        $this->assertEquals(2, oxDb::getDB()->getOne("select oxsoldamount from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test disable reminder.
     *
     * @return null
     */
    public function testDisableReminder()
    {
        $oArticle = $this->_createArticle('_testArt');
        $rs = $oArticle->disableReminder(1);
        $this->assertTrue($rs->EOF);
        $this->assertEquals(2, oxDb::getDB()->getOne("select oxremindactive from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test set article long description.
     *
     * @return null
     */
    public function testSetArticleLongDesc()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->setArticleLongDesc("LongDesc");
        $oArticle->save();
        $this->assertEquals("LongDesc", oxDb::getDB()->getOne("select oxlongdesc from oxartextends where oxid = '_testArt'"));
    }

    /**
     * Test save article.
     *
     * @return null
     */
    public function testSave()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxtitle = new oxField("newTitle", oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals("newTitle", oxDb::getDB()->getOne("select oxtitle from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test save updates timestamp.
     *
     * FS#1958
     *
     * @return null
     */
    public function testSaveAndUpdateTimeStamp()
    {
        $oArticle = $this->_createArticle('_testArt');
        oxDb::getDB()->execute("update oxarticles set oxtimestamp='2005-06-06 10:10:10' where oxid = '_testArt'");
        $oArticle->oxarticles__oxtitle = new oxField("newTitle", oxField::T_RAW);
        $oArticle->save();
        $this->assertNotEquals('2005-06-06 10:10:10', oxDb::getDB()->getOne("select oxtimestamp from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test get picture galery.
     *
     * @return null
     */
    public function testGetPictureGallery1()
    {
        $sArtID = "531f91d4ab8bfb24c4d04e473d246d0b";

        $sRawPath = $this->getConfig()->getPictureUrl(null);
        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArtID);

        $aPicGallery = $oArticle->getPictureGallery();

        $sActPic = $sRawPath . 'generated/product/1/380_340_75/' . preg_replace('#^1/#', '', $oArticle->oxarticles__oxpic1->value);
        $this->assertEquals($sActPic, $aPicGallery['ActPic']);
        $aPicGallery = $oArticle->getPictureGallery();

        $this->setRequestParameter('actpicid', 2);
        $aPicGallery = $oArticle->getPictureGallery();
        $this->assertEquals(2, $aPicGallery['ActPicID']);
    }

    /**
     * Test onChange event does nothing for new article.
     *
     * @return null
     */
    public function testOnChangeNewArt()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertNull($oArticle->onChange());
    }

    /**
     * Test onChange event updates stock.
     *
     * @return null
     */
    public function testOnChangeUpdateStock()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oVariant->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->save();
        $oArticle->UNITonChangeUpdateStock('_testArt');
        $this->assertEquals(2, oxDb::getDB()->getOne("select oxvarstock from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test onChange event updates stock and resets related counters.
     *
     * @return null
     */
    public function testOnChangeUpdateStockResetCounts()
    {
        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oVariant->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxvendorid = new oxField('oxvendorid');
        $oVariant->oxarticles__oxmanufacturerid = new oxField('oxmanufacturerid');
        $oVariant->save();
        $oArticle = $this->getMock('oxarticle', array('_onChangeResetCounts'));
        $oArticle->expects($this->any())->method('_onChangeResetCounts');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->UNITonChangeUpdateStock('_testArt');
        $this->assertEquals(2, oxDb::getDB()->getOne("select oxvarstock from oxarticles where oxid = '_testArt'"));
        $this->assertEquals(1, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test onChange event updates stock and resets related counters.
     *
     * @return null
     */
    public function testOnChangeUpdateStockResetCounts2()
    {

        $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oVariant->delete();
        $oArticle = $this->getMock('oxarticle', array('_onChangeResetCounts'));
        $oArticle->expects($this->any())->method('_onChangeResetCounts')->with($this->equalTo('_testArt'), $this->equalTo('oxvendorid'), $this->equalTo('oxmanufacturerid'));
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->oxarticles__oxstock = new oxField(1);
        $oArticle->oxarticles__oxvendorid = new oxField('oxvendorid');
        $oArticle->oxarticles__oxmanufacturerid = new oxField('oxmanufacturerid');
        $oArticle->save();
        $oArticle->oxarticles__oxstock = new oxField(1, oxField::T_RAW);

        $oArticle->UNITonChangeUpdateStock('_testArt');
        $this->assertEquals(0, oxDb::getDB()->getOne("select oxvarstock from oxarticles where oxid = '_testArt'"));
        $this->assertEquals(0, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'"));

    }

    /**
     * Test onChange event updates variant counts.
     *
     * FS#1819
     *
     * @return null
     */
    public function testOnChangeUpdateVarCount()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');

        $oVariant->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->save();
        $oArticle->UNITonChangeUpdateVarCount('_testArt');
        $this->assertEquals(1, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test onChange event updates stock and resets related counters.
     *
     * FS#1819
     *
     * @return null
     */
    public function testOnChangeUpdateVarCountIfNoVars()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oVariant->delete();
        $oArticle->UNITonChangeUpdateVarCount('_testArt');
        $this->assertEquals(0, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'"));
    }

    /**
     * Test if on change resets counts.
     *
     * @return null
     */
    public function testOnChangeResetCounts()
    {
        $sCat = "8a142c3e4143562a5.46426637";
        $sVend = "68342e2955d7401e6.18967838";
        $sMan = "fe07958b49de225bd1dbc7594fb9a6b0";
        oxDb::getDB()->execute("insert into oxobject2category (oxid, oxobjectid, oxcatnid) values ('test', '_testArt', '$sCat' )");

        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvendorid = new oxField($sVend, oxField::T_RAW);
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sMan, oxField::T_RAW);
        $oArticle->UNITonChangeResetCounts('_testArt', $sVend, $sMan);
    }

    /**
     * Test is visible for preview in admin.
     *
     * @return null
     */
    public function testIsVisiblePreview()
    {
        $oArticle = $this->_createArticle('_testArt');
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return 'testadmin_sid';}");
        $oArticle->oxarticles__oxactive = new oxField(0);

        $this->setRequestParameter('preview', md5('testadmin_sid' . 'oxdefaultadmin' . oxDb::getDb()->getOne('select oxpassword from oxuser where oxid = "oxdefaultadmin" ') . 'malladmin'));
        $this->assertTrue($oArticle->isVisible());
    }

    /**
     * Test is visible for inactive.
     *
     * @return null
     */
    public function testIsVisibleNotActive()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $this->assertFalse($oArticle->isVisible());
    }

    /**
     * Test is visible.
     *
     * @return null
     */
    public function testIsVisible()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $this->assertTrue($oArticle->isVisible());
    }

    /**
     * Test is visible when out of stock.
     *
     * @return null
     */
    public function testIsVisibleNoStock()
    {
        $oArticle = $this->_createArticle('_testArt');
        $this->getConfig()->setConfigParam('blUseStock', true);
        $oArticle->oxarticles__oxstock = new oxField(-1, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->assertFalse($oArticle->isVisible());
    }

    /**
     * Test is visible when out of stock.
     *
     * @return null
     */
    public function testIsVisibleNoStockButReserved()
    {
        $oArticle = $this->_createArticle('_testArt');

        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->getConfig()->setConfigParam('blUseStock', true);

        $oBR = $this->getMock('oxBasketReservation', array('getReservedAmount'));
        $oBR->expects($this->once())->method('getReservedAmount')->with($this->equalTo($oArticle->getId()))->will($this->returnValue(5));
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oA = $this->getMock('oxarticle', array('getSession'));
        $oA->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oA->load($oArticle->getId());

        $oA->oxarticles__oxstock = new oxField(-1, oxField::T_RAW);
        $oA->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->assertTrue($oA->isVisible());
    }

    /**
     * Test get custom VAT.
     */
    public function testGetCustomVAT()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $this->assertEquals(7, $oArticle->getCustomVAT());
    }

    /**
     * Test get custom VAT.
     * From PHP 7.0.6 the way isset works changed. In previous versions when value is not set __get was being called.
     */
    public function testGetCustomVATWithLazyLoadedVat()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $this->assertEquals(7, $oArticle->getCustomVAT());
    }

    /**
     * Test check for stock when stock checking is disabled.
     *
     * @return null
     */
    public function testCheckForStockNotActiveStock()
    {
        $this->getConfig()->setConfigParam('blUseStock', false);
        $this->assertTrue($this->_createArticle('_testArt')->checkForStock(4));
    }

    /**
     * Test check for stock when stock flag is 1.
     *
     * @return null
     */
    public function testCheckForStockWithStockFlag()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(1, oxField::T_RAW);
        $oArticle->save();
        $this->assertTrue($oArticle->checkForStock(4));
    }

    /**
     * Test check for stock when stock flag is 2.
     *
     * @return null
     */
    public function testCheckForStockZero()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();
        $this->assertFalse($oArticle->checkForStock(4));
        $blErr = oxRegistry::getSession()->getVariable('Errors');
        $this->assertTrue(isset($blErr));
    }

    /**
     * Test check for stock with uneven amounts.
     *
     * @return null
     */
    public function testCheckForStockUnevenAmounts()
    {
        $oArticle = $this->_createArticle('_testArt');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', false);
        $oArticle->oxarticles__oxstock = new oxField(4.5, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();
        $this->assertTrue($oArticle->checkForStock(4));
    }

    /**
     * Test check for stock .
     *
     * @return null
     */
    public function testCheckForStock()
    {
        $oArticle = $this->_createArticle('_testArt');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', false);
        $oArticle->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals(2, $oArticle->checkForStock(4));
    }

    /**
     * Test check for stock .
     *
     * @return null
     */
    public function testCheckForStockWithBasketReservation()
    {
        $oArticle = $this->_createArticle('_testArt');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', false);
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $oArticle->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();

        $oBR = $this->getMock('oxBasketReservation', array('getReservedAmount'));
        $oBR->expects($this->once())->method('getReservedAmount')->with($this->equalTo('_testArt'))->will($this->returnValue(5));
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oA = $this->getMock('oxarticle', array('getSession', '_assignStock'));
        $oA->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oA->expects($this->any())->method('_assignStock')->will($this->returnValue(null));
        $oA->load($oArticle->getId());

        $this->assertEquals(7, $oA->checkForStock(9));
    }

    /**
     * test stock reducing, when negative values are ok
     *
     * @return null
     */
    public function testReduceStockNegativeOk()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals(10, $oArticle->reduceStock(10, true));
        $this->assertEquals(-8, $oArticle->oxarticles__oxstock->value);

        $oA = oxNew('oxArticle');
        $oA->load($oArticle->getId());
        $this->assertEquals(-8, $oA->oxarticles__oxstock->value);
    }

    /**
     * test stock reducing, when negative values are NOT ok
     *
     * @return null
     */
    public function testReduceStockNegativeNotOk()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals(2, $oArticle->reduceStock(10, false));
        $this->assertEquals(0, $oArticle->oxarticles__oxstock->value);

        $oA = oxNew('oxArticle');
        $oA->load($oArticle->getId());
        $this->assertEquals(0, $oA->oxarticles__oxstock->value);
    }

    /**
     * Test get article long description.
     *
     * @return null
     */
    public function testGetLongDescription()
    {
        $this->_createArticle('_testArt', '_testVar');

        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc) values ( '_testArt', 'test &amp;')");
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $this->assertEquals('test &amp;', $oArticle->getLongDescription()->value);

        $oVariant = oxNew('oxArticle');
        $oVariant->load('_testVar');
        $this->assertEquals('test &amp;', $oVariant->getLongDescription()->value);

    }

    /**
     * Test get article long description in other language.
     *
     * @return null
     */
    public function testGetLongDescriptionInOtherLang()
    {
        $this->_createArticle('_testArt', '_testVar');

        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc_1) values ( '_testArt', 'lang 1 test &amp;')");

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->setLanguage(1);
        $oArticle->aaa = true;
        $this->assertEquals('lang 1 test &amp;', $oArticle->getLongDescription('_testArt')->value);
    }

    /**
     * Test get article long description and parse it in smarty.
     *
     * buglist#335
     *
     * @return null
     */
    public function testGetLongDescriptionWithSmartyTags()
    {
        $this->_createArticle('_testArt');

        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', true);
        $sDesc = 'aa[{* smarty comment *}]zz';

        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc) values ( '_testArt', '$sDesc')");

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $this->assertEquals('aazz', $oArticle->getLongDesc());
    }

    /**
     *  Test get cached article long description.
     *
     * @return null
     */
    public function testGetLongDescriptionCached()
    {
        $this->_createArticle('_testArt');

        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc) values ( '_testArt', 'aaaad')");

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $this->assertEquals('aaaad', $oArticle->getLongDescription()->value);
    }

    /**
     *  Test get variant long description from self in admin.
     *
     * @return null
     */
    public function testGetLongDescriptionVariantSelfInAdmin()
    {
        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc) values ( '_testArt', 'aaaad')");

        $oVariant = oxNew('oxarticle');
        $oVariant->setEnableMultilang(false);
        $oVariant->setAdminMode(true);
        $oVariant->load('_testVar2');
        $oVariant->setId('_testVar2');
        $oVariant->oxarticles__oxprice = new oxField(12.2, oxField::T_RAW);
        $oVariant->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oVariant->oxarticles__oxparentid = new oxField($this->oArticle->oxarticles__oxid->value, oxField::T_RAW);
        $oVariant->oxarticles__oxtitle = new oxField("test", oxField::T_RAW);

        $oVariant->save();
        $this->assertEquals('', $oVariant->getLongDescription()->value);
    }

    /**
     *  Test get variant long description from variant parent.
     *
     * @return null
     */
    public function testGetLongDescriptionVariantParent()
    {
        $this->_createArticle('_testArt', '_testVar');

        oxDb::getDB()->execute("delete from oxartextends where oxid = '_testVar'");
        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc) values ( '_testArt', '----d')");
        oxDb::getDb()->execute("insert into oxartextends (oxid, oxlongdesc) values ( '_testVar', '')");

        $oVariant = oxNew('oxArticle');
        $oVariant->load('_testVar');
        $this->assertEquals('----d', $oVariant->getLongDescription()->value);
    }

    /**
     * Test get attributes.
     *
     * @return null
     */
    public function testGetAttributes()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1672');
        $sSelect = "select oxattrid from oxobject2attribute where oxobjectid = '$sArtID'";
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = "select oxvalue from oxobject2attribute where oxattrid = '$sID' and oxobjectid = '$sArtID'";
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $aAttrList = $oArticle->getAttributes();
        $sAttribValue = $aAttrList[$sID]->oxobject2attribute__oxvalue->value;
        $this->assertEquals($sExpectedValue, $sAttribValue);
    }

    /**
     * Test get attributes in other language.
     *
     * @return null
     */
    public function testGetAttributesInOtherLang()
    {
        $this->markTestSkipped('Does not work for some reason after changing oxobjectid to correct value');
        $oArticle = $this->getMock('oxarticle', array('getLanguage'));
        $oArticle->expects($this->any())->method('getLanguage')->will($this->returnValue(1));
        $oArticle->load('1672');
        $sSelect = "select oxattrid from oxobject2attribute where oxobjectid = '1672'";
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = "select oxvalue_1 from oxobject2attribute where oxattrid = '$sID' and oxobjectid = '1672'";
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $aAttrList = $oArticle->getAttributes();
        $sAttribValue = $aAttrList[$sID]->oxobject2attribute__oxvalue->value;
        $this->assertEquals($sExpectedValue, $sAttribValue);
    }

    /**
     * Test get sorted attributes.
     *
     * @return null
     */
    public function testGetAttributesWithSort()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sSql = "insert into oxattribute (oxid, oxshopid, oxtitle, oxpos ) values ('test3', '{$sShopId}', 'test3', '3')";
        $this->addToDatabase($sSql, 'oxattribute');
        $sSql = "insert into oxattribute (oxid, oxshopid, oxtitle, oxpos ) values ('test1', '{$sShopId}', 'test1', '1')";
        $this->addToDatabase($sSql, 'oxattribute');
        $sSql = "insert into oxattribute (oxid, oxshopid, oxtitle, oxpos ) values ('test2', '{$sShopId}', 'test2', '2')";
        $this->addToDatabase($sSql, 'oxattribute');

        $sArtId = $oArticle->getId();
        $sSql = "insert into oxobject2attribute (oxid, oxobjectid, oxattrid, oxvalue ) values ('test3', '$sArtId', 'test3', '3')";
        $this->addToDatabase($sSql, 'oxobject2attribute');
        $sSql = "insert into oxobject2attribute (oxid, oxobjectid, oxattrid, oxvalue ) values ('test1', '$sArtId', 'test1', '1')";
        $this->addToDatabase($sSql, 'oxobject2attribute');
        $sSql = "insert into oxobject2attribute (oxid, oxobjectid, oxattrid, oxvalue ) values ('test2', '$sArtId', 'test2', '2')";
        $this->addToDatabase($sSql, 'oxobject2attribute');

        $aAttrList = $oArticle->getAttributes();
        $iCnt = 1;
        foreach ($aAttrList as $sId => $aAttr) {
            $this->assertEquals('test' . $iCnt, $sId);
            $this->assertEquals((string) $iCnt, $aAttr->oxattribute__oxvalue->value);
            $iCnt++;
        }
    }

    /**
     * Test get displayable in basket/order attributes, when all are not dispayable.
     *
     * @return null
     */
    public function testGetAttributesDisplayableInBasket()
    {
        $sSelect = "update oxattribute set oxdisplayinbasket = 1 where oxid = '8a142c3f0b9527634.96987022' ";
        $rs = oxDb::getDB()->execute($sSelect);
        $sSelect = "update oxattribute set oxdisplayinbasket = 1 where oxid = 'd8842e3b7c5e108c1.63072778' "; // texture
        $rs = oxDb::getDB()->execute($sSelect);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1672');
        $oArticle->oxarticles__oxparentid = new oxField('1351');
        $oArticle->save();

        $aAttrList = $oArticle->getAttributesDisplayableInBasket();
        $sAttribValue = $aAttrList['8a142c3f0c0baa3f4.54955953']->oxattribute__oxvalue->rawValue;
        $sAttribParentValue = $aAttrList['d8842e3b7d4e7acb1.34583879']->oxattribute__oxvalue->rawValue;
        $this->assertEquals('25 cm', $sAttribValue);
        $this->assertEquals('Granit', $sAttribParentValue);
    }

    /**
     * Test get displayable in basket/order attributes, when all are not dispayable.
     *
     * @return null
     */
    public function testGetAttributesDisplayableInBasketNoAttributes()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1672');
        $oArticle->oxarticles__oxparentid = new oxField('');
        $oArticle->save();

        $aAttrList = $oArticle->getAttributesDisplayableInBasket();
        $this->assertEquals(0, count($aAttrList));
    }

    /**
     * Test assign parent field values.
     *
     * @return null
     */
    public function testAssignParentFieldValues1()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $oArticle->oxarticles__oxfreeshipping = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxthumb = new oxField('test.jpg', oxField::T_RAW);
        $oArticle->save();

        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->oxarticles__oxthumb = new oxField('nopic.jpg', oxField::T_RAW);
        $oVariant->cleanup();
        $oVariant->UNITassignParentFieldValues();
        $this->assertEquals($oArticle->oxarticles__oxvat->value, $oVariant->oxarticles__oxvat->value);

        $this->assertEquals("test.jpg", $oVariant->oxarticles__oxthumb->value);
        $this->assertNotEquals($oArticle->oxarticles__oxid->value, $oVariant->oxarticles__oxid->value);
    }

    /**
     * Test assign parent field values (pictures).
     *
     * @return null
     */
    public function testAssignParentFieldValuesPics()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $this->getConfig()->setConfigParam('blAutoIcons', true);
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $oArticle->oxarticles__oxfreeshipping = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxicon = new oxField('parent_ico.jpg', oxField::T_RAW);
        $oArticle->save();
        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->oxarticles__oxicon = new oxField('variant_ico.jpg', oxField::T_RAW);
        $oVariant->cleanup();
        $oVariant->UNITassignParentFieldValues();
        $this->assertEquals($oArticle->oxarticles__oxvat->value, $oVariant->oxarticles__oxvat->value);
        $this->assertNotEquals($oArticle->oxarticles__oxicon->value, $oVariant->oxarticles__oxicon->value);
        $this->assertNotEquals($oArticle->oxarticles__oxid->value, $oVariant->oxarticles__oxid->value);
    }

    /**
     * Test assign parent field values (long desctiotions).
     *
     * @return null
     */
    public function testAssignParentFieldValuesLongdesc()
    {
        $this->_createArticle('_testArt', '_testVar');
        oxDb::getDB()->execute("delete from oxartextends where oxid = '_testVar'");

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->setArticleLongDesc('testLongDesc');
        $oArticle->save();

        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $this->assertEquals('testLongDesc', $oVariant->getLongDescription()->value);
    }

    /**
     * Test assign not buyable parent flag.
     *
     * @return null
     */
    public function testAssignNotBuyableParent()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oArticle->UNITassignNotBuyableParent();
        $this->assertTrue($oArticle->_blNotBuyableParent);
    }

    /**
     * Test assign not buyable parent flag if no variants are found.
     *
     * @return null
     */
    public function testAssignNotBuyableParentIfNoVariants()
    {
        $this->getConfig()->setConfigParam('blVariantParentBuyable', true);
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxvarcount = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxvarstock = new oxField(0, oxField::T_RAW);
        $oArticle->UNITassignNotBuyableParent();
        $this->assertFalse($oArticle->_blNotBuyableParent);
    }

    /**
     * Test assign stock if green.
     *
     * @return null
     */
    public function testAssignStockIfGreen()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstockflag = new oxField(4, oxField::T_RAW);
        $oArticle->UNITassignStock();
        $this->assertEquals(0, $oArticle->getStockStatus());
        $this->assertNull($this->_blNotBuyable);
    }

    /**
     * Test assign stock not allowing uneven amounts.
     *
     * @return null
     */
    public function testAssignStockDontAllowUnevenAmounts()
    {
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', false);
        $this->getConfig()->setConfigParam('blLoadVariants', false);
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(4.6, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(4, oxField::T_RAW);
        $oArticle->oxarticles__oxvarstock = new oxField(2, oxField::T_RAW);
        $oArticle->UNITassignStock();
        $this->assertEquals(0, $oArticle->getStockStatus());
        $this->assertEquals(4, $oArticle->oxarticles__oxstock->value);
        $this->assertTrue($oArticle->_blNotBuyable);
    }

    /**
     * Test assign stock if orange.
     *
     * @return null
     */
    public function testAssignStockIfOrange()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);
        $this->getConfig()->setConfigParam('sStockWarningLimit', 5);
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(6, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxvarstock = new oxField(4, oxField::T_RAW);
        $oArticle->UNITassignNotBuyableParent();
        $oArticle->UNITassignStock();
        $this->assertEquals(1, $oArticle->getStockStatus());
    }

    /**
     * Test assign stock if red.
     *
     * @return null
     */
    public function testAssignStockIfRed()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);
        $this->getConfig()->setConfigParam('sStockWarningLimit', 5);
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $this->_createArticle('_testArt', '_testVar');
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->UNITassignNotBuyableParent();
        $oArticle->UNITassignStock();
        $this->assertEquals(-1, $oArticle->getStockStatus());
        $this->assertTrue($oArticle->_blNotBuyable);
        $this->assertTrue($oArticle->_blNotBuyableParent);
    }

    /**
     * Test assign stock if red.
     *
     * @return null
     */
    public function testAssignStockWhenStockEmptyButReserved()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->getConfig()->setConfigParam('blUseStock', true);
        $this->getConfig()->setConfigParam('sStockWarningLimit', 5);
        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);

        $this->_createArticle('_testArt', '_testVar');

        $oBR = $this->getMock('oxBasketReservation', array('getReservedAmount'));
        $oBR->expects($this->once())->method('getReservedAmount')->with($this->equalTo('_testArt'))->will($this->returnValue(5));
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oA = $this->getMock('oxarticle', array('getSession'));
        $oA->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oA->load('_testArt');
        $oA->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oA->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oA->UNITassignNotBuyableParent();
        $oA->UNITassignStock();
        $this->assertEquals(-1, $oA->getStockStatus());
        $this->assertFalse($oA->_blNotBuyable);
        $this->assertTrue($oA->_blNotBuyableParent);
    }


    /**
     * Test assign dyn (picture) directory.
     *
     * @return null
     */
    public function testAssignDynImageDir()
    {
        $myConfig = $this->getConfig();
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxshopid = new oxField(1, oxField::T_RAW);
        $oArticle->UNITassignDynImageDir();
        $this->assertEquals($myConfig->getPictureUrl(null, false, $myConfig->isSsl(), null, 1), $oArticle->getDynImageDir());
        $this->assertEquals($myConfig->getPictureDir(false), $oArticle->dabsimagedir);
        $this->assertEquals($myConfig->getPictureUrl(null, false, false, null, 1), $oArticle->nossl_dimagedir);
        $this->assertEquals($myConfig->getPictureUrl(null, false, true, null), $oArticle->ssl_dimagedir);
    }

    /**
     * Test picture lazy loading.
     *
     * @return null
     */
    public function testLazyLoadPictures()
    {
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load("09646538b54bac72b4ccb92fb5e3649f");
        $oArticle->zz = true;

        $this->assertFalse(isset($oArticle->oxarticles__oxpic1));
        $this->assertFalse(isset($oArticle->oxarticles__oxzoom1));

        //first time access
        $sPic = $oArticle->oxarticles__oxpic1->value;
        $sZoomPic = $oArticle->oxarticles__oxzoom1->value;

        $this->assertTrue(isset($oArticle->oxarticles__oxpic1));
        $this->assertEquals("front_z1.jpg", $oArticle->oxarticles__oxpic1->value);
    }

    /**
     * Test thumbnail lazy loading.
     *
     * @return null
     */
    public function testLazyLoadPictureThumb()
    {
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load("2000");

        $this->assertFalse(isset($oArticle->oxarticles__oxthumb));

        //first time access
        $sPic = $oArticle->oxarticles__oxthumb->value;

        $this->assertTrue(isset($oArticle->oxarticles__oxthumb));
        $this->assertEquals("2000_th.jpg", $oArticle->oxarticles__oxthumb->value);
    }

    /**
     *  Test icon lazy loading.
     *
     * @return null
     */
    public function testLazyLoadPictureIcon()
    {
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load("2000");

        $this->assertFalse(isset($oArticle->oxarticles__oxicon));

        //first time access
        $sPic = $oArticle->oxarticles__oxicon->value;

        $this->assertTrue(isset($oArticle->oxarticles__oxicon));
        $this->assertEquals("2000_ico.jpg", $oArticle->oxarticles__oxicon->value);
    }

    /**
     * Test is buyable getter.
     *
     * @return null
     */
    public function testIsBuyablePlain()
    {
        $oArticle = $this->getProxyClass('oxArticleHelper');
        $oArticle->setNonPublicVar("_blNotBuyable", false);
        $this->assertTrue($oArticle->isBuyable());
        $oArticle->setNonPublicVar("_blNotBuyable", true);
        $this->assertFalse($oArticle->isBuyable());
    }

    /**
     * Test is buyable with variants.
     *
     * @return null
     */
    public function testIsBuyableWithVariants1()
    {
        $sParentArticleId = 2077;
        if ($this->getConfig()->getEdition() === 'EE') {
            $sParentArticleId = 2363;
        }

        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load($sParentArticleId);
        $this->assertFalse($oArticle->isBuyable());
    }

    /**
     * Test is buyable with variants.
     *
     * @return null
     */
    public function testIsBuyableWithVariants2()
    {
        $sParentArticleId = 2077;
        if ($this->getConfig()->getEdition() === 'EE') {
            $sParentArticleId = 2363;
        }

        $this->getConfig()->setConfigParam('blVariantParentBuyable', true);
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load($sParentArticleId);
        $this->assertTrue($oArticle->isBuyable());
    }

    /**
     * Test is buyable when out of stock.
     *
     * @return null
     */
    public function testIsBuyableOutOfStock()
    {
        $this->_createArticle('_testArt', '_testVar');

        $this->getConfig()->setConfigParam('blUseStock', true);
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(3, oxField::T_RAW);
        $oArticle->save();
        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('_testArt');
        $this->assertFalse($oArticle->isBuyable());
    }


    /**
     * Testing standard link getter
     *
     * @return null
     */
    public function testGetStdLinkshoudlReturnDefaultLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $this->setRequestParameter('pgNr', 10);
        $this->setRequestParameter('cnid', 'yyy');
        $this->setRequestParameter('mnid', 'mmm');
        $this->setRequestParameter('listtype', 'search');

        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=details&amp;anid=xxx&amp;cnid=yyy&amp;pgNr=10&amp;mnid=mmm&amp;listtype=search';

        $oArticle = $this->getMock('oxarticle', array('getSession'));
        $oArticle->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oArticle->setId('xxx');

        $this->assertEquals($sUrl, $oArticle->getStdLink(0, array('cnid' => 'yyy', 'pgNr' => 10, 'mnid' => 'mmm', 'listtype' => 'search')));
    }

    /**
     * Testing link getter
     *
     * @return null
     */
    public function testGetLink()
    {
        $this->setRequestParameter('pgNr', 10);
        $this->setRequestParameter('cnid', 'yyy');

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=details&amp;anid=xxx', $oArticle->getLink());
    }

    /**
     * Testing link getter in german.
     *
     * @return null
     */
    public function testGetLinkSeoDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oArticle = oxNew('oxArticle');

        if ($this->getConfig()->getEdition() === 'EE') {
            $oArticle->loadInLang(0, '1889');
            $sExp = "Spiele/Brettspiele/Bierspiel-OANS-ZWOA-GSUFFA.html";
        } else {
            $oArticle->loadInLang(0, '1126');
            $sExp = "Geschenke/Bar-Equipment/Bar-Set-ABSINTH.html";
        }

        $this->assertEquals($this->getConfig()->getShopUrl() . $sExp, $oArticle->getLink());
    }

    /**
     * Testing link getter in english.
     *
     * @return null
     */
    public function testGetLinkSeoEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->loadInLang(1, '1951');

        $sExp = "en/Gifts/Living/Clocks/Wall-Clock-BIKINI-GIRL.html";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sExp = "en/Living/Clocks/Wall-Clock-BIKINI-GIRL.html";
        }

        $this->assertEquals($this->getConfig()->getShopUrl() . $sExp, $oArticle->getLink());
    }

    /**
     * Test oxnid is never cached as field.
     *
     * @return null
     */
    public function testOxnidIsNeverCachedAsField()
    {
        $oArticle = oxNew('oxArticle');
        $this->cleanTmpDir();
        $oArticle->load(1126);
        //trying to access the field
        $sTestValue = $oArticle->oxarticles__oxnid->value;
        try {
            $oArticle->load(1126);
        } catch (Exception $e) {
            $this->fail("oxnid is registered");
        }
    }

    /**
     * Test formated price getter.
     *
     * @return null
     */
    public function testFPriceGetter()
    {
        $oArticle = oxNew('oxArticle');
        $oPrice = oxNew('oxPrice');
        $oArticle->setPrice($oPrice);

        $this->assertEquals('0,00', $oArticle->getFPrice());

        $oPrice->setPrice(10);
        $this->assertEquals("10,00", $oArticle->getFPrice());
    }

    /**
     * Test if multilingual field.
     *
     * @return null
     */
    public function testIsMultilingualField()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertTrue($oArticle->isMultilingualField('oxlongdesc'));
        $this->assertTrue($oArticle->isMultilingualField('oxtitle'));
        $this->assertFalse($oArticle->isMultilingualField('oxprice'));
        $this->assertFalse($oArticle->isMultilingualField('nonexistant'));

        $this->cleanTmpDir();
        //same only making sure is not cached
        $this->assertTrue($oArticle->isMultilingualField('oxlongdesc'));
        $this->assertTrue($oArticle->isMultilingualField('oxtitle'));
        $this->assertFalse($oArticle->isMultilingualField('oxprice'));
        $this->assertFalse($oArticle->isMultilingualField('nonexistant'));
    }

    /**
     * Test load images after save.
     *
     * @return null
     */
    public function testLoadImagesAfterSave()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1651');
        $aPicGallery = $oArticle->getPictureGallery();
        $actpic = $aPicGallery['ActPic'];
        $oArticle->save();
        $aPicGallery = $oArticle->getPictureGallery();
        $this->assertEquals($actpic, $aPicGallery['ActPic']);
    }

    /**
     * Test get select list.
     *
     * @return null
     */
    public function testGetSelectList()
    {
        $oDb = oxDb::getDB();
        $myConfig = $this->getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        $sShopId = $myConfig->getBaseShopId();
        $sVal = 'three!P!-5,99__threeValue@@';

        $sQ = 'insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ("_testoxsellist", "' . $sShopId . '", "_testoxsellist", "_testoxsellist", "' . $sVal . '")';
        $this->addToDatabase($sQ, 'oxselectlist');

        $sQ = 'insert into oxobject2selectlist (oxid, oxobjectid, oxselnid, oxsort) values ("_testoxsellist", "1651", "_testoxsellist", 1) ';
        $this->addToDatabase($sQ, 'oxobject2selectlist');

        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $this->getConfig()->setConfigParam('bl_perfUseSelectlistPrice', true);

        $oObject = new stdClass();
        $oObject->price = '-5.99';
        $oObject->fprice = '-5,99';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'three -5,99 ' . $oCurrency->sign;
        $oObject->value = 'threeValue';
        $aSelList[] = $oObject;
        $aShouldBe[0] = $aSelList;
        $aShouldBe[0]['name'] = '_testoxsellist';

        $oArticle = oxNew('oxArticleHelper');
        $oArticle->load('1651');
        $oArticle->cleanup();
        $this->assertEquals($aShouldBe, $oArticle->getSelectLists());
    }

    /**
     * Test get media url's.
     *
     * @return null
     */
    public function testGetMediaUrls()
    {
        $this->cleanUpTable('oxmediaurls');
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc) values ('_test1', '1126', '/test.jpg', 'test1')";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc) values ('_test2', '1126', 'http://www.youtube.com/watch?v=ZN239G6aJZo', 'test2')";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc) values ('_test3', '1126', 'test.jpg', 'test3')";
        oxDb::getDb()->execute($sQ);

        $oArt = oxNew('oxArticle');
        $oArt->load('1126');
        $oMediaUrls = $oArt->getMediaUrls();

        $this->assertEquals(3, count($oMediaUrls));
        $this->assertTrue(isset($oMediaUrls['_test1']));
        $this->assertTrue(isset($oMediaUrls['_test2']));
        $this->assertTrue(isset($oMediaUrls['_test3']));
        $this->assertEquals('test2', $oMediaUrls['_test2']->oxmediaurls__oxdesc->value);
        $this->assertEquals("<a href=\"test.jpg\" target=\"_blank\">test3</a>", $oMediaUrls['_test3']->getHtml());

        $this->cleanUpTable('oxmediaurls');
    }

    /**
     * Test if parent buyable is checked for varselect.
     *
     * FS#1748
     *
     * @return null
     */
    public function testIfParentBuyableCheckVarselect()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oArticle->oxarticles__oxvarcount = new oxField(1, oxField::T_RAW);
        $oVariant->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oVariant->save();
        $this->getConfig()->setConfigParam('blVariantParentBuyable', true);
        $oArticle->save();
        $sOxid = oxDb::getDb()->getOne("select oxvarselect from oxarticles where oxid = '{$oArticle->getId()}'");
        $this->assertEquals('', $sOxid);
    }

    /**
     * Test get parent article.
     *
     * @return null
     */
    public function testGetParentArticle()
    {
        oxTestModules::addFunction('oxarticle', 'clearParentCache', '{self::$_aLoadedParents = array();}');
        $oA = oxNew('oxArticle');
        $oA->clearParentCache();

        $oArticle = $this->_createArticle('_testArt');
        $oVariant = $this->_createVariant('_testVar', '_testArt');
        $oParent1 = $oVariant->getParentArticle();
        $oParent2 = $oVariant->getParentArticle();
        $this->assertEquals('_testArt', $oParent1->getId());
        $this->assertSame($oParent1, $oParent2);
        $this->assertNull($oArticle->getParentArticle());
    }

    /**
     * Test assign parent field value.
     *
     * @return null
     */
    public function testAssignParentFieldValue()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxvat = new oxField(7, oxField::T_RAW);
        $oArticle->oxarticles__oxfreeshipping = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxthumb = new oxField('test.jpg', oxField::T_RAW);
        $oArticle->save();
        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->oxarticles__oxthumb = new oxField('nopic.jpg', oxField::T_RAW);
        $oVariant->cleanup();
        $oVariant->UNITassignParentFieldValue('oxarticles__oxthumb');
        $this->assertEquals("test.jpg", $oVariant->oxarticles__oxthumb->value);
        $this->assertNotEquals($oArticle->oxarticles__oxid->value, $oVariant->oxarticles__oxid->value);
    }

    /**
     * Test assign parent field values to inherit Quantity and Unit.
     *
     * @return null
     */
    public function testAssignParentFieldValues_QuantityUnitInherit()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxunitquantity = new oxField('3', oxField::T_TEXT);
        $oArticle->oxarticles__oxunitname = new oxField('_UNIT_KG', oxField::T_TEXT);
        $oArticle->save();

        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->cleanup();
        $oVariant->UNITassignParentFieldValues();
        $this->assertEquals('3', $oVariant->oxarticles__oxunitquantity->value);
        $this->assertEquals('_UNIT_KG', $oVariant->oxarticles__oxunitname->value);
    }

    /**
     * Test assign parent field values to not inherit Quantity and Unit.
     *
     * @return null
     */
    public function testAssignParentFieldValues_QuantityUnitDontInherit()
    {
        $oArticle = $this->_createArticle('_testArt', '_testVar');
        $oArticle->oxarticles__oxunitquantity = new oxField('3', oxField::T_TEXT);
        $oArticle->oxarticles__oxunitname = new oxField('_UNIT_KG', oxField::T_TEXT);
        $oArticle->save();

        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->oxarticles__oxunitquantity = new oxField('7', oxField::T_TEXT);
        $oVariant->oxarticles__oxunitname = new oxField('_UNIT_L', oxField::T_TEXT);
        $oVariant->cleanup();
        $oVariant->UNITassignParentFieldValues();
        $this->assertEquals('7', $oVariant->oxarticles__oxunitquantity->value);
        $this->assertEquals('_UNIT_L', $oVariant->oxarticles__oxunitname->value);
    }

    /**
     * Test assign parent field value with zero price.
     *
     * @return null
     */
    public function testAssignParentFieldValueIfPriceIsZero()
    {
        $this->_createArticle('_testArt', '_testVar');
        $oVariant = oxNew('oxArticleHelper');
        $oVariant->load('_testVar');
        $oVariant->oxarticles__oxprice = new oxField("0", oxField::T_RAW);
        $oVariant->UNITassignParentFieldValue('oxarticles__oxprice');
        $this->assertEquals(15.5, $oVariant->oxarticles__oxprice->value);
    }

    /**
     * Test assign parent field value - when variant has his own thumbnail, icon
     * and zoom picture.
     *
     * @return null
     */
    public function testAssignParentFieldValue_variantHasOwnImages()
    {
        $oParentArticle = oxNew('oxArticle');
        $oParentArticle->oxarticles__oxicon = new oxField('parent_ico.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxthumb = new oxField('parent_thumb.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxzoom1 = new oxField('parent_zoom1.jpg', oxField::T_RAW);

        $oVarArticle = $this->getMock('oxarticle', array('getParentArticle', '_hasMasterImage'));
        $oVarArticle->expects($this->any())->method('getParentArticle')->will($this->returnValue($oParentArticle));
        $oVarArticle->expects($this->any())->method('_hasMasterImage')->will($this->returnValue(true));

        $oVarArticle->oxarticles__oxicon = new oxField('var_ico.jpg', oxField::T_RAW);
        $oVarArticle->oxarticles__oxthumb = new oxField('var_thumb.jpg', oxField::T_RAW);
        $oVarArticle->oxarticles__oxzoom1 = new oxField('var_zoom1.jpg', oxField::T_RAW);

        $oVarArticle->UNITassignParentFieldValue("oxicon");
        $this->assertEquals("var_ico.jpg", $oVarArticle->oxarticles__oxicon->value);

        $oVarArticle->UNITassignParentFieldValue("oxthumb");
        $this->assertEquals("var_thumb.jpg", $oVarArticle->oxarticles__oxthumb->value);

        $oVarArticle->UNITassignParentFieldValue("oxzoom1");
        $this->assertEquals("var_zoom1.jpg", $oVarArticle->oxarticles__oxzoom1->value);
    }

    /**
     * Test assign parent field value - when variant has his own thumbnail, icon
     * and zoom picture.
     *
     * #5165 defines that no parent image values should be loaded in case variant has at least one picture
     *
     * @return null
     */
    public function testAssignParentFieldValue_variantHasOwnMasterImage()
    {
        $oParentArticle = oxNew('oxArticle');
        $oParentArticle->oxarticles__oxid = new oxField('parentArt', oxField::T_RAW);
        $oParentArticle->oxarticles__oxicon = new oxField('parent_icon.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxthumb = new oxField('parent_thumb.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxpic1 = new oxField('parent_pic1.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxpic2 = new oxField('parent_pic2.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxtitle = new oxField('testArt', oxField::T_RAW);

        $oVarArticle = $this->getMock('oxarticle', array('getParentArticle', '_hasMasterImage'));
        $oVarArticle->init(null, true);
        $oVarArticle->expects($this->any())->method('getParentArticle')->will($this->returnValue($oParentArticle));
        $oVarArticle->expects($this->any())->method('_hasMasterImage')->will($this->returnValue(true));

        $oVarArticle->oxarticles__oxparentid = new oxField('parentArt', oxField::T_RAW);
        $oVarArticle->oxarticles__oxpic1 = new oxField('variant_pic1.jpg', oxField::T_RAW);
        $oVarArticle->oxarticles__oxicon = new oxField('variant_icon.jpg', oxField::T_RAW);

        $oVarArticle->UNITassignParentFieldValues();

        //check if some values are really assigned from parent and our test makes sense
        $this->assertEquals("testArt", $oVarArticle->oxarticles__oxtitle->value);

        //specific variant picture value is taken
        $this->assertEquals("variant_icon.jpg", $oVarArticle->oxarticles__oxicon->value);
        $this->assertEquals("variant_pic1.jpg", $oVarArticle->oxarticles__oxpic1->value);

        //parent values are not loaded
        $this->assertEquals("", $oVarArticle->oxarticles__oxthumb->value);
        $this->assertEquals("", $oVarArticle->oxarticles__oxpic2->value);
    }

    /**
     * Test assign parent field value - when variant does not his own thumbnail, icon
     * and zoom picture.
     *
     * @return null
     */
    public function testAssignParentFieldValue_variantDoesNotHasOwnImages()
    {
        $oParentArticle = oxNew('oxArticle');
        $oParentArticle->oxarticles__oxicon = new oxField('parent_ico.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxthumb = new oxField('parent_thumb.jpg', oxField::T_RAW);
        $oParentArticle->oxarticles__oxzoom1 = new oxField('parent_zoom1.jpg', oxField::T_RAW);

        $oVarArticle = $this->getMock('oxarticle', array('getParentArticle', '_hasMasterImage', '_assignZoomPictureValues'));
        $oVarArticle->expects($this->any())->method('getParentArticle')->will($this->returnValue($oParentArticle));
        $oVarArticle->expects($this->any())->method('_hasMasterImage')->will($this->returnValue(false));
        $oVarArticle->expects($this->any())->method('_assignZoomPictureValues')->will($this->returnValue(new oxField()));

        $oVarArticle->UNITassignParentFieldValue("oxicon");
        $this->assertEquals("parent_ico.jpg", $oVarArticle->oxarticles__oxicon->value);

        $oVarArticle->UNITassignParentFieldValue("oxthumb");
        $this->assertEquals("parent_thumb.jpg", $oVarArticle->oxarticles__oxthumb->value);

        $oVarArticle->rrr = 1;
        $oVarArticle->UNITassignParentFieldValue("oxzoom1");

        $this->assertEquals("parent_zoom1.jpg", $oVarArticle->oxarticles__oxzoom1->value);
    }

    /**
     * Test assign parent field value with non zero price.
     *
     * @return null
     */
    public function testAssignParentFieldValueIfPriceIsNotZero()
    {
        $oArticle2 = oxNew('oxArticleHelper');
        $oArticle2->load('_testVar');
        $oArticle2->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle2->UNITassignParentFieldValue('oxarticles__oxprice');
        $this->assertEquals(10, $oArticle2->oxarticles__oxprice->value);
    }

    /**
     * Test assign parent field value with zero price as string.
     *
     * @return null
     */
    public function testAssignParentFieldValueStringZeroValue()
    {
        $oArticle2 = oxNew('oxArticleHelper');
        $oArticle2->load('_testVar');
        $oArticle2->oxarticles__oxtitle = new oxField("0", oxField::T_RAW);
        $oArticle2->UNITassignParentFieldValue('oxarticles__oxtitle');
        $this->assertEquals("0", $oArticle2->oxarticles__oxtitle->value);
    }

    /**
     * Test get link with language.
     *
     * @return null
     */
    public function testGetLinkWithLanguage()
    {
        $this->setRequestParameter('pgNr', 10);
        $this->setRequestParameter('cnid', 'yyy');

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=details&amp;anid=xxx&amp;lang=2', $oArticle->getLink(2));

        // next
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oArticle = oxNew('oxArticle');

        if ($this->getConfig()->getEdition() === 'EE') {
            $oArticle->loadInLang(1, '1889');
            $sExp = "Spiele/Brettspiele/Bierspiel-OANS-ZWOA-GSUFFA.html";
        } else {
            $oArticle->loadInLang(1, '1126');
            $sExp = "Geschenke/Bar-Equipment/Bar-Set-ABSINTH.html";
        }

        $this->assertEquals($this->getConfig()->getShopUrl() . $sExp, $oArticle->getLink(0));

        // next
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->loadInLang(0, '1951');

        $sExp = "en/Gifts/Living/Clocks/Wall-Clock-BIKINI-GIRL.html";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sExp = "en/Living/Clocks/Wall-Clock-BIKINI-GIRL.html";
        }

        $this->assertEquals($this->getConfig()->getShopUrl() . $sExp, $oArticle->getLink(1));

    }

    /**
     * Test get standard links.
     *
     * Should return default link with language parameter.
     *
     * @return null
     */
    public function testGetStdLinkshoudlReturnDefaultLinkWithLangParam()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $this->setRequestParameter('pgNr', 10);
        $this->setRequestParameter('cnid', 'yyy');
        $this->setRequestParameter('mnid', 'mmm');
        $this->setRequestParameter('listtype', 'search');

        $sUrl1 = $this->getConfig()->getShopHomeURL() . 'cl=details&amp;anid=xxx&amp;cnid=yyy&amp;pgNr=10&amp;mnid=mmm&amp;listtype=search&amp;lang=1';
        $sUrl2 = $this->getConfig()->getShopHomeURL() . 'cl=details&amp;anid=xxx&amp;cnid=yyy&amp;pgNr=10&amp;mnid=mmm&amp;listtype=search';

        $oArticle = $this->getMock('oxarticle', array('getSession'));
        $oArticle->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oArticle->setId('xxx');

        $this->assertEquals($sUrl1, $oArticle->getStdLink(1, array("cnid" => "yyy", "pgNr" => 10, "mnid" => "mmm", "listtype" => "search")));
        $this->assertEquals($sUrl2, $oArticle->getStdLink(0, array("cnid" => "yyy", "pgNr" => 10, "mnid" => "mmm", "listtype" => "search")));
    }

    /**
     * Test get dyn (picture) image dir.
     *
     * @return null
     */
    public function testGetDynImageDir()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxshopid = new oxField(1);
        $oArticle->UNITassignDynImageDir();
        $this->assertEquals($this->getConfig()->getPictureUrl(null, false, $this->getConfig()->isSsl(), null, 1), $oArticle->getDynImageDir());
    }

    /**
     * Test get display select list.
     *
     * @return null
     */
    public function testGetDispSelList()
    {
        $oArticle = $this->getMock('oxarticle', array('getSelectLists'));
        $oArticle->expects($this->once())->method('getSelectLists')->will($this->returnValue('aaa'));
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', true);
        $this->assertEquals('aaa', $oArticle->getDispSelList());
    }

    /**
     * Test set display select list.
     *
     * @return null
     */
    public function testSetGetDispSelList()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setSelectlist('aaa');
        $this->assertEquals('aaa', $oArticle->getDispSelList());
    }

    /**
     * Test get more details link.
     *
     * @return null
     */
    public function testGetMoreDetailLink()
    {
        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setNonPublicVar("_sMoreDetailLink", "testDetailsLink");
        $this->assertEquals("testDetailsLink", $oArticle->getMoreDetailLink());
    }

    /**
     * Test get more details link with all request parameters.
     *
     * @return null
     */
    public function testGetMoreDetailLinkTestingIfAllRequestParamsAreSet()
    {
        oxTestModules::addFunction('oxUtilsUrl', 'processUrl($url, $blFinalUrl = true, $aParams = NULL, $iLang = NULL)', '{return "PROC".$url.(int)$final."CORP";}');

        $this->setRequestParameter('cnid', 'yyy');
        $oArticle = $this->getMock("oxarticle", array('getId'));
        $oArticle->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $this->assertEquals('PROC' . $this->getConfig()->getShopUrl() . 'index.php' . '0CORPcl=moredetails&amp;cnid=yyy&amp;anid=xxx', $oArticle->getMoreDetailLink());
    }

    /**
     * test get to basket link.
     *
     * @return null
     */
    public function testGetToBasketLink()
    {
        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setNonPublicVar("_sToBasketLink", "testBasketLink");
        $this->assertEquals("testBasketLink", $oArticle->getToBasketLink());
    }

    /**
     * Test get to basket link with all request parameters.
     *
     * @return null
     */
    public function testGetToBasketLinkTestingIfAllRequestParamsAreSet()
    {
        $this->setRequestParameter('cnid', 'yyy');
        $this->setRequestParameter('cl', 'thankyou');
        $this->setRequestParameter('tpl', '/my/tpl/file.tpl');

        oxTestModules::addFunction('oxUtilsUrl', 'processUrl($url, $blFinalUrl = true, $aParams = NULL, $iLang = NULL)', '{return "PROC".$url.(int)$final."CORP";}');

        $oArticle = $this->getMock("oxarticle", array('getId'));
        $oArticle->expects($this->exactly(2))->method('getId')->will($this->returnValue('xxx'));

        $this->assertEquals('PROC' . $this->getConfig()->getShopUrl() . 'index.php' . '0CORPcl=basket&amp;cnid=yyy&amp;fnc=tobasket&amp;aid=xxx&amp;anid=xxx&amp;tpl=file.tpl', $oArticle->getToBasketLink());
    }

    /**
     * Test get to basket link for search engine.
     *
     * @return null
     */
    public function testGetToBasketLinkIsSearchEngine()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return 'seolink'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "isSearchEngine", "{return true;}");

        $oArticle = $this->getMock("oxarticle", array('getLink'));
        $oArticle->expects($this->once())->method('getLink')->will($this->returnValue('seolink'));

        $this->assertEquals('seolink', $oArticle->getToBasketLink());
    }

    /**
     * Test get stock status.
     *
     * @return null
     */
    public function testGetStockStatus()
    {
        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setNonPublicVar("_iStockStatus", "testBasketLink");
        $this->assertEquals("testBasketLink", $oArticle->getStockStatus());
    }

    /**
     * Test get delivery date.
     *
     * @return null
     */
    public function testGetDeliveryDate()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxdelivery = new oxField('2008-01-01', oxField::T_RAW);
        $oArticle->save();

        $sDelDate = '01.01.2008';
        if ($oArticle->getLanguage() == 1) {
            $sDelDate = '2008-01-01';
        }

        $this->assertEquals($sDelDate, $oArticle->getDeliveryDate());
    }

    /**
     * Test get delivery date when not set.
     *
     * @return null
     */
    public function testGetDeliveryDateIfNotSet()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxdelivery = new oxField('0000-00-00', oxField::T_RAW);
        $oArticle->save();
        $this->assertFalse($oArticle->getDeliveryDate());
    }

    /**
     * Test get formated old price when it is more than price.
     *
     * @return null
     */
    public function testGetFTPriceIfMore()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(15.5, oxField::T_RAW);
        $oArticle->oxarticles__oxtprice = new oxField(16.6, oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals('16,60', $oArticle->getFTPrice());
    }

    /**
     * Test get formated old price when it is same as price.
     *
     * @return null
     */
    public function testGetFTPriceIfEqual()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(15.5, oxField::T_RAW);
        $oArticle->oxarticles__oxtprice = new oxField(15.5, oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals('', $oArticle->getFTPrice());
    }

    /**
     * Test get formated old price when it is less than price.
     *
     * @return null
     */
    public function testGetFTPriceIfLess()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(15.5, oxField::T_RAW);
        $oArticle->oxarticles__oxtprice = new oxField(14.4, oxField::T_RAW);
        $oArticle->save();
        $this->assertEquals('', $oArticle->getFTPrice());
    }

    /**
     * Test get formated old price when not set.
     *
     * @return null
     */
    public function testGetFTPriceIfNotSet()
    {
        $this->assertNull($this->_createArticle('_testArt')->getFTPrice());
    }

    /**
     * Test get formated price.
     *
     * @return null
     */
    public function testGetFPrice()
    {
        $this->assertEquals('15,50', $this->_createArticle('_testArt')->getFPrice());
    }

    /**
     * Test resetting of remind status when reminder is sent and stock is higher than remindamount
     *
     * @return null
     */
    public function testResetRemindStatus()
    {
        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxremindactive = new oxField(2, oxField::T_RAW);
        $oArticle->oxarticles__oxremindamount = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(20, oxField::T_RAW);

        $oArticle->resetRemindStatus();

        $this->assertEquals(1, $oArticle->oxarticles__oxremindactive->value);
    }

    /**
     * Test get formated price when not set.
     *
     * @return null
     */
    public function testGetFPriceIfNotSet()
    {
        $oArticle = $this->getMock('oxarticle', array('getPrice'));
        $oArticle->expects($this->once())->method('getPrice')->will($this->returnValue(null));
        $this->assertNull($oArticle->getFPrice());
    }

    /**
     * Test is parent not buyable.
     *
     * @return null
     */
    public function testIsParentNotBuyable()
    {
        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setNonPublicVar("_blNotBuyableParent", true);
        $this->assertTrue($oArticle->isParentNotBuyable());
    }

    /**
     * Test is not buyable.
     *
     * @return null
     */
    public function testIsNotBuyable()
    {
        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setNonPublicVar("_blNotBuyable", true);
        $this->assertTrue($oArticle->isNotBuyable());
    }

    /**
     * Test get picture url.
     *
     * @return null
     */
    public function testGetPictureUrl()
    {
        $oPH = $this->getMock('oxPictureHandler', array('getPicUrl'));
        $oPH->expects($this->once())->method('getPicUrl')->with($this->equalTo('product/1/'), $this->equalTo('nopic.jpg'))->will($this->returnValue('testPic1Url'));

        oxTestModules::addModuleObject('oxPictureHandler', $oPH);

        $oArticle = oxNew('oxArticle');

        $this->assertEquals('testPic1Url', $oArticle->getPictureUrl(1));
    }

    /**
     * Test get picture url when new path is set up
     *
     * @return null
     */
    public function testGetPictureUrlNewPath()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("cabrinha_caliber_2011.jpg");

        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/product/1/380_340_75/cabrinha_caliber_2011.jpg";

        $this->assertEquals($sUrl, $oArticle->getPictureUrl(1));
    }

    /**
     * Test get picture url without image index.
     *
     * Check if method returns null when passed to method parameter is empty
     *
     * @return null
     */
    public function testGetPictureUrl_noIndex()
    {
        $oConfig = $this->getMock('oxConfig', array('getPictureUrl'));
        $oConfig->expects($this->never())->method('getPictureUrl');

        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setConfig($oConfig);

        $this->assertNull($oArticle->getPictureUrl(0));
    }

    /**
     * Test get picture icon url with new path setup
     *
     * @return null
     */
    public function testGetIconUrlNewPath()
    {
        $oArticle = $this->getMock('oxarticle', array('_getIconName', '_isFieldEmpty', '_assignPictureValues'));

        $oArticle->oxarticles__oxpic1 = new oxField("30-360-back_p1_z_f_th_665.jpg");

        $oArticle->expects($this->any())->method('_isFieldEmpty')->will($this->returnValue(false));
        $oArticle->expects($this->any())->method('_assignPictureValues')->will($this->returnValue(null));
        $oArticle->expects($this->never())->method('_getIconName');

        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/product/1/87_87_75/30-360-back_p1_z_f_th_665.jpg";

        $this->assertEquals($sUrl, $oArticle->getIconUrl(1));
    }

    /**
     * Test get thumbnail url when new path is set up
     *
     * @return null
     */
    public function testGetThumbnailUrlNewPath()
    {
        $oArticle = $this->getMock('oxarticle', array('_isFieldEmpty', '_assignPictureValues'));
        $oArticle->oxarticles__oxthumb = new oxField("detail1_z3_ico_th.jpg");
        $oArticle->expects($this->any())->method('_isFieldEmpty')->will($this->returnValue(false));
        $oArticle->expects($this->any())->method('_assignPictureValues')->will($this->returnValue(null));

        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/product/thumb/185_150_75/detail1_z3_ico_th.jpg";

        $this->assertEquals($sUrl, $oArticle->getThumbnailUrl());
    }


    /**
     * Test get zoom picture url when new path is set up
     *
     * @return null
     */
    public function testGetZoomPictureUrlNewPath()
    {
        $oArticle = $this->getMock('oxarticle', array('_isFieldEmpty'));
        $oArticle->oxarticles__oxpic1 = new oxField("30-360-back_p1_z_f_th_665.jpg");
        $oArticle->expects($this->any())->method('_isFieldEmpty')->will($this->returnValue(false));

        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/product/1/665_665_75/30-360-back_p1_z_f_th_665.jpg";

        $this->assertEquals($sUrl, $oArticle->getZoomPictureUrl(1));
    }

    /**
     * Test get zoom picture url withount index specified.
     *
     * Check if method returns null when passed to method parameter is empty
     *
     * @return null
     */
    public function testGetZoomPictureUrl_noIndex()
    {
        $oConfig = $this->getMock('oxConfig', array('getPictureUrl'));
        $oConfig->expects($this->never())->method('getPictureUrl');

        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setConfig($oConfig);

        $this->assertNull($oArticle->getZoomPictureUrl());
    }

    /**
     * Test set/get article user.
     *
     * @return null
     */
    public function testSetGetArticleUser()
    {
        $oSubj = oxNew('oxArticle');
        $oSubj->setArticleUser('testUser');
        $this->assertEquals('testUser', $oSubj->getArticleUser());
    }

    /**
     * Test get global article user.
     *
     * @return null
     */
    public function testGetArticleUserGlobal()
    {
        $oSubj = oxNew('oxArticle');
        $oSubj->setUser('testUser');
        $this->assertEquals('testUser', $oSubj->getArticleUser());
    }

    /**
     * Test get non global article user.
     *
     * @return null
     */
    public function testGetArticleUserNonGlobal()
    {
        $oSubj = oxNew('oxArticle');
        $oSubj->setUser('testUser');
        $oSubj->setArticleUser('testLocalUser');
        $this->assertEquals('testLocalUser', $oSubj->getArticleUser());
    }

    /**
     * Test oxarticle::updateVariantsRemind()
     *
     * @return null
     */
    public function testUpdateVariantsRemind()
    {
        $oParent = oxNew('oxArticle');
        $oParent->setId("_testParent");
        $oParent->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oParent->oxarticles__oxactive = new oxField(1);
        $oParent->oxarticles__oxremindactive = new oxField(0);
        $oParent->oxarticles__oxvarcount = new oxField(1);
        $oParent->save();

        $oVariant = oxNew('oxArticle');
        $oVariant->setId("_testVariant");
        $oVariant->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oVariant->oxarticles__oxparentid = new oxField("_testParent");
        $oVariant->oxarticles__oxactive = new oxField(1);
        $oVariant->oxarticles__oxremindactive = new oxField(0);
        $oVariant->save();

        $oParent->oxarticles__oxremindactive = new oxField(1);
        $oParent->updateVariantsRemind();

        $oVariant->load('_testVariant');
        $this->assertEquals(1, $oVariant->oxarticles__oxremindactive->value);

        $oParent->oxarticles__oxremindactive = new oxField(0);
        $oParent->updateVariantsRemind();

        $oVariant->load('_testVariant');
        $this->assertEquals(0, $oVariant->oxarticles__oxremindactive->value);

        $oParent->delete();
        $oVariant->delete();
    }

    /**
     * Test is field empty positives.
     *
     * @return null
     */
    public function testIsFieldEmptyPositive()
    {
        //T2009-01-09
        //the tests are so trivial that I'll just do a buch of assert in one test
        $oSubj = $this->getProxyClass("oxarticle");

        $oSubj->oxanyfield = new stdClass();
        $oSubj->oxanyfield->value = "";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxanyfield", ""));

        $oSubj->oxanyfield = new stdClass();
        $oSubj->oxanyfield->value = "0000-00-00 00:00:00";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxanyfield"));

        $oSubj->oxanyfield = new stdClass();
        $oSubj->oxanyfield->value = "0000-00-00";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxanyfield"));

        $oSubj->oxarticles__oxanyfield = new stdClass();
        $oSubj->oxarticles__oxanyfield->value = null;
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxanyfield"));

        $oSubj->oxarticles__oxpic1 = new stdClass();
        $oSubj->oxarticles__oxpic1->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxpic1"));

        $oSubj->oxarticles__oxpic1 = new stdClass();
        $oSubj->oxarticles__oxpic1->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__OXPIC1"));

        $oSubj->oxarticles__oxpic2 = new stdClass();
        $oSubj->oxarticles__oxpic2->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxpic2"));

        $oSubj->oxarticles__oxpic12 = new stdClass();
        $oSubj->oxarticles__oxpic12->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxpic12"));

        $oSubj->oxarticles__oxthumb = new stdClass();
        $oSubj->oxarticles__oxthumb->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxthumb"));

        $oSubj->oxarticles__oxthumb = new stdClass();
        $oSubj->oxarticles__oxthumb->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("OXTHUMB"));

        $oSubj->oxarticles__oxicon = new stdClass();
        $oSubj->oxarticles__oxicon->value = "nopic_ico.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxicon"));

        $oSubj->oxarticles__oxicon = new stdClass();
        $oSubj->oxarticles__oxicon->value = "nopic_ico.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__OXICON"));

        $oSubj->oxarticles__oxzoom1 = new stdClass();
        $oSubj->oxarticles__oxzoom1->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxzoom1"));

        $oSubj->oxarticles__oxzoom2 = new stdClass();
        $oSubj->oxarticles__oxzoom2->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxzoom2"));

        $oSubj->oxarticles__oxzoom1 = new stdClass();
        $oSubj->oxarticles__oxzoom1->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("OXARTICLES__OXZOOM1"));

        $oSubj->oxarticles__oxunitquantity = new stdClass();
        $oSubj->oxarticles__oxunitquantity->value = 0;
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxunitquantity"));

        $oSubj->oxarticles__oxunitquantity = new stdClass();
        $oSubj->oxarticles__oxunitquantity->value = "0";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxunitquantity"));
    }

    /**
     * Test is field empty negatives.
     *
     * @return null
     */
    public function testIsFieldEmptyNegative()
    {
        //T2009-01-09
        //the tests are so trivial that I'll just do a buch of assert in one test
        $oSubj = $this->getProxyClass("oxarticle");

        $oSubj->oxanyfield = new stdClass();
        $oSubj->oxanyfield->value = "anyValue";
        $this->assertFalse($oSubj->UNITisFieldEmpty("oxanyfield"));

        $oSubj->oxanyfield = new stdClass();
        $oSubj->oxanyfield->value = "0000-00-00 00:00:01";
        $this->assertFalse($oSubj->UNITisFieldEmpty("oxanyfield"));

        $oSubj->oxarticles__oxanyfield = new stdClass();
        $oSubj->oxarticles__oxanyfield->value = "nopic.jpg";
        $this->assertFalse($oSubj->UNITisFieldEmpty("oxarticles__oxanyfield"));

        $oSubj->oxarticles__oxicon = new stdClass();
        $oSubj->oxarticles__oxicon->value = "nopic.jpg";
        $this->assertTrue($oSubj->UNITisFieldEmpty("oxarticles__oxicon"));

        $oSubj->oxarticles__oxthumb = new stdClass();
        $oSubj->oxarticles__oxthumb->value = "nopic_ico.jpg";
        $this->assertFalse($oSubj->UNITisFieldEmpty("oxarticles__oxthumb"));

        $oSubj->oxarticles__oxpic = new stdClass();
        $oSubj->oxarticles__oxpic->value = "nopic_ico.jpg";
        $this->assertFalse($oSubj->UNITisFieldEmpty("oxarticles__oxpic"));

        $oSubj->oxarticles__oxunitquantity = new stdClass();
        $oSubj->oxarticles__oxunitquantity->value = 3;
        $this->assertFalse($oSubj->UNITisFieldEmpty("oxarticles__oxunitquantity"));
    }

    /**
     * Test set load parent data.
     *
     * @return null
     */
    public function testGetLoadParentDataDefault()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertFalse($oArticle->getLoadParentData());
    }

    /**
     * Test set load parent data.
     *
     * @return null
     */
    public function testGetSetLoadParentDataTrue()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setLoadParentData(true);

        $this->assertTrue($oArticle->getLoadParentData());
    }

    /**
     * Test get similar products.
     *
     * @return null
     */
    public function testGetSimilarProducts()
    {
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");

        $oList = $oArticle->getSimilarProducts();

        if ('EE' === $this->getConfig()->getEdition()) {
            $this->assertEquals(4, count($oList));
        } else {
            $this->assertEquals(5, count($oList));
        }
    }

    /**
     * Test get similar products.
     *
     * @return null
     */
    public function testGetSimilarProductsForVariant()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('2000_varant');
        $oArticle->oxarticles__oxparentid = new oxField('2000');
        $oArticle->oxarticles__oxprice = new oxField(12.2);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oArticle->oxarticles__oxtitle = new oxField("test");
        $oArticle->oxarticles__oxtitle_1 = new oxField("testEng");
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000_varant');
        $oList = $oArticle->getSimilarProducts();

        $iCount = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $iCount = 4;
        }
        $this->assertEquals($iCount, count($oList));
        $oArticle->delete();
    }

    /**
     * Test get similar products if no attributes exists.
     *
     * @return null
     */
    public function testGetSimilarProductsNoAttribDontLoadSimilar()
    {
        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArt");
        $this->assertNull($oArticle->getSimilarProducts());
    }

    /**
     * Test get similar products when attribute loading is disabled in config.
     *
     * @return null
     */
    public function testGetSimilarProductsNoAttrib()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSimilar', false);
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");
        $this->assertNull($oArticle->getSimilarProducts());
    }

    /**
     * Test get similar products with 100% match.
     *
     * #0001137: iAttributesPercent = 100 doesnt work
     *
     * @return null
     */
    public function testGetSimilarProductsIf100Percent()
    {
        $this->getConfig()->setConfigParam('iAttributesPercent', 100);
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");
        $oList = $oArticle->getSimilarProducts();
        $iCount = 4;
        $this->assertEquals($iCount, count($oList));
    }

    /**
     * Test long descriptio saving , save raw value.
     *
     * @return null
     */
    public function testLongDescSaving_savesRawValue()
    {
        $oArticle = oxNew("oxArticle");
        if ($oArticle->load('test_SubshopFields_savesRawValue')) {
            $oArticle->delete();
        }
        oxDb::getDb()->execute('delete from oxarticles where oxid="test_SubshopFields_savesRawValue"');
        oxDb::getDb()->execute('delete from oxartextends where oxid="test_SubshopFields_savesRawValue"');


        // insert article
        $oArticle = oxNew("oxArticle");
        $oArticle->assign(array('OXID' => 'test_SubshopFields_savesRawValue'));
        $oArticle->setArticleLongDesc('lalaal&!<b><');
        $oArticle->save();

        $oArticle = oxNew("oxArticle");
        $this->assertTrue($oArticle->load('test_SubshopFields_savesRawValue'));
        $this->assertEquals('lalaal&!<b><', $oArticle->getLongDescription()->getRawValue());

        // lang 1
        $oArticle = oxNew("oxArticle");
        $oArticle->setLanguage(1);
        $oArticle->assign(array('OXID' => 'test_SubshopFields_savesRawValue'));
        $oArticle->setArticleLongDesc('lalaal&!<b><a');
        $oArticle->save();

        $oArticle = oxNew("oxArticle");
        $this->assertTrue($oArticle->loadInLang(1, 'test_SubshopFields_savesRawValue'));
        $this->assertEquals('lalaal&!<b><a', $oArticle->getLongDescription()->getRawValue());

        // back in 0 lang
        $oArticle = oxNew("oxArticle");
        $oArticle->setLanguage(0);
        $this->assertTrue($oArticle->load('test_SubshopFields_savesRawValue'));
        $this->assertEquals('lalaal&!<b><', $oArticle->getLongDescription()->getRawValue());
    }

    /**
     * Test long descriptio saving , save raw value.
     *
     * @return null
     */
    public function testLongDescSavingIfMultilingualIsFalse()
    {
        // insert article
        $oArticle = oxNew("oxArticle");
        $oArticle->setEnableMultilang(false);
        $oArticle->setId("_testArt");

        $oArticle->setArticleLongDesc('[de] lalaal&!<b><');
        $this->assertEquals("[de] lalaal&!<b><", $oArticle->getLongDescription()->value);

        // if _blEmployMultilanguage is false it is possible to set more languages only over fields. Not over setter/getter.
        $oArticle->oxarticles__oxlongdesc_1 = new oxField('[en] lalaal&!<b><', oxField::T_RAW);
        $this->assertEquals("[en] lalaal&!<b><", $oArticle->oxarticles__oxlongdesc_1->value);

        $oArticle->setLanguage(0);
        $oArticle->save();

        $this->assertEquals("[de] lalaal&!<b><", oxDb::getDB()->getOne("select oxlongdesc from oxartextends where oxid = '_testArt'"));
        $this->assertEquals("[en] lalaal&!<b><", oxDb::getDB()->getOne("select oxlongdesc_1 from oxartextends where oxid = '_testArt'"));
    }

    /**
     * Test long descriptio saving , save raw value.
     *
     * @return null
     */
    public function testLongDescSavingIfLongDescIsSkipped()
    {
        // insert article
        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setNonPublicVar('_aSkipSaveFields', array("oxlongdesc"));
        $oArticle->setId("_testArt");
        $oArticle->setArticleLongDesc('[de] lalaal&!<b><');
        $this->assertEquals("[de] lalaal&!<b><", $oArticle->getLongDescription()->value);
        $oArticle->UNITsaveArtLongDesc();

        $this->assertEquals("", oxDb::getDB()->getOne("select oxlongdesc from oxartextends where oxid = '_testArt'"));
    }

    /**
     * Test assign parent field values and set title.
     *
     * #1031: Lazy loading of field values does not load parent's oxtitle
     *
     * @return null
     */
    public function testAssignParentFieldValuesSetTitle()
    {
        $sVarId = '8a142c4100e0b2f57.59530204';
        $sParentId = '2077';
        $sTitle = 'Tischlampe SPHERE';

        if ($this->getConfig()->getEdition() === 'EE') {
            $sVarId = '2275-01';
            $sParentId = '2275';
            $sTitle = 'BBQ Grill TONNE';
        }
        $oArticle2 = oxNew('oxArticleHelper');
        $oArticle2->load($sVarId);
        $oArticle2->UNITassignParentFieldValues();
        $this->assertEquals($sTitle, $oArticle2->oxarticles__oxtitle->value);
    }

    /**
     * Test get category id's select.
     *
     * @return null
     */
    public function testGetSelectCatIds()
    {
        $oArticle = oxNew("oxArticle");
        $sO2CView = $oArticle->UNITgetObjectViewName('oxobject2category');
        $sCatView = $oArticle->UNITgetObjectViewName('oxcategories');

        $sSelect1 = "select oxobject2category.oxcatnid as oxcatnid from $sO2CView as oxobject2category left join $sCatView as oxcategories on oxcategories.oxid = oxobject2category.oxcatnid ";
        $sSelect1 .= "where oxobject2category.oxobjectid='test' and oxcategories.oxid is not null and oxcategories.oxactive" . (($oArticle->getLanguage()) ? '_' . $oArticle->getLanguage() : '') . " = 1 ";
        $sSubSelect = "and oxcategories.oxhidden = 0 and (select count(cats.oxid) from $sCatView as cats where cats.oxrootid = oxcategories.oxrootid and cats.oxleft < oxcategories.oxleft and cats.oxright > oxcategories.oxright and ( cats.oxhidden = 1 or cats.oxactive" . (($oArticle->getLanguage()) ? "_" . $oArticle->getLanguage() : "") . " = 0 ) ) = 0 ";
        $sSelect2 = "order by oxobject2category.oxtime ";
        $this->assertEquals($sSelect1 . $sSelect2, $oArticle->UNITgetSelectCatIds('test', false));
        // #1306: selecting active categories will not be checked if parent categories are active
        $this->assertEquals($sSelect1 . $sSubSelect . $sSelect2, $oArticle->UNITgetSelectCatIds('test', true));
    }

    /**
     * Test get category id's.
     *
     * @return null
     */
    public function testGetCategoryIds()
    {
        $oObj1 = oxNew("oxCategory");
        $oObj1->setId("_testCat1");
        $oObj1->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oObj1->oxcategories__oxactive = new oxField("0", oxField::T_RAW);
        $oObj1->save();
        $oObj2 = oxNew("oxCategory");
        $oObj2->setId("_testCat2");
        $oObj2->oxcategories__oxparentid = new oxField($oObj1->getId(), oxField::T_RAW);
        $oObj2->oxcategories__oxactive = new oxField("1", oxField::T_RAW);
        $oObj2->save();

        $sQ = "insert into oxobject2category set oxid = '_testArt2Cat', oxcatnid = '_testCat2', oxobjectid = '_testArt'";
        $this->addToDatabase($sQ, 'oxobject2category');

        $oArticle = $this->_createArticle('_testArt');
        $this->assertEquals(array("_testCat2"), $oArticle->getCategoryIds(false, true));
        // #1306: Selecting active categories will not be checked if parent categories are active
        $this->assertEquals(array(), $oArticle->getCategoryIds(true, true));
    }

    /**
     * Test get category id's - adding price categories to list.
     *
     * @return null
     */
    public function testGetCategoryIds_adsPriceCategoriesToList()
    {
        $oObj1 = oxNew("oxCategory");
        $oObj1->setId("_testCat1");
        $oObj1->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oObj1->oxcategories__oxactive = new oxField("1", oxField::T_RAW);
        $oObj1->save();

        $oObj2 = oxNew("oxCategory");
        $oObj2->setId("_testCat2");
        $oObj2->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oObj2->oxcategories__oxactive = new oxField("1", oxField::T_RAW);
        $oObj2->oxcategories__oxpricefrom = new oxField(100);
        $oObj2->oxcategories__oxpriceto = new oxField(200);
        $oObj2->save();

        $sQ = "insert into oxobject2category set oxid = '_testArt1Cat', oxcatnid = '_testCat1', oxobjectid = '_testArt'";
        $this->addToDatabase($sQ, 'oxobject2category');
        $this->addTableForCleanup('oxcategories');

        $oArticle = $this->_createArticle('_testArt');
        $oArticle->oxarticles__oxprice = new oxField(99);

        // price cat should be skipped
        $this->assertEquals(array("_testCat1"), $oArticle->getCategoryIds(false, true));

        // price cat should be inlcuded (M:1598)
        $oArticle->oxarticles__oxprice = new oxField(101);
        $this->assertEquals(array("_testCat1", "_testCat2"), $oArticle->getCategoryIds(false, true));
    }

    /**
     * Tests if the "oxarticle::GetCategoryIds()" uses a cached value
     *
     * @return null
     */
    public function testGetCategoryIds_VariantAssignedToCategory()
    {
        $testCatId = 'testcatid';
        $oCategory = oxNew('oxCategory');
        $oCategory->setId($testCatId);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oCategory->save();

        $testAid = 'testaid';
        $testParentid = 'testparentid';
        $oArticle = oxNew('oxArticle');
        $oArticle->setId($testAid);
        $oArticle->oxarticles__oxparentid = new oxField($testParentid, oxField::T_RAW);

        // assigning articles to category
        $oA2C = oxNew('oxobject2category');
        $oA2C->oxobject2category__oxobjectid = new oxField($testAid);
        $oA2C->oxobject2category__oxcatnid = new oxField($testCatId);
        $oA2C->setId($testAid);
        $oA2C->save();

        $oA2C = oxNew('oxBase');
        $oA2C->init('oxobject2category');
        $oA2C->oxobject2category__oxobjectid = new oxField($testParentid);
        $oA2C->oxobject2category__oxcatnid = new oxField($testCatId);
        $oA2C->setId($testParentid);
        $oA2C->save();

        $this->assertEquals(array($testCatId), $oArticle->getCategoryIds(false, true));
    }

    /**
     * Test get standard link with parameters.
     *
     * @return null
     */
    public function testGetStdLinkWithParams()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oArticle = $this->getMock('oxarticle', array('getSession'));
        $oArticle->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oArticle->setId('xxx');

        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=details&amp;anid=xxx&amp;cnid=cid&amp;lala=lili&amp;pgNr=10&amp;mnid=mmm&amp;listtype=search&amp;lang=1';

        $this->assertEquals($sUrl, $oArticle->getStdLink(1, array('cnid' => 'cid', 'lala' => 'lili', 'pgNr' => 10, 'mnid' => 'mmm', 'listtype' => 'search')));
    }

    /**
     * Test get select for price categories.
     *
     * @return null
     */
    public function testGetSqlForPriceCategories()
    {
        $oA = oxNew('oxArticle');
        $oA->setId('_testx');
        $oA->oxarticles__oxprice = new oxField(95);
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->assertEquals("select oxid from oxv_oxcategories_1_de where oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= '95' and oxpriceto >= '95' union select oxid from oxv_oxcategories_1_de where oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= '95' union select oxid from oxv_oxcategories_1_de where oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= '95'", $oA->getSqlForPriceCategories());
            $this->assertEquals("select oxid, oxlalaa from oxv_oxcategories_1_de where oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= '95' and oxpriceto >= '95' union select oxid, oxlalaa from oxv_oxcategories_1_de where oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= '95' union select oxid, oxlalaa from oxv_oxcategories_1_de where oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= '95'", $oA->getSqlForPriceCategories('oxid, oxlalaa'));
        } else {
            $this->assertEquals("select oxid from oxv_oxcategories_de where oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= '95' and oxpriceto >= '95' union select oxid from oxv_oxcategories_de where oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= '95' union select oxid from oxv_oxcategories_de where oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= '95'", $oA->getSqlForPriceCategories());
            $this->assertEquals("select oxid, oxlalaa from oxv_oxcategories_de where oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= '95' and oxpriceto >= '95' union select oxid, oxlalaa from oxv_oxcategories_de where oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= '95' union select oxid, oxlalaa from oxv_oxcategories_de where oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= '95'", $oA->getSqlForPriceCategories('oxid, oxlalaa'));
        }
    }

    /**
     * Data provider for testInPriceCategoryNoException.
     *
     * @return array
     */
    public function testInPriceCategoryNoExceptionDataProvider()
    {
        return array(
            array('1', true),
            array('', false)
        );
    }

    /**
     * Test in price category with no exceptions from db side.
     *
     * @dataProvider testInPriceCategoryNoExceptionDataProvider
     */
    public function testInPriceCategoryNoException($return, $expected)
    {
        $oA = oxNew('oxArticle');

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())
            ->method('getOne')
            ->will($this->returnValue($return));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock);

        $this->assertEquals($expected, $oA->inPriceCategory('sCatNid'));
    }

    /**
     * Test in price category with exception case.
     *
     * @return null
     */
    public function testInPriceCategoryException()
    {
        $oA = oxNew('oxArticle');
        $oA->setId('_testx');
        $oA->oxarticles__oxprice = new oxField(95);

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())
            ->method('getOne')
            ->will($this->returnCallback(function ($s) {
                    throw new Exception($s);
            }));

        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock);

        try {
            $oA->inPriceCategory('sCatNid');
        } catch (Exception $e) {
            if ($this->getConfig()->getEdition() === 'EE') {
                $this->assertEquals("select 1 from oxv_oxcategories_1_de where oxid='sCatNid' and(   (oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= '95' and oxpriceto >= '95') or (oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= '95') or (oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= '95'))", $e->getMessage());
            } else {
                $this->assertEquals("select 1 from oxv_oxcategories_de where oxid='sCatNid' and(   (oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= '95' and oxpriceto >= '95') or (oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= '95') or (oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= '95'))", $e->getMessage());
            }

            return;
        }
        $this->fail('exception from oxdb not thrown');
    }

    /**
     * Check if method "onChange" calls method "_onChangeStockResetCount" when
     * updating article stock (action = ACTION_UPDATE_STOCK)
     *
     * @return null
     */
    public function testOnChange_callsCountResetOnStockChange()
    {
        $oArticle = $this->getMock("oxarticle", array("_onChangeStockResetCount"));
        $oArticle->expects($this->once())->method('_onChangeStockResetCount')->with($this->equalTo('_testArt'));
        $oArticle->onChange(ACTION_UPDATE_STOCK, '_testArt');
    }

    /**
     * Check if method "onChange" does not calls method "_onChangeStockResetCount"
     * when not updating article stock (action != ACTION_UPDATE_STOCK)
     *
     * @return null
     */
    public function testOnChange_callsCountResetOnlyStockChange()
    {
        $oArticle = $this->getMock("oxarticle", array("_onChangeStockResetCount"));
        $oArticle->expects($this->never())->method('_onChangeStockResetCount');
        $oArticle->onChange(null, '_testArt');
    }

    /**
     * Check if method calls method "_onChangeResetCounts" with
     * correct parameters when stock value is zero
     *
     * @return null
     */
    public function testOnChange_onChangeStockResetCount()
    {
        $oArticle = $this->getMock("oxarticle", array("_onChangeResetCounts"));
        $oArticle->expects($this->once())->method('_onChangeResetCounts')->with($this->equalTo('_testArt'), $this->equalTo('_testVendorId'), $this->equalTo('_testManufacturerId'));
        $oArticle->oxarticles__oxvendorid = new oxField("_testVendorId");
        $oArticle->oxarticles__oxmanufacturerid = new oxField("_testManufacturerId");

        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->oxarticles__oxstock = new oxField(0);

        $oArticle->UNITonChangeStockResetCount("_testArt");
    }

    /**
     * Test checking if article has upladed master picture
     *
     * @return null
     */
    public function testHasMasterImage_noImage()
    {
        $oConfig = $this->getMock("oxconfig", array("getMasterPicturePath"));
        $oConfig->expects($this->any())->method('getMasterPicturePath')->with($this->equalTo('product/1/testPic1.jpg'))->will($this->returnValue(false));

        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setConfig($oConfig);
        $oArticle->oxarticles__oxpic1 = new oxField('testPic1.jpg');

        $this->assertFalse($oArticle->UNIThasMasterImage(1));
    }

    /**
     * Test checking if article has upladed master picture when pic value is
     * "nopic.jpg"
     *
     * @return null
     */
    public function testHasMasterImage_withDefaultNoImageValue()
    {
        $oConfig = $this->getMock("oxconfig", array("getMasterPicturePath"));
        $oConfig->expects($this->never())->method('getMasterPicturePath');

        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setConfig($oConfig);
        $oArticle->oxarticles__oxpic1 = new oxField('nopic.jpg');

        $this->assertFalse($oArticle->UNIThasMasterImage(1));
    }

    /**
     * #2192 Test checking if article has upladed master picture when pic value is
     * ""
     *
     * @return null
     */
    public function testHasMasterImage_withEmptyImageValue()
    {
        $oConfig = $this->getMock("oxconfig", array("getMasterPicturePath"));
        $oConfig->expects($this->never())->method('getMasterPicturePath');

        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setConfig($oConfig);
        $oArticle->oxarticles__oxpic1 = new oxField('');

        $this->assertFalse($oArticle->UNIThasMasterImage(1));
    }

    /**
     * Test checking if article has upladed master picture
     *
     * @return null
     */
    public function testHasMasterImage_hasImage()
    {
        $oConfig = $this->getMock("oxconfig", array("getMasterPicturePath"));
        $oConfig->expects($this->at(0))->method('getMasterPicturePath')->with($this->equalTo('product/1/testPic1.jpg'))->will($this->returnValue(true));
        $oConfig->expects($this->at(1))->method('getMasterPicturePath')->with($this->equalTo('product/2/testPic2.jpg'))->will($this->returnValue(true));

        $oArticle = $this->getProxyClass("oxarticle");
        $oArticle->setConfig($oConfig);
        $oArticle->oxarticles__oxpic1 = new oxField('testPic1.jpg');
        $oArticle->oxarticles__oxpic2 = new oxField('2/testPic2.jpg');

        $this->assertTrue($oArticle->UNIThasMasterImage(1));
        $this->assertTrue($oArticle->UNIThasMasterImage(2));
    }

    /**
     * Test checking if variant article has upladed master picture
     *
     * @return null
     */
    public function testHasMasterImage_IfParentHasImage()
    {
        $oConfig = $this->getMock("oxconfig", array("getMasterPicturePath"));
        $oConfig->expects($this->any())->method('getMasterPicturePath')->will($this->returnValue(true));

        $oArticle = $this->getProxyClass("oxArticle");
        $oArticle->setConfig($oConfig);
        $oArticle->oxarticles__oxpic1 = new oxField('testPic1.jpg', oxField::T_RAW);

        $oArticle2 = $this->getMock($this->getProxyClassName("oxArticle"), array("isVariant", "getParentArticle"));
        $oArticle2->expects($this->any())->method('isVariant')->will($this->returnValue(true));
        $oArticle2->expects($this->any())->method('getParentArticle')->will($this->returnValue($oArticle));
        $oArticle2->setConfig($oConfig);
        $oArticle2->oxarticles__oxpic1 = new oxField('testPic1.jpg');

        $this->assertFalse($oArticle2->UNIThasMasterImage(1));
    }

    /**
     * Test getting article images file names
     *
     * @return null
     */
    public function testGetPictureFieldValue()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField('testpic.jpg');
        $oArticle->oxarticles__oxicon = new oxField('testico.jpg');
        $oArticle->oxarticles__thumb = new oxField('testthumb.jpg');
        $oArticle->oxarticles__oxzoom2 = new oxField('testzoom.jpg');

        $this->assertEquals('testpic.jpg', $oArticle->getPictureFieldValue("oxpic", 1));
        $this->assertEquals('testico.jpg', $oArticle->getPictureFieldValue("oxicon"));
        $this->assertEquals('testthumb.jpg', $oArticle->getPictureFieldValue("thumb"));
        $this->assertEquals('testzoom.jpg', $oArticle->getPictureFieldValue("oxzoom", 2));
    }

    /**
     * Test checking getting master zoom picture url
     *
     * @return null
     */
    public function testGetMasterZoomPictureUrl_hasImage()
    {

        $sMasterPicDir = $this->getConfig()->getPictureUrl("master");
        $sPic = $sMasterPicDir . "/product/1/30-360-back_p1_z_f_th_665.jpg";


        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField('30-360-back_p1_z_f_th_665.jpg');

        $this->assertEquals($sPic, $oArticle->getMasterZoomPictureUrl(1));
    }

    /**
     * Test checking getting master zoom picture url - no picture defined
     *
     * @return null
     */
    public function testGetMasterZoomPictureUrl_notExistingImage()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField('noSuchPic.jpg');

        $this->assertFalse($oArticle->getMasterZoomPictureUrl(1));
    }

    /**
     * Test checking getting master zoom picture url - no picture defined
     *
     * @return null
     */
    public function testGetMasterZoomPictureUrl_noImage()
    {
        $oArticle = oxNew('oxArticle');

        $this->assertFalse($oArticle->getMasterZoomPictureUrl(1));
    }

    /**
     * Test checking getting master zoom picture url - pic value = "nopic"
     *
     * @return null
     */
    public function testGetMasterZoomPictureUrl_noPicValue()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField('nopic.jpg');

        $this->assertFalse($oArticle->getMasterZoomPictureUrl(1));
    }

    /**
     * oxArticle::getVariantSelections() test case
     *
     * @return null
     */
    public function testGetVariantSelections()
    {
        oxTestModules::addFunction("oxVariantHandler", "buildVariantSelections", "{return 'buildVariantSelections';}");
        $oVariantHandler = $this->getMock('oxVariantHandler', array("buildVariantSelections"));
        $aVariantSelections = array('selections' => 'asd', 'rawselections' => 'asd');
        $oVariantHandler->expects($this->once())->method("buildVariantSelections")
            ->with($this->equalTo('varname'), $this->equalTo('variants'), $this->equalTo(1), $this->equalTo(2), $this->equalTo(3))
            ->will($this->returnValue($aVariantSelections));
        oxTestModules::addModuleObject("oxVariantHandler", $oVariantHandler);

        $oProduct = $this->getMock("oxArticle", array("getVariants"));
        $oProduct->expects($this->once())->method('getVariants')->will($this->returnValue('variants'));
        $oProduct->oxarticles__oxvarcount = new oxField(3);
        $oProduct->oxarticles__oxvarname = new oxField('varname');
        $this->assertEquals($aVariantSelections, $oProduct->getVariantSelections(1, 2, 3));
    }

    /**
     * oxArticle::getVariantSelections() with all inactive variants
     * #0004199
     *
     * @return null
     */
    public function testGetVariantSelectionsWithAllInactiveVariants()
    {
        oxTestModules::addFunction("oxVariantHandler", "buildVariantSelections", "{return 'buildVariantSelections';}");
        $oVariantHandler = $this->getMock('oxVariantHandler', array("buildVariantSelections"));
        $aVariantSelections = array('selections' => 'asd', 'rawselections' => '');
        $oVariantHandler->expects($this->once())->method("buildVariantSelections")
            ->with($this->equalTo('varname'), $this->equalTo('variants'), $this->equalTo(1), $this->equalTo(2), $this->equalTo(3))
            ->will($this->returnValue($aVariantSelections));
        oxTestModules::addModuleObject("oxVariantHandler", $oVariantHandler);

        $oProduct = $this->getMock("oxArticle", array("getVariants"));
        $oProduct->expects($this->once())->method('getVariants')->will($this->returnValue('variants'));
        $oProduct->oxarticles__oxvarcount = new oxField(3);
        $oProduct->oxarticles__oxvarname = new oxField('varname');
        $this->assertFalse($oProduct->getVariantSelections(1, 2, 3));
    }

    /**
     * oxArticle::getVariantSelections() should return selection list when no variants exists (blLoadVariants = false)
     *
     * @return null
     */
    public function testGetVariantSelectionsWithNoVariants()
    {
        oxTestModules::addFunction("oxVariantHandler", "buildVariantSelections", "{return 'buildVariantSelections';}");
        $oVariantHandler = $this->getMock('oxVariantHandler', array("buildVariantSelections"));
        $aVariantSelections = array('selections' => 'asd', 'rawselections' => '');
        $oVariantHandler->expects($this->once())->method("buildVariantSelections")
            ->with($this->equalTo('varname'), $this->equalTo(array()), $this->equalTo(1), $this->equalTo(2), $this->equalTo(3))
            ->will($this->returnValue($aVariantSelections));
        oxTestModules::addModuleObject("oxVariantHandler", $oVariantHandler);

        $oProduct = $this->getMock("oxArticle", array("getVariants"));
        $oProduct->expects($this->once())->method('getVariants')->will($this->returnValue(array()));
        $oProduct->oxarticles__oxvarcount = new oxField(3);
        $oProduct->oxarticles__oxvarname = new oxField('varname');
        $this->assertEquals($aVariantSelections, $oProduct->getVariantSelections(1, 2, 3));
    }

    /**
     * oxArticle::getSelections() test case
     *
     * @return null
     */
    public function testGetSelections()
    {
        // inserting selection lists
        $oSel = oxNew('oxBase');
        $oSel->init("oxselectlist");
        $oSel->setId("_testSel1");
        $oSel->oxselectlist__oxshopid = new oxField(1);
        $oSel->oxselectlist__oxtitle = new oxField("selection list A");
        $oSel->oxselectlist__oxtitle_1 = new oxField("selection list A");
        $oSel->oxselectlist__oxvaldesc = new oxField("L__@@M__@@S__@@");
        $oSel->oxselectlist__oxvaldesc_1 = new oxField("L__@@M__@@S__@@");
        $oSel->save();

        $oSel = oxNew('oxBase');
        $oSel->init("oxselectlist");
        $oSel->setId("_testSel2");
        $oSel->oxselectlist__oxshopid = new oxField(1);
        $oSel->oxselectlist__oxtitle = new oxField("selection list B");
        $oSel->oxselectlist__oxtitle_1 = new oxField("selection list B");
        $oSel->oxselectlist__oxvaldesc = new oxField("Blue__@@Green__@@Red__@@");
        $oSel->oxselectlist__oxvaldesc_1 = new oxField("Blue__@@Green__@@Red__@@");
        $oSel->save();

        // assigning to products
        $oO2S = oxNew('oxBase');
        $oO2S->init("oxobject2selectlist");
        $oO2S->setId("_testo2s1");
        $oO2S->oxobject2selectlist__oxobjectid = new oxField("1126");
        $oO2S->oxobject2selectlist__oxselnid = new oxField("_testSel1");
        $oO2S->oxobject2selectlist__oxsort = new oxField(1);
        $oO2S->save();

        $oO2S = oxNew('oxBase');
        $oO2S->init("oxobject2selectlist");
        $oO2S->setId("_testo2s2");
        $oO2S->oxobject2selectlist__oxobjectid = new oxField("1126");
        $oO2S->oxobject2selectlist__oxselnid = new oxField("_testSel2");
        $oO2S->oxobject2selectlist__oxsort = new oxField(2);
        $oO2S->save();

        // loading product
        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        // default
        $aList = $oProduct->getSelections();
        $this->assertTrue((bool) $aList);
        $this->assertEquals(2, $aList->count());

        $aIds = $aList->arrayKeys();
        $this->assertEquals($aList[$aIds[0]]->getActiveSelection()->getName(), "L");
        $this->assertEquals($aList[$aIds[1]]->getActiveSelection()->getName(), "Blue");

        // limited
        $aList = $oProduct->getSelections(1);
        $this->assertTrue((bool) $aList);
        $this->assertEquals(1, $aList->count());
        $aIds = $aList->arrayKeys();
        $this->assertEquals($aList[$aIds[0]]->getActiveSelection()->getName(), "L");

        // with filter
        $aList = $oProduct->getSelections(null, array(1, 2));
        $this->assertTrue((bool) $aList);
        $this->assertEquals(2, $aList->count());

        $aIds = $aList->arrayKeys();
        $this->assertEquals($aList[$aIds[0]]->getActiveSelection()->getName(), "M");
        $this->assertEquals($aList[$aIds[1]]->getActiveSelection()->getName(), "Red");
    }

    /**
     * Inserts new test language tables
     *
     * @return null
     */
    protected function _insertTestLanguage()
    {
        // creating new language tables
        $aQ[] = "CREATE TABLE oxarticles_set1 (OXID char(32) COLLATE latin1_general_ci NOT NULL, PRIMARY KEY (OXID)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXVARNAME_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXVARSELECT_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXTITLE_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $sQ[] = "ALTER TABLE oxarticles_set1 ADD OXSHORTDESC_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXURLDESC_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXSEARCHKEYS_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXSTOCKTEXT_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";
        $aQ[] = "ALTER TABLE oxarticles_set1 ADD OXNOSTOCKTEXT_5 varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT ''";

        $aQ[] = "CREATE TABLE oxartextends_set1 (OXID char(32) COLLATE latin1_general_ci NOT NULL, PRIMARY KEY (`OXID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
        $aQ[] = "ALTER TABLE oxartextends_set1 ADD OXLONGDESC_5 text COLLATE latin1_general_ci NOT NULL";

        $aQ[] = "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_1_1 AS SELECT oxarticles.* FROM oxarticles";
        $aQ[] = "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_1_0 AS SELECT oxarticles.* FROM oxarticles";
        $aQ[] = "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_0 AS SELECT oxartextends.* FROM oxartextends";
        $aQ[] = "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_1 AS SELECT oxartextends.* FROM oxartextends";

        $oDb = oxDb::getDb();
        foreach ($aQ as $sQ) {
            $oDb->execute($sQ);
        }
    }

    /**
     * Removes test language tables
     *
     * @return null
     */
    protected function _deleteTestLanguage()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("drop table oxarticles_set1");
        $oDb->execute("drop table oxartextends_set1");
        $oDb->execute("drop view oxv_oxarticles_1_0");
        $oDb->execute("drop view oxv_oxarticles_1_1");
        $oDb->execute("drop view oxv_oxartextends_0");
        $oDb->execute("drop view oxv_oxartextends_1");
    }

    /**
     * Test case for #0002726: rows in additional language tables ar not deleted
     *
     * @return null
     */
    public function testDeleteWithUnlimitedLanguages()
    {
        $this->_insertTestLanguage();
        $this->_createArticle('_testArt', '_testVar');
        $this->getConfig()->setConfigParam("iLangPerTable", 4);

        oxTestModules::addFunction("oxLang", "getLanguageIds", "{return array('0'=>'de', '1'=>'en', '2', '3', '4', '5');}");
        oxTestModules::addFunction("oxArticle", "_assignPrices", "{}");
        oxTestModules::addFunction("oxArticle", "_onChangeUpdateStock", "{}");

        $sProdId = '_testArt';
        $sVarId = '_testVar';

        $oDb = oxDb::getDb();

        // inserting test data
        $aQ2[] = "insert into oxarticles_set1 (oxid, oxtitle_5, oxvarname_5) values ('{$sProdId}','title','varname') ";
        $aQ2[] = "insert into oxartextends_set1 (oxid, oxlongdesc_5) values ('{$sProdId}','longdesc') ";
        $aQ2[] = "insert into oxarticles_set1 (oxid, oxtitle_5, oxvarname_5) values ('{$sVarId}','title','varname') ";
        $aQ2[] = "insert into oxartextends_set1 (oxid, oxlongdesc_5) values ('{$sVarId}','longdesc') ";
        foreach ($aQ2 as $sQ) {
            $oDb->execute($sQ);
        }

        $aQ[] = "select 1 from oxarticles where oxid = '{$sProdId}'";
        $aQ[] = "select 1 from oxarticles_set1 where oxid = '{$sProdId}'";
        $aQ[] = "select 1 from oxartextends_set1 where oxid = '{$sProdId}'";
        $aQ[] = "select 1 from oxarticles where oxid = '{$sVarId}'";
        $aQ[] = "select 1 from oxarticles_set1 where oxid = '{$sVarId}'";
        $aQ[] = "select 1 from oxartextends_set1 where oxid = '{$sVarId}'";

        // tables are full before deletion
        foreach ($aQ as $sQ) {
            $this->assertTrue((bool) $oDb->getOne($sQ));
        }

        $oProduct = oxNew("oxArticle");
        $oProduct->delete($sProdId);

        // tables are cleaned-up after deletion
        foreach ($aQ as $sQ) {
            $this->assertFalse($oDb->getOne($sQ));
        }

        $this->_deleteTestLanguage();
    }

    public function testGetUnitName()
    {
        $sConstName = "_UNIT_KG";
        $oProduct = oxNew('oxArticle');

        // unit name is not set
        $oProduct->oxarticles__oxunitname = new oxField(null);
        $this->assertNull($oProduct->getUnitName());

        // unit name is set..
        $oProduct->oxarticles__oxunitname = new oxField($sConstName);
        $this->assertEquals(oxRegistry::getLang()->translateString($sConstName), $oProduct->getUnitName());
    }


    /**
     * Test case for getArticlefiles
     *
     * @return null
     */
    public function testGetArticleFiles()
    {
        $this->_createArticle('_testArt', '_testVar');
        $oDb = oxDb::getDb();

        // inserting test data
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME) values ('testId1','_testArt','testFile1') ";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME) values ('testId2','_testArt','testFile2') ";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME) values ('testId3','_testVar','testFile3') ";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME) values ('testId4','_testVar','testFile4') ";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME) values ('testId5','_testVar','testFile5') ";

        foreach ($aQ as $sQ) {
            $oDb->execute($sQ);
        }

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testVar');

        $this->getConfig()->setConfigParam('blVariantParentBuyable', false);

        $this->assertEquals(3, count($oArticle->getArticleFiles()));
        //checking chache
        $this->assertEquals(3, count($oArticle->getArticleFiles(true)));

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testVar');
        $this->assertEquals(5, count($oArticle->getArticleFiles(true)));

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testVar');
        $this->getConfig()->setConfigParam('blVariantParentBuyable', true);

        $this->assertEquals(3, count($oArticle->getArticleFiles()));
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testVar');
        $this->assertEquals(3, count($oArticle->getArticleFiles(true)));

    }

    /**
     * Test checking oxarticle::isDownloadable
     *
     * @return null
     */
    public function testIsDownloadable()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxisdownloadable = new oxField(true);

        $this->assertTrue($oArticle->isDownloadable());
    }

    /**
     * Test has amount price
     *
     * @return null
     */
    public function testHasAmountPriceEmpty()
    {
        oxArticleHelper::resetAmountPrice();

        oxDb::getDb()->execute('TRUNCATE TABLE `oxprice2article`');

        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        $this->assertFalse($oProduct->hasAmountPrice());
    }

    /**
     * Test has amount price
     *
     * @return null
     */
    public function testHasAmountPrice()
    {
        oxArticleHelper::resetAmountPrice();

        // assign scale price Amount 2-2 Price 11.95
        $oPrice2Prod = oxNew('oxBase');
        $oPrice2Prod->init('oxprice2article');
        $oPrice2Prod->setId('_testPrice2article');
        $oPrice2Prod->oxprice2article__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oPrice2Prod->oxprice2article__oxartid = new oxField("1126");
        $oPrice2Prod->oxprice2article__oxaddabs = new oxField(17);
        $oPrice2Prod->oxprice2article__oxamount = new oxField(2);
        $oPrice2Prod->oxprice2article__oxamountto = new oxField(2);
        $oPrice2Prod->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        $this->assertTrue($oProduct->hasAmountPrice());
    }

    /**
     * Test has amount price
     *
     * @return null
     */
    public function testSetRating()
    {
        $oProduct = $this->_createArticle('_testArt');
        $oProduct->setRatingAverage(4);
        $oProduct->setRatingCount(13);
        $oProduct->save();

        $oP = oxNew('oxArticle');
        $oP->load("_testArt");

        $this->assertEquals(4, $oP->oxarticles__oxrating->value);
        $this->assertEquals(13, $oP->oxarticles__oxratingcnt->value);
    }

    /**
     * Checks that in admin articles are not cached statically
     */
    public function testStaticCacheInAdmin()
    {
        $this->setAdminMode(1);
        $oArticle = $this->getMock('oxArticle', array('_loadFromDb'));

        $oArticle->expects($this->exactly(4))->method('_loadFromDb')->with($this->equalTo("2176"))->
            will($this->returnValue(array("oxid" => 2176, "oxparentid" => 2000)));
        $oArticle->load("2176");
        $oArticle->load("2176");
        $oArticle->load("2176");
        $oArticle->load("2176");

        $this->assertEquals(2000, $oArticle->getFieldData("oxparentid"));
    }

    /**
     * Checks that in admin articles are not cached statically
     */
    public function testGetVariantsCount()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxvarcount = new oxField(39);

        $this->assertEquals(39, $oArticle->getVariantsCount());
    }

    public function testGetSize()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxlength = new oxField(1);
        $oArticle->oxarticles__oxwidth = new oxField(2);
        $oArticle->oxarticles__oxheight = new oxField(3);

        $this->assertEquals(6, $oArticle->getSize());
    }

    public function testGetWright()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxweight = new oxField(1.12);

        $this->assertEquals(1.12, $oArticle->getWeight());
    }

    public function testIsImageField()
    {
        $oArt = $this->getProxyClass("oxArticle");

        $this->assertFalse($oArt->UNITisImageField("oxarticles__oxtitle"));
        $this->assertTrue($oArt->UNITisImageField("oxarticles__oxthumb"));
        $this->assertTrue($oArt->UNITisImageField("oxarticles__oxicon"));
        $this->assertTrue($oArt->UNITisImageField("oxarticles__oxpic2"));
        $this->assertTrue($oArt->UNITisImageField("oxarticles__oxpic1"));
    }

    /**
     * @return array
     */
    public function providerHasAgreement()
    {
        return array(
            array(1, 1, true),
            array(0, 1, false),
            array(1, 0, false),
            array(0, 0, false)
        );
    }

    /**
     * @param $iIsIntangible
     * @param $iShowCustomAgreement
     * @param $blResult
     *
     * @dataProvider providerHasAgreement
     */
    public function testHasIntangibleAgreement($iIsIntangible, $iShowCustomAgreement, $blResult)
    {
        $oProduct = $this->_getArticleWithCustomisedAgreement($iShowCustomAgreement);
        $oProduct->oxarticles__oxnonmaterial = new oxField($iIsIntangible);

        $this->assertSame($blResult, $oProduct->hasIntangibleAgreement());
    }

    /**
     */
    public function testHasIntangibleAgreementWithBothIntagibleAndDownloadableArticle()
    {
        $oProduct = $this->_getArticleWithCustomisedAgreement(true);
        $oProduct->oxarticles__oxnonmaterial = new oxField(true);
        $oProduct->oxarticles__oxisdownloadable = new oxField(true);

        $this->assertSame(false, $oProduct->hasIntangibleAgreement());
    }

    /**
     * @param $iIsDownloadable
     * @param $iShowCustomAgreement
     * @param $blResult
     *
     * @dataProvider providerHasAgreement
     */
    public function testHasDownloadableAgreement($iIsDownloadable, $iShowCustomAgreement, $blResult)
    {
        $oProduct = $this->_getArticleWithCustomisedAgreement($iShowCustomAgreement);
        $oProduct->oxarticles__oxisdownloadable = new oxField($iIsDownloadable);

        $this->assertSame($blResult, $oProduct->hasDownloadableAgreement());
    }

    /**
     */
    public function testHasDownloadableAgreementWithBothIntagibleAndDownloadableArticle()
    {
        $oProduct = $this->_getArticleWithCustomisedAgreement(true);
        $oProduct->oxarticles__oxnonmaterial = new oxField(true);
        $oProduct->oxarticles__oxisdownloadable = new oxField(true);

        $this->assertSame(true, $oProduct->hasDownloadableAgreement());
    }

    /**
     * Returns article with set custom agreement field.
     *
     * @param $iShowCustomAgreement
     *
     * @return oxArticle
     */
    private function _getArticleWithCustomisedAgreement($iShowCustomAgreement)
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticle');
        $oProduct->oxarticles__oxshowcustomagreement = new oxField($iShowCustomAgreement);

        return $oProduct;
    }

}
