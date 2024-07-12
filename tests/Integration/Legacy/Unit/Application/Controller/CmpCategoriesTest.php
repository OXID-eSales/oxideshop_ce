<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \Exception;
use OxidEsales\Eshop\Core\Config;
use \stdClass;
use \oxField;
use \oxTestModules;

class CmpCategoriesTest extends \PHPUnit\Framework\TestCase
{
    public static $oCL;

    protected function tearDown(): void
    {
        self::$oCL = null;
        parent::tearDown();
    }

    public function testInitReturnsInOrderStep()
    {
        $oActView = $this->getMock('stdClass', ['getIsOrderStep']);
        $oActView->expects($this->once())->method('getIsOrderStep')->willReturn(true);

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getTopActiveView']);
        $oCfg->method('getConfigParam')->with('blDisableNavBars')->willReturn(true);
        $oCfg->expects($this->once())->method('getTopActiveView')->willReturn($oActView);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getActCat', 'getConfig']);
        $o->expects($this->never())->method('getActCat');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $o->init();
    }

    public function testInitReturnsInOrderStepCfgOff()
    {
        $oActView = $this->getMock('stdClass', ['getIsOrderStep']);
        $oActView->expects($this->never())->method('getIsOrderStep')->willReturn(true);

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getTopActiveView']);
        $oCfg->method('getConfigParam')->with('blDisableNavBars')->willReturn(false);
        $oCfg->expects($this->never())->method('getTopActiveView')->willReturn($oActView);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getActCat', 'getConfig']);
        $o->expects($this->once())->method('getActCat')->willThrowException(new Exception("passed: OK"));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        try {
            $o->init();
        } catch (Exception $exception) {
            $this->assertSame("passed: OK", $exception->getMessage());

            return;
        }

        $this->fail("no exception is thrown");
    }

    public function testInitReturnsNoOrderStep()
    {
        $oActView = $this->getMock('stdClass', ['getIsOrderStep']);
        $oActView->expects($this->once())->method('getIsOrderStep')->willReturn(false);

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getTopActiveView']);
        $oCfg->method('getConfigParam')->with('blDisableNavBars')->willReturn(true);

        $oCfg->expects($this->once())->method('getTopActiveView')->willReturn($oActView);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getActCat', 'getConfig']);
        $o->expects($this->once())->method('getActCat')->willThrowException(new Exception("passed: OK"));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        try {
            $o->init();
        } catch (Exception $exception) {
            $this->assertSame("passed: OK", $exception->getMessage());

            return;
        }

        $this->fail("no exception is thrown");
    }

    public function testInitLoadManufacturerTree()
    {
        $oActView = $this->getMock('stdClass', ['getIsOrderStep']);
        $oActView->expects($this->once())->method('getIsOrderStep')->willReturn(false);

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getTopActiveView']);
        $oCfg
            ->method('getConfigParam')
            ->withConsecutive(['blDisableNavBars'], ['bl_perfLoadManufacturerTree'])
            ->willReturnOnConsecutiveCalls(true, true);

        $oCfg->method('getTopActiveView')->willReturn($oActView);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getActCat', 'getConfig', 'loadManufacturerTree']);
        $o->expects($this->once())->method('getActCat')->willReturn("actcat..");
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->expects($this->once())->method('loadManufacturerTree')->with("manid")->willThrowException(new Exception("passed: OK"));

        $this->setRequestParameter('mnid', 'manid');
        try {
            $o->init();
        } catch (Exception $exception) {
            $this->assertSame("passed: OK", $exception->getMessage());

            return;
        }

        $this->fail("no exception is thrown");
    }


