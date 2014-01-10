<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: oxcmpBasketTest.php 25500 2010-02-01 23:12:31Z alfonsas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Views_oxcmpCategoriesTest extends OxidTestCase
{
    public static $oCL = null;

    public function tearDown()
    {
        self::$oCL = null;
        parent::tearDown();
    }

    public function testInitReturnsInOrderStep()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(true));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDisableNavBars'))->will($this->returnValue(true));
        $oCfg->expects($this->once())->method('getActiveView')->will($this->returnValue($oActView));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig'));
        $o->expects($this->never())->method('_getActCat');
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $o->init();
    }

    public function testInitReturnsInOrderStepCfgOff()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->never())->method('getIsOrderStep')->will($this->returnValue(true));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDisableNavBars'))->will($this->returnValue(false));
        $oCfg->expects($this->never())->method('getActiveView')->will($this->returnValue($oActView));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig'));
        $o->expects($this->once())->method('_getActCat')->will($this->throwException(new Exception("passed: OK")));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        try {
            $o->init();
        } catch (Exception $e) {
            $this->assertEquals("passed: OK", $e->getMessage());
            return;
        }
        $this->fail("no exception is thrown");
    }

    public function testInitReturnsNoOrderStep()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(false));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDisableNavBars'))->will($this->returnValue(true));
        $oCfg->expects($this->once())->method('getActiveView')->will($this->returnValue($oActView));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig'));
        $o->expects($this->once())->method('_getActCat')->will($this->throwException(new Exception("passed: OK")));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        try {
            $o->init();
        } catch (Exception $e) {
            $this->assertEquals("passed: OK", $e->getMessage());
            return;
        }
        $this->fail("no exception is thrown");
    }

    public function testInitLoadVendorTree()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(false));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('blDisableNavBars'))     ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getActiveView'  )->will($this->returnValue($oActView));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree'))->will( $this->returnValue( true ));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig', '_loadVendorTree'));
        $o->expects($this->once())->method('_getActCat')->will($this->returnValue("actcat.."));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->once())->method('_loadVendorTree')->with($this->equalTo("actcat.."))->will($this->throwException(new Exception("passed: OK")));

        try {
            $o->init();
        } catch (Exception $e) {
            $this->assertEquals("passed: OK", $e->getMessage());
            return;
        }
        $this->fail("no exception is thrown");
    }


    public function testInitLoadManufacturerTree()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(false));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('blDisableNavBars'))     ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getActiveView'  )->will($this->returnValue($oActView));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree'))->will( $this->returnValue( true ));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig', '_loadVendorTree', '_loadManufacturerTree'));
        $o->expects($this->once())->method('_getActCat')->will($this->returnValue("actcat.."));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->never())->method('_loadVendorTree');
        $o->expects($this->once())->method('_loadManufacturerTree')->with($this->equalTo("manid"))->will($this->throwException(new Exception("passed: OK")));

        modConfig::setParameter('mnid', 'manid');
        try {
            $o->init();
        } catch (Exception $e) {
            $this->assertEquals("passed: OK", $e->getMessage());
            return;
        }
        $this->fail("no exception is thrown");
    }


    public function testInitLoadCategoryTree()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(false));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('blDisableNavBars'))     ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getActiveView'  )->will($this->returnValue($oActView));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 4 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree'))->will( $this->returnValue( true ));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig', '_loadVendorTree', '_loadManufacturerTree', '_loadCategoryTree'));
        $o->expects($this->once())->method('_getActCat')->will($this->returnValue("actcat.."));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->never())->method('_loadVendorTree');
        $o->expects($this->never())->method('_loadManufacturerTree');
        $o->expects($this->once())->method('_loadCategoryTree')->with($this->equalTo("actcat.."))->will($this->throwException(new Exception("passed: OK")));

        try {
            $o->init();
        } catch (Exception $e) {
            $this->assertEquals("passed: OK", $e->getMessage());
            return;
        }
        $this->fail("no exception is thrown");
    }


    public function testInitChecksTopNaviConfigParamAndSkipsGetMoreCat()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(false));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('blDisableNavBars'))     ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getActiveView'  )->will($this->returnValue($oActView));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 4 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree'))->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 5 ) )->method( 'getConfigParam' )->with($this->equalTo('blTopNaviLayout'))->will( $this->returnValue( false ));

        $o = $this->getMock('oxcmp_categories', array('_getActCat', 'getConfig', '_loadVendorTree', '_loadManufacturerTree', '_loadCategoryTree', '_getMoreCategory'));
        $o->expects($this->once())->method('_getActCat')->will($this->returnValue("actcat.."));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->never())->method('_loadVendorTree');
        $o->expects($this->never())->method('_loadManufacturerTree');
        $o->expects($this->once())->method('_loadCategoryTree')->with($this->equalTo("actcat.."));
        $o->expects($this->never())->method('_getMoreCategory');

        $o->init();
    }

    public function testInitChecksTopNaviConfigParamAndInvokesGetMoreCat()
    {
        $oActView = $this->getMock('stdClass', array('getIsOrderStep'));
        $oActView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(false));

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getActiveView'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('blDisableNavBars'))     ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getActiveView'  )->will($this->returnValue($oActView));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree'))->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 4 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree'))->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 5 ) )->method( 'getConfigParam' )->with($this->equalTo('blTopNaviLayout'))->will( $this->returnValue( true ));

        $sClass = oxTestModules::addFunction('oxcmp_categories', '__get($name)', '{$name = str_replace("UNIT_", "_", $name); return $this->$name; }');

        $o = $this->getMock($sClass, array('_getActCat', 'getConfig', '_loadVendorTree', '_loadManufacturerTree', '_loadCategoryTree', '_getMoreCategory'));
        $o->expects($this->once())->method('_getActCat')->will($this->returnValue("actcat.."));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->never())->method('_loadVendorTree');
        $o->expects($this->never())->method('_loadManufacturerTree');
        $o->expects($this->once())->method('_loadCategoryTree')->with($this->equalTo("actcat.."));
        $o->expects($this->once())->method('_getMoreCategory')->with($this->equalTo("actcat.."), $this->equalTo("oxcidval"))->will($this->returnValue("more cat ret"));

        modConfig::setParameter('oxcid', 'oxcidval');
        $o->init();
        $this->assertEquals("more cat ret", $o->UNIT_oMoreCat);
    }



    public function testGetProductNoAnid()
    {
        $oParent = $this->getMock('stdClass', array('getViewProduct'));
        $oParent->expects($this->never())->method('getViewProduct')->will($this->returnValue(false));

        $o = $this->getMock('oxcmp_categories', array());
        $o->setParent($oParent);

        modConfig::setParameter( 'anid', '' );

        $this->assertSame(null, $o->getProduct());
    }

    public function testGetProductWithAnidAndGetViewProduct()
    {
        modConfig::setParameter( 'anid', 'lalala' );

        $oParent = $this->getMock('stdClass', array('getViewProduct'));
        $oParent->expects($this->once())->method('getViewProduct')->will($this->returnValue('asd'));

        $o = new oxcmp_categories;
        $o->setParent($oParent);

        $this->assertEquals('asd', $o->getProduct());
    }

    public function testGetProductWithAnidLoadsArticle()
    {
        modConfig::setParameter( 'anid', 'lalala' );

        oxTestModules::addFunction('oxarticle', 'load($id)', '{$this->setId($id); return "lalala" == $id;}');

        $oExpectArticle = oxNew('oxarticle');
        $this->assertEquals(true, $oExpectArticle->load('lalala'));

        $oParent = $this->getMock('stdClass', array('getViewProduct', 'setViewProduct'));
        $oParent->expects($this->once())->method('getViewProduct')->will($this->returnValue(null));
        $oParent->expects($this->once())->method('setViewProduct')->with($this->equalTo($oExpectArticle))->will($this->returnValue(null));

        $o = new oxcmp_categories;
        $o->setParent($oParent);

        $this->assertEquals('lalala', $o->getProduct()->getId());
    }

    public function testGetProductWithAnidLoadArticleFails()
    {
        modConfig::setParameter( 'anid', 'blah' );

        oxTestModules::addFunction('oxarticle', 'load($id)', '{$this->setId($id); return "lalala" == $id;}');

        $oParent = $this->getMock('stdClass', array('getViewProduct', 'setViewProduct'));
        $oParent->expects($this->once())->method('getViewProduct')->will($this->returnValue(null));
        $oParent->expects($this->never())->method('setViewProduct');

        $o = new oxcmp_categories;
        $o->setParent($oParent);

        $this->assertSame(null, $o->getProduct());
    }

    public function testGetActCatLoadDefault()
    {
        $oActShop = new stdClass;
        $oActShop->oxshops__oxdefcat = new oxField('default category');

        $oCfg = $this->getMock('stdClass', array('getActiveShop'));
        $oCfg->expects( $this->once() )->method( 'getActiveShop' )->will( $this->returnValue( $oActShop ));

        $o = $this->getMock('oxcmp_categories', array('getConfig', 'getProduct', '_addAdditionalParams'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->once())->method('getProduct')->will($this->returnValue(null));
        $o->expects($this->never())->method('_addAdditionalParams');

        modConfig::setParameter('oxcid', null);
        modConfig::setParameter('tpl', null);
        modConfig::setParameter('mnid', null);
        modConfig::setParameter('searchtag', null);
        modConfig::setParameter('cnid', null);

        $this->assertEquals('default category', $o->UNITgetActCat());
    }

    public function testGetActCatLoadDefaultoxroot()
    {
        $oActShop = new stdClass;
        $oActShop->oxshops__oxdefcat = new oxField('oxrootid');

        $oCfg = $this->getMock('stdClass', array('getActiveShop'));
        $oCfg->expects( $this->once() )->method( 'getActiveShop' )->will( $this->returnValue( $oActShop ));

        $o = $this->getMock('oxcmp_categories', array('getConfig', 'getProduct', '_addAdditionalParams'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->once())->method('getProduct')->will($this->returnValue(null));
        $o->expects($this->never())->method('_addAdditionalParams');

        modConfig::setParameter('oxcid', null);
        modConfig::setParameter('tpl', null);
        modConfig::setParameter('mnid', null);
        modConfig::setParameter('searchtag', null);
        modConfig::setParameter('cnid', null);

        $this->assertSame(null, $o->UNITgetActCat());
    }

    public function testGetActCatWithProduct()
    {
        $o = $this->getMock('oxcmp_categories', array( 'getProduct', '_addAdditionalParams'));
        $o->expects($this->once())->method('getProduct')->will($this->returnValue("product"));
        $o->expects($this->once())->method('_addAdditionalParams')->with(
                    $this->equalTo("product"),
                    $this->equalTo(null),
                    $this->equalTo('mnid'),
                    $this->equalTo('oxcid'),
                    $this->equalTo('searchtag')
                );

        modConfig::setParameter('oxcid', 'oxcid');
        modConfig::setParameter('tpl', 'tpl');
        modConfig::setParameter('mnid', 'mnid');
        modConfig::setParameter('searchtag', 'searchtag');
        modConfig::setParameter('cnid', 'cnid');

        $this->assertSame(null, $o->UNITgetActCat());
    }

    public function testGetActCatWithProductAltBranches()
    {
        $o = $this->getMock('oxcmp_categories', array( 'getProduct', '_addAdditionalParams'));
        $o->expects($this->once())->method('getProduct')->will($this->returnValue("product"));
        $o->expects($this->once())->method('_addAdditionalParams')->with(
                    $this->equalTo("product"),
                    $this->equalTo("cnid"),
                    $this->equalTo(''),
                    $this->equalTo('tpl'),
                    $this->equalTo('searchtag')
                );

        modConfig::setParameter('oxcid', '');
        modConfig::setParameter('tpl', 'tpl');
        modConfig::setParameter('mnid', '');
        modConfig::setParameter('searchtag', 'searchtag');
        modConfig::setParameter('cnid', 'cnid');

        $this->assertSame(null, $o->UNITgetActCat());
    }

    public function testLoadCategoryTreeIsNotNeeded()
    {
        self::$oCL = $this->getMock('stdclass', array('buildTree'));
        self::$oCL->expects($this->never())->method('buildTree');

        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl)', '{if ("oxcategorylist" == $cl) return Unit_Views_oxcmpCategoriesTest::$oCL; return parent::oxNew($cl);}');

        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree'))     ->will( $this->returnValue( false ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertNull($o->UNITloadCategoryTree("act cat"));
    }

    public function testLoadCategoryTree()
    {
        self::$oCL = $this->getMock('stdclass', array('buildTree', 'getClickCat'));
        self::$oCL->expects($this->once())->method('buildTree')
                ->with(
                        $this->equalTo('act cat'),
                        $this->equalTo('passitthru1'),
                        $this->equalTo('passitthru2'),
                        $this->equalTo('passitthru3')
                    );
        self::$oCL->expects($this->once())->method('getClickCat')->will($this->returnValue("returned click category"));

        $oParent = $this->getMock('stdclass', array('setCategoryTree', 'setActCategory'));
        $oParent->expects($this->once())->method('setCategoryTree')
                ->with(
                        $this->equalTo(self::$oCL)
                    );
        $oParent->expects($this->once())->method('setActCategory')
                ->with(
                        $this->equalTo("returned click category")
                    );

        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl)', '{if ("oxcategorylist" == $cl) return Unit_Views_oxcmpCategoriesTest::$oCL; return parent::oxNew($cl);}');

        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree'))       ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('blLoadFullTree'))           ->will( $this->returnValue( "passitthru1" ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadTreeForSearch')) ->will( $this->returnValue( "passitthru2" ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('blTopNaviLayout'))          ->will( $this->returnValue( "passitthru3" ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $o->setParent($oParent);

        $this->assertNull($o->UNITloadCategoryTree("act cat"));
    }










    public function testLoadVendorTreeIsNotNeeded()
    {
        self::$oCL = $this->getMock('stdclass', array('buildVendorTree'));
        self::$oCL->expects($this->never())->method('buildVendorTree');

        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl)', '{if ("oxvendorlist" == $cl) return Unit_Views_oxcmpCategoriesTest::$oCL; return parent::oxNew($cl);}');

        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree'))     ->will( $this->returnValue( false ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertNull($o->UNITloadVendorTree("act vend"));
    }

    public function testLoadVendorTree()
    {
        self::$oCL = $this->getMock('stdclass', array('buildVendorTree', 'getClickVendor'));
        self::$oCL->expects($this->once())->method('buildVendorTree')
                ->with(
                        $this->equalTo('vendorlist'),
                        $this->equalTo('act vend'),
                        $this->equalTo('passitthru1')
                    );
        self::$oCL->expects($this->once())->method('getClickVendor')->will($this->returnValue("returned click vendor"));

        $oParent = $this->getMock('stdclass', array('setVendorTree', 'setActVendor'));
        $oParent->expects($this->once())->method('setVendorTree')
                ->with(
                        $this->equalTo(self::$oCL)
                    );
        $oParent->expects($this->once())->method('setActVendor')
                ->with(
                        $this->equalTo("returned click vendor")
                    );

        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl)', '{if ("oxvendorlist" == $cl) return Unit_Views_oxcmpCategoriesTest::$oCL; return parent::oxNew($cl);}');

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getShopHomeURL'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getShopHomeURL' )                                                ->will( $this->returnValue( "passitthru1" ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $o->setParent($oParent);

        $this->assertNull($o->UNITloadVendorTree("act vend"));
    }









    public function testLoadManufacturerTreeIsNotNeeded()
    {
        self::$oCL = $this->getMock('stdclass', array('buildVendorTree'));
        self::$oCL->expects($this->never())->method('buildVendorTree');

        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl)', '{if ("oxmanufacturerlist" == $cl) return Unit_Views_oxcmpCategoriesTest::$oCL; return parent::oxNew($cl);}');

        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree'))     ->will( $this->returnValue( false ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertNull($o->UNITloadManufacturerTree("act Manufacturer"));
    }

    public function testLoadManufacturerTree()
    {
        self::$oCL = $this->getMock('stdclass', array('buildManufacturerTree', 'getClickManufacturer'));
        self::$oCL->expects($this->once())->method('buildManufacturerTree')
                ->with(
                        $this->equalTo('manufacturerlist'),
                        $this->equalTo('act Manufacturer'),
                        $this->equalTo('passitthru1')
                    );
        self::$oCL->expects($this->once())->method('getClickManufacturer')->will($this->returnValue("returned click Manufacturer"));

        $oParent = $this->getMock('stdclass', array('setManufacturerTree', 'setActManufacturer'));
        $oParent->expects($this->once())->method('setManufacturerTree')
                ->with(
                        $this->equalTo(self::$oCL)
                    );
        $oParent->expects($this->once())->method('setActManufacturer')
                ->with(
                        $this->equalTo("returned click Manufacturer")
                    );

        oxTestModules::addFunction('oxUtilsObject', 'oxNew($cl)', '{if ("oxmanufacturerlist" == $cl) return Unit_Views_oxcmpCategoriesTest::$oCL; return parent::oxNew($cl);}');

        $oCfg = $this->getMock('stdClass', array('getConfigParam', 'getShopHomeURL'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getShopHomeURL' )                                                ->will( $this->returnValue( "passitthru1" ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $o->setParent($oParent);

        $this->assertNull($o->UNITloadManufacturerTree("act Manufacturer"));
    }






    public function testRenderEverythingOff()
    {
        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree')) ->will( $this->returnValue( false ));

        $oParent = $this->getMock('stdClass', array('getVendorTree', 'getManufacturerTree', 'getCategoryTree'));
        $oParent->expects( $this->never() )->method( 'getVendorTree' );
        $oParent->expects( $this->never() )->method( 'getManufacturerTree' );
        $oParent->expects( $this->never() )->method( 'getCategoryTree' );

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->setParent($oParent);

        $this->assertNull($o->render());
    }

    public function testRenderVendorList()
    {
        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree')) ->will( $this->returnValue( false ));

        $oVTree  = $this->getMock('stdClass', array('getRootCat'));
        $oVTree->expects( $this->at(0) )->method( 'getRootCat' )->will($this->returnValue("root vendor cat"));

        $oParent = $this->getMock('stdClass', array('getVendorTree', 'getManufacturerTree', 'getCategoryTree', 'setVendorlist', 'setRootVendor' ));
        $oParent->expects( $this->at(0) )->method( 'getVendorTree' )->will($this->returnValue($oVTree));
        $oParent->expects( $this->never() )->method( 'getManufacturerTree' );
        $oParent->expects( $this->never() )->method( 'getCategoryTree' );
        $oParent->expects( $this->once() )->method( 'setVendorlist' )->with($this->equalTo($oVTree));
        $oParent->expects( $this->once() )->method( 'setRootVendor' )->with($this->equalTo("root vendor cat"));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->setParent($oParent);

        $this->assertNull($o->render());
    }

    public function testRenderMenufactList()
    {
        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree')) ->will( $this->returnValue( false ));

        $oMTree  = $this->getMock('stdClass', array('getRootCat'));
        $oMTree->expects( $this->at(0) )->method( 'getRootCat' )->will($this->returnValue("root Manufacturer cat"));

        $oParent = $this->getMock('stdClass', array('getVendorTree', 'getManufacturerTree', 'getCategoryTree', 'setManufacturerlist', 'setRootManufacturer' ));
        $oParent->expects( $this->never() )->method( 'getVendorTree' );
        $oParent->expects( $this->at(0) )->method( 'getManufacturerTree' )->will($this->returnValue($oMTree));
        $oParent->expects( $this->never() )->method( 'getCategoryTree' );
        $oParent->expects( $this->once() )->method( 'setManufacturerlist' )->with($this->equalTo($oMTree));
        $oParent->expects( $this->once() )->method( 'setRootManufacturer' )->with($this->equalTo("root Manufacturer cat"));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->setParent($oParent);

        $this->assertNull($o->render());
    }

    public function testRenderCategoryList()
    {
        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadTreeForSearch')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 4 ) )->method( 'getConfigParam' )->with($this->equalTo('blTopNaviLayout')) ->will( $this->returnValue( false ));

        $oCTree  = $this->getMock('stdClass', array());

        $oParent = $this->getMock('stdClass', array('getVendorTree', 'getManufacturerTree', 'getCategoryTree'));
        $oParent->expects( $this->never() )->method( 'getVendorTree' );
        $oParent->expects( $this->never() )->method( 'getManufacturerTree' );
        $oParent->expects( $this->at(0) )->method( 'getCategoryTree' )->will($this->returnValue($oCTree));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->setParent($oParent);

        $this->assertSame($oCTree, $o->render());
    }

    public function testRenderCategoryListLoadSearch()
    {
        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadTreeForSearch')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 4 ) )->method( 'getConfigParam' )->with($this->equalTo('blTopNaviLayout')) ->will( $this->returnValue( false ));

        $oCTree  = $this->getMock('stdClass', array());

        $oParent = $this->getMock('stdClass', array('getVendorTree', 'getManufacturerTree', 'getCategoryTree', 'setSearchCatTree'));
        $oParent->expects( $this->never() )->method( 'getVendorTree' );
        $oParent->expects( $this->never() )->method( 'getManufacturerTree' );
        $oParent->expects( $this->at(0) )->method( 'getCategoryTree' )->will($this->returnValue($oCTree));
        $oParent->expects( $this->at(1) )->method( 'setSearchCatTree' )->with($this->equalTo($oCTree));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->setParent($oParent);

        $this->assertSame($oCTree, $o->render());
    }

    public function testRenderCategoryListTopNavi()
    {
        $oCfg = $this->getMock('stdClass', array('getConfigParam'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadVendorTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadManufacturerTree')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 2 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadCatTree')) ->will( $this->returnValue( true ));
        $oCfg->expects( $this->at( 3 ) )->method( 'getConfigParam' )->with($this->equalTo('bl_perfLoadTreeForSearch')) ->will( $this->returnValue( false ));
        $oCfg->expects( $this->at( 4 ) )->method( 'getConfigParam' )->with($this->equalTo('blTopNaviLayout')) ->will( $this->returnValue( true ));

        $oCTree  = $this->getMock('stdClass', array());

        $oParent = $this->getMock('stdClass', array('getVendorTree', 'getManufacturerTree', 'getCategoryTree', 'setCatMore' ));
        $oParent->expects( $this->never() )->method( 'getVendorTree' );
        $oParent->expects( $this->never() )->method( 'getManufacturerTree' );
        $oParent->expects( $this->at(0) )->method( 'getCategoryTree' )->will($this->returnValue($oCTree));
        $oParent->expects( $this->at(1) )->method( 'setCatMore' )->with($this->equalTo("more category"));

        $sClass = oxTestModules::addFunction('oxcmp_categories', '__set($name, $v)', '{$name = str_replace("UNIT_", "_", $name); $this->$name = $v; }');

        $o = $this->getMock($sClass, array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $o->UNIT_oMoreCat = "more category";
        $o->setParent($oParent);

        $this->assertSame($oCTree, $o->render());
    }





    public function testGetMoreCategoryoxMore()
    {
        $oCfg = $this->getMock('stdClass', array('getShopHomeURL'));
        $oCfg->expects( $this->at( 0 ) )->method( 'getShopHomeURL' )->will( $this->returnValue( "myshophomeurl" ));

        $o = $this->getMock('oxcmp_categories', array('getConfig'));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oMoreCat = $o->UNITgetMoreCategory('oxmore', '');
        $this->assertTrue($oMoreCat instanceof oxStdClass);
        $this->assertEquals('myshophomeurlcnid=oxmore', $oMoreCat->closelink);
        $this->assertEquals(true, $oMoreCat->expanded);
    }

    /**
     * Testing oxcmp_categories::_getMoreCategory()
     *
     * @return null
     */
    public function testGetMoreCategoryCategoryExpanded()
    {
        $oTestMoreCat = new oxStdClass();
        $oTestMoreCat->closelink = $oTestMoreCat->openlink =oxConfig::getInstance()->getShopHomeURL().'cnid=oxmore';
        $oTestMoreCat->expanded  = true;

        modConfig::getInstance()->setConfigParam( 'iTopNaviCatCount', 1 );

        $oCat1 = $this->getMock( "oxStdClass", array( "getContentCats", "getExpanded" ) );
        $oCat1->expects( $this->once() )->method( 'getContentCats' )->will( $this->returnValue( false ) );
        $oCat1->expects( $this->once() )->method( 'getExpanded' )->will( $this->returnValue( false ) );

        $oCat2 = $this->getMock( "oxStdClass", array( "getContentCats", "getExpanded" ) );
        $oCat2->expects( $this->once() )->method( 'getContentCats' )->will( $this->returnValue( false ) );
        $oCat2->expects( $this->once() )->method( 'getExpanded' )->will( $this->returnValue( true ) );

        $oParent = $this->getMock( "oxStdClass", array( "getCategoryTree" ) );
        $oParent->expects( $this->once() )->method( 'getCategoryTree' )->will( $this->returnValue( array( $oCat1, $oCat2 ) ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once() )->method( 'getParent' )->will( $this->returnValue( $oParent ) );
        $oMoreCat = $oCmp->UNITgetMoreCategory( null, "testContId" );
        $this->assertEquals( $oTestMoreCat, $oMoreCat );
    }

    /**
     * Testing oxcmp_categories::_getMoreCategory()
     *
     * @return null
     */
    public function testGetMoreCategoryContentExpanded()
    {
        $oTestMoreCat = new oxStdClass();
        $oTestMoreCat->closelink = $oTestMoreCat->openlink =oxConfig::getInstance()->getShopHomeURL().'cnid=oxmore';
        $oTestMoreCat->expanded  = true;

        modConfig::getInstance()->setConfigParam( 'iTopNaviCatCount', 1 );

        $oContent1 = $this->getMock( "oxStdClass", array( "getId" ) );
        $oContent1->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( "testContId2" ) );

        $oContent2 = $this->getMock( "oxStdClass", array( "getId" ) );
        $oContent2->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( "testContId" ) );

        $oCat1 = $this->getMock( "oxStdClass", array( "getContentCats", "getExpanded" ) );
        $oCat1->expects( $this->once() )->method( 'getContentCats' )->will( $this->returnValue( false ) );
        $oCat1->expects( $this->once() )->method( 'getExpanded' )->will( $this->returnValue( false ) );

        $oCat2 = $this->getMock( "oxStdClass", array( "getContentCats", "getExpanded" ) );
        $oCat2->expects( $this->once() )->method( 'getContentCats' )->will( $this->returnValue( array( $oContent1, $oContent2 ) ) );
        $oCat2->expects( $this->never() )->method( 'getExpanded' );

        $oParent = $this->getMock( "oxStdClass", array( "getCategoryTree" ) );
        $oParent->expects( $this->once() )->method( 'getCategoryTree' )->will( $this->returnValue( array( $oCat1, $oCat2 ) ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once() )->method( 'getParent' )->will( $this->returnValue( $oParent ) );
        $oMoreCat = $oCmp->UNITgetMoreCategory( null, "testContId" );
        $this->assertEquals( $oTestMoreCat, $oMoreCat );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsSearch()
    {
        modConfig::setParameter( "searchparam", "testSearchParam" );
        modConfig::setParameter( "searchcnid", "testSearchCnid" );
        modConfig::setParameter( "searchvendor", "testSearchVendor" );
        modConfig::setParameter( "searchmanufacturer", "testSearchManufacturer" );
        modConfig::setParameter( "listtype", "search" );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( 'search' ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "testCatId" ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "testCatId", $oCmp->UNITaddAdditionalParams( new oxarticle, "testCatId", "testManId", "testContId", "testTag", "testVendorId" ) );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsManufacturer()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadManufacturerTree', true );

        modConfig::setParameter( "searchparam", null );
        modConfig::setParameter( "searchcnid", null );
        modConfig::setParameter( "searchvendor", null );
        modConfig::setParameter( "searchmanufacturer", null );
        modConfig::setParameter( "listtype", null );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( 'manufacturer' ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "testManId" ) );

        $oProduct = $this->getMock( "oxStdClass", array( "getManufacturerId" ) );
        $oProduct->expects( $this->once())->method( "getManufacturerId" )->will( $this->returnValue( "testManId" ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "testManId", $oCmp->UNITaddAdditionalParams( $oProduct, "testCatId", "testManId", "testContId", "testTag", "testVendorId" ) );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsVendor()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadVendorTree', true );

        modConfig::setParameter( "searchparam", null );
        modConfig::setParameter( "searchcnid", null );
        modConfig::setParameter( "searchvendor", null );
        modConfig::setParameter( "searchmanufacturer", null );
        modConfig::setParameter( "listtype", null );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( 'vendor' ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "v_testVendorId" ) );

        $oProduct = $this->getMock( "oxStdClass", array( "getVendorId", "getManufacturerId" ) );
        $oProduct->expects( $this->once())->method( "getVendorId" )->will( $this->returnValue( "testVendorId" ) );
        $oProduct->expects( $this->once())->method( "getManufacturerId" )->will( $this->returnValue( "_testManId" ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "v_testVendorId", $oCmp->UNITaddAdditionalParams( $oProduct, "v_testVendorId", "testManId", "testContId", "testTag", "v_testVendorId" ) );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsTag()
    {
        modConfig::setParameter( "searchparam", null );
        modConfig::setParameter( "searchcnid", null );
        modConfig::setParameter( "searchvendor", null );
        modConfig::setParameter( "searchmanufacturer", null );
        modConfig::setParameter( "listtype", null );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( 'tag' ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "testCatId" ) );

        $oProduct = $this->getMock( "oxStdClass", array( "getVendorId", "getManufacturerId" ) );
        $oProduct->expects( $this->any())->method( "getVendorId" )->will( $this->returnValue( "_testVendorId" ) );
        $oProduct->expects( $this->any())->method( "getManufacturerId" )->will( $this->returnValue( "_testManId" ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "testCatId", $oCmp->UNITaddAdditionalParams( $oProduct, "testCatId", "testManId", "testContId", "testTag", "testVendorId" ) );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsDefaultCat()
    {
        modConfig::setParameter( "searchparam", null );
        modConfig::setParameter( "searchcnid", null );
        modConfig::setParameter( "searchvendor", null );
        modConfig::setParameter( "searchmanufacturer", null );
        modConfig::setParameter( "listtype", null );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( null ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "testCatId" ) );

        $oProduct = $this->getMock( "oxStdClass", array( "getCategoryIds" ) );
        $oProduct->expects( $this->once())->method( "getCategoryIds" )->will( $this->returnValue( array( "testCatId" ) ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "testCatId", $oCmp->UNITaddAdditionalParams( $oProduct, null, null, null, null, null ) );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsDefaultManufacturer()
    {
        modConfig::setParameter( "searchparam", null );
        modConfig::setParameter( "searchcnid", null );
        modConfig::setParameter( "searchvendor", null );
        modConfig::setParameter( "searchmanufacturer", null );
        modConfig::setParameter( "listtype", null );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( 'manufacturer' ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "testManId" ) );

        $oProduct = $this->getMock( "oxStdClass", array( "getCategoryIds", "getManufacturerId" ) );
        $oProduct->expects( $this->once())->method( "getCategoryIds" )->will( $this->returnValue( false ) );
        $oProduct->expects( $this->once())->method( "getManufacturerId" )->will( $this->returnValue( "testManId" ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "testManId", $oCmp->UNITaddAdditionalParams( $oProduct, null, null, null, null, null ) );
    }

    /**
     * Testing oxcmp_categories::_addAdditionalParams()
     *
     * @return null
     */
    public function testAddAdditionalParamsDefaultVendor()
    {
        modConfig::setParameter( "searchparam", null );
        modConfig::setParameter( "searchcnid", null );
        modConfig::setParameter( "searchvendor", null );
        modConfig::setParameter( "searchmanufacturer", null );
        modConfig::setParameter( "listtype", null );

        $oParent = $this->getMock( "oxStdClass", array( "setListType", "setCategoryId" ) );
        $oParent->expects( $this->once())->method( "setListType" )->with( $this->equalTo( 'vendor' ) );
        $oParent->expects( $this->once())->method( "setCategoryId" )->with( $this->equalTo( "testVendorId" ) );

        $oProduct = $this->getMock( "oxStdClass", array( "getCategoryIds", "getManufacturerId", "getVendorId" ) );
        $oProduct->expects( $this->once())->method( "getCategoryIds" )->will( $this->returnValue( false ) );
        $oProduct->expects( $this->once())->method( "getManufacturerId" )->will( $this->returnValue( false ) );
        $oProduct->expects( $this->once())->method( "getVendorId" )->will( $this->returnValue( "testVendorId" ) );

        $oCmp = $this->getMock( "oxcmp_categories", array( "getParent" ) );
        $oCmp->expects( $this->once())->method( "getParent" )->will( $this->returnValue( $oParent ) );
        $this->assertEquals( "testVendorId", $oCmp->UNITaddAdditionalParams( $oProduct, null, null, null, null, null ) );
    }
}