    public function testInitLoadCategoryTree()
    {
        $oActView = $this->getMock('stdClass', ['getIsOrderStep']);
        $oActView->expects($this->once())->method('getIsOrderStep')->willReturn(false);

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getTopActiveView']);
        $oCfg
            ->method('getConfigParam')
            ->withConsecutive(['blDisableNavBars'], ['bl_perfLoadManufacturerTree'])
            ->willReturnOnConsecutiveCalls(true, false);

        $oCfg->method('getTopActiveView')->willReturn($oActView);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getActCat', 'getConfig', 'loadManufacturerTree', 'loadCategoryTree']);
        $o->expects($this->once())->method('getActCat')->willReturn("actcat..");
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->expects($this->never())->method('loadManufacturerTree');
        $o->expects($this->once())->method('loadCategoryTree')->with("actcat..")->willThrowException(new Exception("passed: OK"));

        try {
            $o->init();
        } catch (Exception $exception) {
            $this->assertSame("passed: OK", $exception->getMessage());

            return;
        }

        $this->fail("no exception is thrown");
    }


    public function testInitChecksTopNaviConfigParamAndSkipsGetMoreCat()
    {
        $oActView = $this->getMock('stdClass', ['getIsOrderStep']);
        $oActView->expects($this->once())->method('getIsOrderStep')->willReturn(false);

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getTopActiveView']);
        $oCfg
            ->method('getConfigParam')
            ->withConsecutive(['blDisableNavBars'], ['bl_perfLoadManufacturerTree'])
            ->willReturnOnConsecutiveCalls(true, false);

        $oCfg->method('getTopActiveView')->willReturn($oActView);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getActCat', 'getConfig', 'loadManufacturerTree', 'loadCategoryTree']);
        $o->expects($this->once())->method('getActCat')->willReturn("actcat..");
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->expects($this->never())->method('loadManufacturerTree');
        $o->expects($this->once())->method('loadCategoryTree')->with("actcat..");

        $o->init();
    }


    public function testGetProductNoAnid()
    {
        $oParent = $this->getMock('stdClass', ['getViewProduct']);
        $oParent->expects($this->never())->method('getViewProduct')->willReturn(false);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, []);
        $o->setParent($oParent);

        $this->setRequestParameter('anid', '');

        $this->assertNull($o->getProduct());
    }

    public function testGetProductWithAnidAndGetViewProduct()
    {
        $this->setRequestParameter('anid', 'lalala');

        $oParent = $this->getMock('stdClass', ['getViewProduct']);
        $oParent->expects($this->once())->method('getViewProduct')->willReturn('asd');

        $o = oxNew('oxcmp_categories');
        $o->setParent($oParent);

        $this->assertSame('asd', $o->getProduct());
    }

    public function testGetProductWithAnidLoadsArticle()
    {
        $this->setRequestParameter('anid', 'lalala');

        oxTestModules::addFunction('oxarticle', 'load($id)', '{$this->setId($id); return "lalala" == $id;}');

        $oExpectArticle = oxNew('oxArticle');
        $this->assertEquals(true, $oExpectArticle->load('lalala'));

        $oParent = $this->getMock('stdClass', ['getViewProduct', 'setViewProduct']);
        $oParent->expects($this->once())->method('getViewProduct')->willReturn(null);
        $oParent->expects($this->once())->method('setViewProduct')->with($oExpectArticle)->willReturn(null);

        $o = oxNew('oxcmp_categories');
        $o->setParent($oParent);

        $this->assertSame('lalala', $o->getProduct()->getId());
    }

    public function testGetProductWithAnidLoadArticleFails()
    {
        $this->setRequestParameter('anid', 'blah');

        oxTestModules::addFunction('oxarticle', 'load($id)', '{$this->setId($id); return "lalala" == $id;}');

        $oParent = $this->getMock('stdClass', ['getViewProduct', 'setViewProduct']);
        $oParent->expects($this->once())->method('getViewProduct')->willReturn(null);
        $oParent->expects($this->never())->method('setViewProduct');

        $o = oxNew('oxcmp_categories');
        $o->setParent($oParent);

        $this->assertNull($o->getProduct());
    }

    public function testGetActCatLoadDefault()
    {
        $oActShop = new stdClass();
        $oActShop->oxshops__oxdefcat = new oxField('default category');

        $oCfg = $this->getMock(Config::class, ['getActiveShop']);
        $oCfg->expects($this->once())->method('getActiveShop')->willReturn($oActShop);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig', 'getProduct', 'addAdditionalParams']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->expects($this->once())->method('getProduct')->willReturn(null);
        $o->expects($this->never())->method('addAdditionalParams');

        $this->setRequestParameter('mnid', null);
        $this->setRequestParameter('cnid', null);

        $this->assertSame('default category', $o->getActCat());
    }

    public function testGetActCatLoadDefaultoxroot()
    {
        $oActShop = new stdClass();
        $oActShop->oxshops__oxdefcat = new oxField('oxrootid');

        $oCfg = $this->getMock(Config::class, ['getActiveShop']);
        $oCfg->expects($this->once())->method('getActiveShop')->willReturn($oActShop);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig', 'getProduct', 'addAdditionalParams']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->expects($this->once())->method('getProduct')->willReturn(null);
        $o->expects($this->never())->method('addAdditionalParams');

        $this->setRequestParameter('mnid', null);
        $this->setRequestParameter('cnid', null);

        $this->assertNull($o->getActCat());
    }

    public function testGetActCatWithProduct()
    {
        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getProduct', 'addAdditionalParams']);
        $o->expects($this->once())->method('getProduct')->willReturn("product");
        $o->expects($this->once())->method('addAdditionalParams')->with(
            "product",
            null,
            'mnid'
        );

        $this->setRequestParameter('mnid', 'mnid');
        $this->setRequestParameter('cnid', 'cnid');

        $this->assertNull($o->getActCat());
    }

    public function testGetActCatWithProductAltBranches()
    {
        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getProduct', 'addAdditionalParams']);
        $o->expects($this->once())->method('getProduct')->willReturn("product");
        $o->expects($this->once())->method('addAdditionalParams')->with(
            "product",
            "cnid",
            ''
        );

        $this->setRequestParameter('mnid', '');
        $this->setRequestParameter('cnid', 'cnid');

        $this->assertNull($o->getActCat());
    }

    public function testLoadCategoryTree()
    {
        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ['buildTree', 'getClickCat']);
        $oCategoryList->expects($this->once())->method('buildTree')->with('act cat');

        oxTestModules::addModuleObject('oxCategoryList', $oCategoryList);

        $oParent = $this->getMock('stdclass', ['setCategoryTree', 'setActiveCategory']);
        $oParent->expects($this->once())->method('setCategoryTree')
            ->with($oCategoryList);

        $o = oxNew('oxcmp_categories');

        $o->setParent($oParent);

        $this->assertNull($o->loadCategoryTree("act cat"));
    }

    public function testLoadManufacturerTreeIsNotNeeded()
    {
        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl, ...$arguments)', '{if ("oxmanufacturerlist" == $cl) return \Unit\Application\Controller\CmpCategoriesTest::$oCL; return parent::oxNew($cl, ...$arguments);}');

        $oCfg = $this->getMock(Config::class, ['getConfigParam']);
        $oCfg->method('getConfigParam')->with('bl_perfLoadManufacturerTree')->willReturn(false);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $this->assertNull($o->loadManufacturerTree("act Manufacturer"));
    }

    public function testLoadManufacturerTree()
    {
        self::$oCL = $this->getMock('stdclass', ['buildManufacturerTree', 'getClickManufacturer']);
        self::$oCL->expects($this->once())->method('buildManufacturerTree')
            ->with(
                'manufacturerlist',
                'act Manufacturer',
                'passitthru1'
            );
        self::$oCL->expects($this->once())->method('getClickManufacturer')->willReturn("returned click Manufacturer");

        $oParent = $this->getMock('stdclass', ['setManufacturerTree', 'setActManufacturer']);
        $oParent->expects($this->once())->method('setManufacturerTree')
            ->with(
                self::$oCL
            );
        $oParent->expects($this->once())->method('setActManufacturer')
            ->with(
                "returned click Manufacturer"
            );

        $oCfg = $this->getMock(Config::class, ['getConfigParam', 'getShopHomeURL']);
        $oCfg->method('getConfigParam')->with('bl_perfLoadManufacturerTree')->willReturn(true);
        $oCfg->method('getShopHomeURL')->willReturn("passitthru1");

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig', 'getManufacturerList']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->method('getManufacturerList')->willReturn(self::$oCL);

        $o->setParent($oParent);

        $this->assertNull($o->loadManufacturerTree("act Manufacturer"));
    }

    public function testRenderEverythingOff()
    {
        $oCfg = $this->getMock(Config::class, ['getConfigParam']);
        $oCfg->method('getConfigParam')->with('bl_perfLoadManufacturerTree')->willReturn(false);

        $oParent = $this->getMock('stdClass', ['getManufacturerTree', 'getCategoryTree']);
        $oParent->expects($this->never())->method('getManufacturerTree');

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->setParent($oParent);

        $this->assertNull($o->render());
    }

    public function testRenderMenufactList()
    {
        $oCfg = $this->getMock(Config::class, ['getConfigParam']);
        $oCfg->method('getConfigParam')->with('bl_perfLoadManufacturerTree')->willReturn(true);

        $oMTree = $this->getMock('stdClass', ['getRootCat']);
        $oMTree->method('getRootCat')->willReturn("root Manufacturer cat");

        $oParent = $this->getMock('stdClass', ['setManufacturerlist', 'setRootManufacturer']);
        $oParent->expects($this->once())->method('setManufacturerlist')->with($oMTree);
        $oParent->expects($this->once())->method('setRootManufacturer')->with("root Manufacturer cat");

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->setManufacturerTree($oMTree);
        $o->setParent($oParent);

        $this->assertNull($o->render());
    }

    public function testRenderCategoryList()
    {
        $oCfg = $this->getMock(Config::class, ['getConfigParam']);
        $oCfg->method('getConfigParam')->with('bl_perfLoadManufacturerTree')->willReturn(false);

        $oCTree = $this->getMock('stdClass', []);

        $oParent = $this->getMock('stdClass', ['setManufacturerTree']);
        $oParent->expects($this->never())->method('setManufacturerTree');

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->setParent($oParent);
        $o->setCategoryTree($oCTree);

        $this->assertSame($oCTree, $o->render());
    }

    public function testRenderCategoryListTopNavi()
    {
        $oCfg = $this->getMock(Config::class, ['getConfigParam']);
        $oCfg->method('getConfigParam')->with('bl_perfLoadManufacturerTree')->willReturn(false);

        $oCTree = $this->getMock('stdClass', []);

        $oParent = $this->getMock('stdClass', ['getManufacturerTree']);
        $oParent->expects($this->never())->method('getManufacturerTree');

        $sClass = oxTestModules::addFunction('oxcmp_categories', '__set($name, $v)', '{$this->$name = $v;}');

        $o = $this->getMock($sClass, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $o->__oMoreCat = "more category";
        $o->setParent($oParent);
        $o->setCategoryTree($oCTree);

        $this->assertSame($oCTree, $o->render());
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     */
    public function testAddAdditionalParamsSearch()
    {
        $this->setRequestParameter("searchparam", "testSearchParam");
        $this->setRequestParameter("searchcnid", "testSearchCnid");
        $this->setRequestParameter("searchvendor", "testSearchVendor");
        $this->setRequestParameter("searchmanufacturer", "testSearchManufacturer");
        $this->setRequestParameter("listtype", "search");

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setListType", "setCategoryId"]);
        $oParent->expects($this->once())->method("setListType")->with('search');
        $oParent->expects($this->once())->method("setCategoryId")->with("testCatId");

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method("getParent")->willReturn($oParent);
        $this->assertSame("testCatId", $oCmp->addAdditionalParams(oxNew('oxarticle'), "testCatId", "testManId", "testVendorId"));
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     */
    public function testAddAdditionalParamsManufacturer()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadManufacturerTree', true);

        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchcnid", null);
        $this->setRequestParameter("searchvendor", null);
        $this->setRequestParameter("searchmanufacturer", null);
        $this->setRequestParameter("listtype", null);

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setListType", "setCategoryId"]);
        $oParent->expects($this->once())->method("setListType")->with('manufacturer');
        $oParent->expects($this->once())->method("setCategoryId")->with("testManId");

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getManufacturerId"]);
        $oProduct->expects($this->once())->method("getManufacturerId")->willReturn("testManId");

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method("getParent")->willReturn($oParent);
        $this->assertSame("testManId", $oCmp->addAdditionalParams($oProduct, "testCatId", "testManId", "testVendorId"));
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     */
    public function testAddAdditionalParamsVendor()
    {
        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchcnid", null);
        $this->setRequestParameter("searchvendor", null);
        $this->setRequestParameter("searchmanufacturer", null);
        $this->setRequestParameter("listtype", null);

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setListType", "setCategoryId"]);
        $oParent->expects($this->once())->method("setListType")->with('vendor');
        $oParent->expects($this->once())->method("setCategoryId")->with("v_testVendorId");

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getVendorId", "getManufacturerId"]);
        $oProduct->expects($this->once())->method("getVendorId")->willReturn("testVendorId");
        $oProduct->expects($this->once())->method("getManufacturerId")->willReturn("_testManId");

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method("getParent")->willReturn($oParent);
        $this->assertSame("v_testVendorId", $oCmp->addAdditionalParams($oProduct, "v_testVendorId", "testManId", "v_testVendorId"));
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     */
    public function testAddAdditionalParamsDefaultCat()
    {
        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchcnid", null);
        $this->setRequestParameter("searchvendor", null);
        $this->setRequestParameter("searchmanufacturer", null);
        $this->setRequestParameter("listtype", null);

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setListType", "setCategoryId"]);
        $oParent->expects($this->once())->method("setListType")->with(null);
        $oParent->expects($this->once())->method("setCategoryId")->with("testCatId");

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getCategoryIds"]);
        $oProduct->expects($this->once())->method("getCategoryIds")->willReturn(["testCatId"]);

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method("getParent")->willReturn($oParent);
        $this->assertSame("testCatId", $oCmp->addAdditionalParams($oProduct, null, null, null));
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     */
    public function testAddAdditionalParamsDefaultManufacturer()
    {
        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchcnid", null);
        $this->setRequestParameter("searchvendor", null);
        $this->setRequestParameter("searchmanufacturer", null);
        $this->setRequestParameter("listtype", null);

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setListType", "setCategoryId"]);
        $oParent->expects($this->once())->method("setListType")->with('manufacturer');
        $oParent->expects($this->once())->method("setCategoryId")->with("testManId");

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getCategoryIds", "getManufacturerId"]);
        $oProduct->expects($this->once())->method("getCategoryIds")->willReturn(false);
        $oProduct->expects($this->once())->method("getManufacturerId")->willReturn("testManId");

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method("getParent")->willReturn($oParent);
        $this->assertSame("testManId", $oCmp->addAdditionalParams($oProduct, null, null, null));
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     */
    public function testAddAdditionalParamsDefaultVendor()
    {
        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchcnid", null);
        $this->setRequestParameter("searchvendor", null);
        $this->setRequestParameter("searchmanufacturer", null);
        $this->setRequestParameter("listtype", null);

        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setListType", "setCategoryId"]);
        $oParent->expects($this->once())->method("setListType")->with('vendor');
        $oParent->expects($this->once())->method("setCategoryId")->with("testVendorId");

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getCategoryIds", "getManufacturerId", "getVendorId"]);
        $oProduct->expects($this->once())->method("getCategoryIds")->willReturn(false);
        $oProduct->expects($this->once())->method("getManufacturerId")->willReturn(false);
        $oProduct->expects($this->once())->method("getVendorId")->willReturn("testVendorId");

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\CategoriesComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method("getParent")->willReturn($oParent);
        $this->assertSame("testVendorId", $oCmp->addAdditionalParams($oProduct, null, null, null));
    }
}
