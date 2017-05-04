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
 * Test oxCategoryList module
 */
class testOxCategoryList extends oxCategoryList
{

    protected $_testAdmin = false;

    /**
     * Get private field value.
     *
     * @param string $sName Field name
     *
     * @return mixed
     */
    public function getVar($sName)
    {
        return $this->{'_' . $sName};
    }

    /**
     * Set private field value.
     *
     * @param string $sName  Field name
     * @param string $sValue Field value
     *
     * @return null
     */
    public function setVar($sName, $sValue)
    {
        $this->{'_' . $sName} = $sValue;
    }

    /**
     * Override isAdmin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->_testAdmin;
    }
}

/**
 * Test oxContentList module
 */
class modContentList_oxcategorylist extends oxContentList
{

    /**
     * Test loadCatMenues.
     *
     * @return bool
     */
    public function loadCatMenues()
    {
        $sActCat = '8a142c3e44ea4e714.31136811';


        $oContent1 = oxNew('Content');
        $oContent2 = clone $oContent1;
        $aResult = array($sActCat => array($oContent1, $oContent2));
        $this->assign($aResult);
    }
}

/**
 * Testing oxCategoryList class
 */
class Unit_Core_oxCategoryListTest extends OxidTestCase
{

    protected $_oList = null;
    protected $_sNoCat;
    protected $_sActCat;
    protected $_sActRoot;
    protected $_aActPath;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oList = oxNew('testOxCategoryList');
        $this->_sNoCat = '_no_such_cat_';

        $this->_sActCat = '8a142c3e44ea4e714.31136811';
        $this->_sActRoot = '8a142c3e4143562a5.46426637';
        $this->_aActPath = array($this->_sActRoot, $this->_sActCat);


        $this->testAdmin = false;
        $this->cleanUpTable('oxcategories');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxcategories');
        oxRemClassModule('modContentList_oxcategorylist');
        parent::tearDown();
    }

    /**
     * Test get Depth Sql Snippet expand level 0.
     *
     * @return null
     */
    public function test_getDepthSqlSnippet_level0()
    {
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSnippet = $this->_oList->UNITgetDepthSqlSnippet(null);
        $sExpSnippet = ' ( 0 ) ';
        $this->assertEquals($sExpSnippet, $sCurSnippet);
    }

    /**
     * Test get Depth Sql Snippet expand level 1.
     *
     * @return null
     */
    public function test_getDepthSqlSnippet_level1()
    {
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 1);

        $sCurSnippet = $this->_oList->UNITgetDepthSqlSnippet(null);

        $sViewName = getViewName('oxcategories');

        $sExpSnippet = " ( 0 or $sViewName.oxparentid = 'oxrootid' ) ";

        $this->assertEquals($sExpSnippet, $sCurSnippet);
    }

    /**
     * Test get Depth Sql Snippet expand level 2.
     *
     * @return null
     */
    public function test_getDepthSqlSnippet_level2()
    {
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 2);

        $sCurSnippet = $this->_oList->UNITgetDepthSqlSnippet(null);

        $sViewName = getViewName('oxcategories');
        $sExpSnippet = " ( 0 or $sViewName.oxparentid = 'oxrootid' or $sViewName.oxrootid = $sViewName.oxparentid or $sViewName.oxid = $sViewName.oxrootid ) ";

        $this->assertEquals($sExpSnippet, $sCurSnippet);
    }

    /**
     * Test get Depth Sql Snippet expand actice category.
     *
     * @return null
     */
    public function test_getDepthSqlSnippet_actcat()
    {
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $oCat = new oxcategory();
        $oCat->load($this->_sActCat);
        $sCurSnippet = $this->_oList->UNITgetDepthSqlSnippet($oCat);

        $sViewName = getViewName('oxcategories');
        $sExpSnippet = " ( 0 or ($sViewName.oxparentid = '8a142c3e44ea4e714.31136811') ) ";

        $this->assertEquals($sExpSnippet, $sCurSnippet);
    }

    /**
     * Test get Depth Sql Snippet expand non existing actvice category.
     *
     * @return null
     */
    public function test_getDepthSqlSnippet_badactcat()
    {
        $this->_oList->setVar('sActCat', $this->_sNoCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSnippet = $this->_oList->UNITgetDepthSqlSnippet(null);
        $sExpSnippet = " ( 0 ) ";
        $this->assertEquals($sExpSnippet, $sCurSnippet);
    }

    /**
     * Test get select string sorting.
     *
     * @return null
     */
    public function test_getSelectString_order()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSql = $this->_oList->UNITgetSelectString();

        $sExpSql = "order by oxrootid asc, oxleft asc";

        $this->assertContains($sExpSql, $sCurSql);
    }

    /**
     * Test get select string hiding empty categories.
     *
     * @return null
     */
    public function test_getSelectString_hideempty()
    {
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', 1);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSql = $this->_oList->UNITgetSelectString();

        $sExpSql = "where 1  order";

        $this->assertContains($sExpSql, $sCurSql);
    }

    /**
     * Test get select string in admin.
     *
     * @return null
     */
    public function test_getSelectString_admin()
    {
        $this->_oList->setVar('testAdmin', true);

        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSql = $this->_oList->UNITgetSelectString();

        $sExpSql = "where 1  order";

        $this->assertContains($sExpSql, $sCurSql);
    }

    /**
     * Test get select string with shop id.
     *
     * @return null
     */
    public function test_getSelectString_shopid()
    {
        $this->_oList->setVar('sShopID', 1);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSql = $this->_oList->UNITgetSelectString();

        $sExpSql = "and oxcategories.oxshopid = '1'";


        $this->assertNotContains($sExpSql, $sCurSql);
    }

    /**
     * Test get select string remove inactive.
     *
     * @return null
     */
    public function test_getSelectString_remove()
    {
        $this->_oList->setVar('sShopID', 1);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $sCurSql = $this->_oList->UNITgetSelectString();

        $sViewName = getViewName('oxcategories');
        $sExpSql = ",not $sViewName.oxactive as oxppremove";


        $this->assertContains($sExpSql, $sCurSql);
    }

    /**
     * Test get select fileds for tree.
     *
     * @return null
     */
    public function test_getSqlSelectFieldsForTree()
    {
        $sExpect = 'tablex.oxid as oxid,'
                   . ' tablex.oxactive as oxactive,'
                   . ' tablex.oxhidden as oxhidden,'
                   . ' tablex.oxparentid as oxparentid,'
                   . ' tablex.oxdefsort as oxdefsort,'
                   . ' tablex.oxdefsortmode as oxdefsortmode,'
                   . ' tablex.oxleft as oxleft,'
                   . ' tablex.oxright as oxright,'
                   . ' tablex.oxrootid as oxrootid,'
                   . ' tablex.oxsort as oxsort,'
                   . ' tablex.oxtitle as oxtitle,'
                   . ' tablex.oxdesc as oxdesc,'
                   . ' tablex.oxpricefrom as oxpricefrom,'
                   . ' tablex.oxpriceto as oxpriceto,'
                   . ' tablex.oxicon as oxicon, tablex.oxextlink as oxextlink,'
                   . ' tablex.oxthumb as oxthumb, tablex.oxpromoicon as oxpromoicon,';

        $sExpect .= 'not tablex.oxactive as oxppremove';
        //
        $oList = new oxCategoryList();


        $this->assertEquals($sExpect, $oList->UNITgetSqlSelectFieldsForTree('tablex'));
    }

    /**
     * Test get select fields for tree in language 1.
     *
     * @return null
     */
    public function test_getSqlSelectFieldsForTree_lang1()
    {
        oxRegistry::getLang()->setBaseLanguage(1);
        $sExpect = 'tablex.oxid as oxid,'
                   . ' tablex.oxactive as oxactive,'
                   . ' tablex.oxhidden as oxhidden,'
                   . ' tablex.oxparentid as oxparentid,'
                   . ' tablex.oxdefsort as oxdefsort,'
                   . ' tablex.oxdefsortmode as oxdefsortmode,'
                   . ' tablex.oxleft as oxleft,'
                   . ' tablex.oxright as oxright,'
                   . ' tablex.oxrootid as oxrootid,'
                   . ' tablex.oxsort as oxsort,'
                   . ' tablex.oxtitle as oxtitle,'
                   . ' tablex.oxdesc as oxdesc,'
                   . ' tablex.oxpricefrom as oxpricefrom,'
                   . ' tablex.oxpriceto as oxpriceto,'
                   . ' tablex.oxicon as oxicon, tablex.oxextlink as oxextlink,'
                   . ' tablex.oxthumb as oxthumb, tablex.oxpromoicon as oxpromoicon,';

        $sExpect .= 'not tablex.oxactive as oxppremove';
        //
        $oList = new oxCategoryList();


        $this->assertEquals($sExpect, $oList->UNITgetSqlSelectFieldsForTree('tablex'));
    }


    /**
     * Test get depth sql union.
     *
     * @return null
     */
    public function test_getDepthSqlUnion()
    {
        $oCat = new oxcategory();
        $oCat->oxcategories__oxrootid = new oxField('rootid');
        $oCat->oxcategories__oxleft = new oxField('151');
        $oCat->oxcategories__oxright = new oxField('959');

        $oList = $this->getMock('oxCategoryList', array('_getSqlSelectFieldsForTree'));
        $oList->expects($this->once())->method('_getSqlSelectFieldsForTree')
            ->with($this->equalTo('maincats'), $this->equalTo(null))
            ->will($this->returnValue('qqqqq'));

        $sViewName = $oCat->getViewName();

        $this->assertEquals("UNION SELECT qqqqq FROM oxcategories AS subcats LEFT JOIN $sViewName AS maincats on maincats.oxparentid = subcats.oxparentid WHERE subcats.oxrootid = 'rootid' AND subcats.oxleft <= 151 AND subcats.oxright >= 959", $oList->UNITgetDepthSqlUnion($oCat));

        $oList = $this->getMock('oxCategoryList', array('_getSqlSelectFieldsForTree'));
        $oList->expects($this->once())->method('_getSqlSelectFieldsForTree')
            ->with($this->equalTo('maincats'), $this->equalTo('lalala'))
            ->will($this->returnValue('qqqqq'));

        $this->assertEquals("UNION SELECT qqqqq FROM oxcategories AS subcats LEFT JOIN $sViewName AS maincats on maincats.oxparentid = subcats.oxparentid WHERE subcats.oxrootid = 'rootid' AND subcats.oxleft <= 151 AND subcats.oxright >= 959", $oList->UNITgetDepthSqlUnion($oCat, 'lalala'));

        $oList = $this->getMock('oxCategoryList', array('_getSqlSelectFieldsForTree'));
        $oList->expects($this->never())->method('_getSqlSelectFieldsForTree');

        $this->assertEquals("", $oList->UNITgetDepthSqlUnion(null));
    }

    /**
     * Test get select fields forcing full tree.
     *
     * @return null
     */
    public function test_getSelectString_Forcefull()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', 1);
        $this->_oList->setVar('iForceLevel', 0);
        oxRegistry::getLang()->setBaseLanguage(1);

        $sCurSql = $this->_oList->UNITgetSelectString();
        $sExpSql = "where 1  order";

        $this->assertContains($sExpSql, $sCurSql);
    }

    /**
     * Test list post processing, removing inactive categories.
     *
     * @return null
     */
    public function test_ppRemoveInactiveCategories()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString());
        $iPreCnt = $this->_oList->count();

        $this->_oList[$this->_sActRoot]->oxcategories__oxppremove = new oxField(true, oxField::T_RAW);

        $this->_oList->UNITppRemoveInactiveCategories();
        $iCurCnt = $this->_oList->count();

        $this->assertNotEquals($iPreCnt, $iCurCnt);
    }

    /**
     * Test list post processing, loading full category object.
     *
     * @return null
     */
    public function test_ppLoadFullCategory()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString());
        $this->_oList[$this->_sActCat] = array();
        $this->assertFalse($this->_oList[$this->_sActCat] instanceof oxcategory);

        $this->_oList->UNITppLoadFullCategory($this->_sActCat);
        $this->assertTrue($this->_oList[$this->_sActCat] instanceof oxcategory);
    }

    /**
     * Test list post processing, loading full category object with removed root category.
     *
     * @return null
     */
    public function test_ppLoadFullCategoryWithRemovedRoot()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString());
        $this->_oList[$this->_sActRoot]->oxcategories__oxppremove = new oxField(true, oxField::T_RAW);
        $this->_oList[$this->_sActCat]->oxcategories__oxppremove = new oxField(true, oxField::T_RAW);
        $this->_oList->UNITppRemoveInactiveCategories();

        $this->_oList->UNITppLoadFullCategory($this->_sActCat);

        $this->assertTrue(isset($this->_sActCat));
        $this->assertFalse($this->_oList[$this->_sActCat] instanceof oxcategory);
    }

    /**
     * Test list post processing, adding active category path information.
     *
     * @return null
     */
    public function test_ppAddPathInfo()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString());
        $this->_oList->UNITppAddPathInfo();

        $aPath = $this->_oList->getPath();
        $aPathKeys = array_keys($aPath);

        $this->assertEquals(array_keys($aPath), $this->_aActPath);

        foreach ($aPath as $oCat) {
            $this->assertEquals($oCat->getExpanded(), true);
        }
    }

    /**
     * Test list post processing, adding path information without active category.
     *
     * @return null
     */
    public function test_ppAddPathInfoIfActCatNotSet()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->UNITppAddPathInfo();
        $this->assertEquals(0, count($this->_oList->getPath()));
    }

    /**
     * Test list post processing, adding content categories to tree with no content categories.
     *
     * @return null
     */
    public function test_ppAddContentCategories()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->load();
        $this->_oList->UNITppAddContentCategories();

        $aContent = $this->_oList[$this->_sActCat]->getContentCats();

        $this->assertEquals(0, count($aContent));
    }

    /**
     * Test list post processing, adding content categories to tree with content categories.
     *
     * @return null
     */
    public function test_ppAddContentCategories_sim()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        oxAddClassModule('modContentList_oxcategorylist', 'oxcontentlist');

        $this->_oList->load();
        $this->_oList->UNITppAddContentCategories();

        $aContent = $this->_oList[$this->_sActCat]->getContentCats();

        $this->assertEquals(2, count($aContent));
    }

    /**
     * Test list post processing, build tree and check path.
     *
     * @return null
     */
    public function test_ppBuildTree_Path()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString(true));
        $this->_oList->UNITppBuildTree();

        $oCat = $this->_oList[$this->_sActRoot];
        foreach ($this->_aActPath as $sNr => $sId) {
            $this->assertEquals($sId, $oCat->getId());
            $this->assertTrue($oCat->getHasVisibleSubcats());
            $this->assertTrue($oCat->getIsVisible());
            $oCat = $oCat->getSubCat($this->_aActPath[$sNr + 1]);
        }
    }

    /**
     * Test list post processing, build tree and check if hidden categories were removed.
     *
     * @return null
     */
    public function test_ppBuildTree_Hidden()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString());
        $this->_oList[$this->_sActCat]->oxcategories__oxhidden = new oxField(true, oxField::T_RAW);
        $this->_oList[$this->_sActCat]->setIsVisible(null);
        $this->_oList->UNITppBuildTree();

        $oCat = $this->_oList[$this->_sActRoot];
        foreach ($this->_aActPath as $sNr => $sId) {

            //Hidded actCat
            if ($sId == $this->_sActCat) {
                $this->assertFalse($oCat->getIsVisible());
            } else {
                $this->assertTrue($oCat->getIsVisible());
            }

            $oCat = $oCat->getSubCat($this->_aActPath[$sNr + 1]);
        }
    }

    /**
     * Test list post processing, build tree and check sorting.
     *
     * @return null
     */
    public function test_ppBuildTree_Sorting()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', $this->_sActCat);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', false);
        $this->_oList->setVar('iForceLevel', 2);

        $sParentId = $this->_sActRoot;
        $oCat1 = new oxCategory();
        $oCat1->setId("_test1");
        $oCat1->oxcategories__oxparentid = new oxField($sParentId);
        $oCat1->oxcategories__oxsort = new oxField(2);
        $oCat1->oxcategories__oxactive = new oxField(1);
        $oCat1->save();
        $oCat2 = new oxCategory();
        $oCat2->setId("_test2");
        $oCat2->oxcategories__oxparentid = new oxField($sParentId);
        $oCat2->oxcategories__oxsort = new oxField(3);
        $oCat2->oxcategories__oxactive = new oxField(1);
        $oCat2->save();
        $oCat3 = new oxCategory();
        $oCat3->setId("_test3");
        $oCat3->oxcategories__oxparentid = new oxField($sParentId);
        $oCat3->oxcategories__oxsort = new oxField(1);
        $oCat3->oxcategories__oxactive = new oxField(1);
        $oCat3->save();

        $this->_oList->buildTree($this->_sActRoot);

        //Check root order
        $aCurRootOrder = array();
        foreach ($this->_oList as $sId => $oCat) {
            $aCurRootOrder[] = $oCat->oxcategories__oxsort->value;
        }
        $aExpRootOrder = $aCurRootOrder;
        asort($aExpRootOrder);
        $this->assertEquals(implode(',', $aExpRootOrder), implode(',', $aCurRootOrder));

        //Chect subcat order
        $aCurSubOrder = array();
        foreach ($this->_oList[$this->_sActRoot]->getSubCats() as $sId => $oCat) {
            $aCurSubOrder[] = $oCat->oxcategories__oxsort->value;
        }
        $aExpSubOrder = $aCurSubOrder;
        asort($aExpSubOrder);
        $this->assertEquals(implode(',', $aExpSubOrder), implode(',', $aCurSubOrder));
    }

    /**
     * Test list post processing, add depth information.
     *
     * @return null
     */
    public function test_ppAddDepthInformation()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', true);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->selectString($this->_oList->UNITgetSelectString(false));
        $this->_oList->UNITppBuildTree();
        $this->_oList->UNITppAddDepthInformation();

        //Check depth info
        $iDepth = 1;
        foreach ($this->_aActPath as $sId) {
            $sPrefixExp = ($iDepth > 1) ? str_repeat('*', $iDepth - 1) . ' ' : '';
            $iPrefixCur = substr($this->_oList[$sId]->oxcategories__oxtitle->value, 0, strlen($sPrefixStr));
            $this->assertEquals($sPrefixStr, $iPrefixCur);
            $iDepth++;
        }
    }

    /**
     * Test list post processing, add depth information.
     *
     * @return null
     */
    public function test_addDepthInfo()
    {
        $aTree = array();
        $oCat = new oxCategory();
        $oCat->setId("_test1");
        $oCat->oxcategories__oxparentid = new oxField("_test2");
        $oCat->oxcategories__oxsort = new oxField(2);
        $oCat->oxcategories__oxactive = new oxField(1);
        $oCat->oxcategories__oxtitle = new oxField("_test");
        $oCat->save();
        $oCat2 = $this->getMock('oxCategory', array('getSubCats'));
        $oCat2->expects($this->any())->method('getSubCats')->will($this->returnValue(array($oCat)));
        $oCat2->setId("_test2");
        $oCat2->oxcategories__oxparentid = new oxField("oxrootid");
        $oCat2->oxcategories__oxsort = new oxField(2);
        $oCat2->oxcategories__oxactive = new oxField(1);
        $oCat2->oxcategories__oxtitle = new oxField("_test");
        $oCat2->save();

        $aNewTree = $this->_oList->UNITaddDepthInfo($aTree, $oCat2);
        $sDepth = "";
        foreach ($aNewTree as $oCat) {
            $sDepth .= "-";
            $this->assertEquals($sDepth . " _test", $oCat->oxcategories__oxtitle->value);
        }
    }




    /**
     * Test if build tree executes all required postprocessing methods.
     *
     * @return null
     */
    public function testBuildTree()
    {
        $oCatList = $this->getMock('testOxCategoryList', array('load', '_ppRemoveInactiveCategories', '_ppAddPathInfo', '_ppAddContentCategories', '_ppBuildTree', '_ppLoadFullCategory'));
        $oCatList->expects($this->at(0))->method('load');
        $oCatList->expects($this->at(1))->method('_ppRemoveInactiveCategories');
        $oCatList->expects($this->at(2))->method('_ppLoadFullCategory');
        $oCatList->expects($this->at(3))->method('_ppAddPathInfo');
        $oCatList->expects($this->at(4))->method('_ppAddContentCategories');
        $oCatList->expects($this->at(5))->method('_ppBuildTree');

        $oCatList->buildTree($this->_sActCat, false, false);

        $this->assertEquals($this->_sActCat, $oCatList->getVar('sActCat'));
    }


    /**
     * Test Load list.
     *
     * @return null
     */
    public function testLoadList()
    {
        $this->_oList->setVar('sShopID', null);
        $this->_oList->setVar('sActCat', null);
        $this->_oList->setVar('blHideEmpty', false);
        $this->_oList->setVar('blForceFull', true);
        $this->_oList->setVar('iForceLevel', 0);

        $this->_oList->loadList();

        //Check depth info
        $iDepth = 1;
        foreach ($this->_aActPath as $sId) {
            $sPrefixExp = ($iDepth > 1) ? str_repeat('*', $iDepth - 1) . ' ' : '';
            $iPrefixCur = substr($this->_oList[$sId]->oxcategories__oxtitle->value, 0, strlen($sPrefixStr));
            $this->assertEquals($sPrefixStr, $iPrefixCur);
            $iDepth++;
        }
    }

    /**
     * Test set shop id.
     *
     * @return null
     */
    public function testSetShopId()
    {
        $oCatList = $this->getProxyClass("oxcategorylist");
        $oCatList->setShopID(3);
        $this->assertEquals(3, $oCatList->getNonPublicVar('_sShopID'));
    }

    /**
     * Test set get active/clicked category.
     *
     * @return null
     */
    public function testGetClickCat()
    {
        $oCatList = $this->getProxyClass("oxcategorylist");
        $oCatList->setNonPublicVar("_aPath", array("aaa", "bbb"));
        $this->assertEquals("bbb", $oCatList->getClickCat());
    }

    /**
     * Test set get active root category.
     *
     * @return null
     */
    public function testGetClickRoot()
    {
        $oCatList = $this->getProxyClass("oxcategorylist");
        $oCatList->setNonPublicVar("_aPath", array(2 => "aaa", 1 => "bbb"));
        $this->assertEquals(array(0 => "aaa"), $oCatList->getClickRoot());
    }

    /**
     * Test update category tree nodes
     *
     * @return null
     */
    public function testUpdateCategoryTreeUpdateNodes()
    {
        $oObj1 = oxNew("oxCategory");
        $oObj1->setId("_test1");
        $oObj1->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oObj1->oxcategories__oxtitle = new oxField("1-8|1-8", oxField::T_RAW);
        $oObj1->save(); // call insert
        $oObj2 = oxNew("oxCategory");
        $oObj2->setId("_test2");
        $oObj2->oxcategories__oxparentid = new oxField($oObj1->getId(), oxField::T_RAW);
        $oObj2->oxcategories__oxtitle = new oxField("2-5|4-7", oxField::T_RAW);
        $oObj2->save(); // call insert
        $oObj3 = oxNew("oxCategory");
        $oObj3->setId("_test3");
        $oObj3->oxcategories__oxparentid = new oxField($oObj1->getId(), oxField::T_RAW);
        $oObj3->oxcategories__oxtitle = new oxField("6-7|2-3", oxField::T_RAW);
        $oObj3->save(); // call insert
        $oObj4 = oxNew("oxCategory");
        $oObj4->setId("_test4");
        $oObj4->oxcategories__oxparentid = new oxField($oObj2->getId(), oxField::T_RAW);
        $oObj4->oxcategories__oxtitle = new oxField("3-4|5-6", oxField::T_RAW);
        $oObj4->save(); // call insert
        // now we have one parent, two children and one 3rd level child
        $this->_oList->updateCategoryTree(false);
        // checking....
        $oDB = oxDb::getDb();
        $sSelect = "select oxtitle, oxleft, oxright from oxcategories where oxid like '_test%'";
        $aData = $oDB->GetAll($sSelect);

        // holding number - show if all tests for N bit set are OK
        $iTrue = 3;
        foreach ($aData as $aLine) {
            $r = $this->_checkData($aLine[0], $aLine[1], $aLine[2]);
            if (!($r & 1) && ($iTrue & 1)) {
                $iTrue &= ~1;
            }
            if (!($r & 2) && ($iTrue & 2)) {
                $iTrue &= ~2;
            }
        }


        $aUpdateInfo = array(
            '<b>Processing : Eco-Fashion</b>(943a9ba3050e78b443c16e043ae60ef3)<br>',
            '<b>Processing : Geschenke</b>(8a142c3e4143562a5.46426637)<br>',
            '<b>Processing : 1-8|1-8</b>(_test1)<br>',
        );

        $this->assertTrue($iTrue == 1 || $iTrue == 2);
        $this->assertEquals($aUpdateInfo, $this->_oList->getUpdateInfo());
    }

    /**
     * as we know two possible good sets of given data, we must check them both
     * sets are saved in oxtitle field, separated by | and values of left and right are sep by - sign
     * these two functions provide us the possibility to check up to 8 sets [could be done for more]
     *
     * @param string $s data
     *
     * @return array
     */
    protected function _parseData($s)
    {
        $a = explode('|', $s);
        $b = array();
        foreach ($a as $c) {
            $b[] = explode('-', $c);
        }

        return $b;
    }

    /**
     * result is the number, each bit N represents if N data set succeded.
     *
     * @param string $s data
     * @param string $l left
     * @param string $r right
     *
     * @return int
     */
    protected function _checkData($s, $l, $r)
    {
        $a = $this->_parseData($s);
        $g = 255;
        $i = 0;
        foreach ($a as $alr) {
            if ($alr[0] != $l || $alr[1] != $r) {
                $g &= ~(1 << $i);
            }
            $i++;
        }

        return $g;
    }

    /**
     * Test set get active root category.
     */
    public function testLoadLevel()
    {
        $oCategoryList = new oxCategoryList();
        $oCategoryList->setLoadLevel(1);
        $this->assertEquals(1, $oCategoryList->getLoadLevel());

        $oCategoryList->setLoadLevel(0);
        $this->assertEquals(0, $oCategoryList->getLoadLevel());

        $oCategoryList->setLoadLevel(2);
        $this->assertEquals(2, $oCategoryList->getLoadLevel());

        $oCategoryList->setLoadLevel(3);
        $this->assertEquals(2, $oCategoryList->getLoadLevel());

        $oCategoryList->setLoadLevel(-1);
        $this->assertEquals(0, $oCategoryList->getLoadLevel());
    }

    /**
     * Test set get active root category.
     */
    public function testLoadFull()
    {
        $oCategoryList = new oxCategoryList();

        $oCategoryList->setLoadFull(true);
        $this->assertTrue($oCategoryList->getLoadFull());

        $oCategoryList->setLoadFull(false);
        $this->assertFalse($oCategoryList->getLoadFull());
    }

    /**
     * Test case for first cache call.
     * Verify that the full category tree is loaded for caching.
     * Verify that former load full flag setting is restored.
     */
    public function testLoadFromCacheLoadFullInitiallyFalse()
    {
        $this->markTestSkipped('EE only');

        $oCacheBackend = $this->getMock('oxCacheBackend', array('get', 'set', 'isActive'), array(), '', false);
        $oCacheBackend->expects($this->any())->method('get')->will($this->returnValue(null));
        $oCacheBackend->expects($this->any())->method('isActive')->will($this->returnValue(true));

        $oCategoryList = $this->getMock('oxCategoryList', array('_getCacheBackend'), array(), '', false);
        $oCategoryList->expects($this->any())->method('_getCacheBackend')->will($this->returnValue($oCacheBackend));
        $oCategoryList->setLoadFull(false);
        $oCategoryList->load();

        $this->assertSame(28, count($oCategoryList));
        $this->assertFalse($oCategoryList->getLoadFull());
    }

    /**
     * Test case for first cache call.
     * Verify that the full category tree is loaded for caching.
     * Verify that former load full flag setting is restored.
     */
    public function testLoadFromCacheLoadFullInitiallyTrue()
    {
        $this->markTestSkipped('EE only');

        $oCacheBackend = $this->getMock('oxCacheBackend', array('get', 'set', 'isActive'), array(), '', false);
        $oCacheBackend->expects($this->any())->method('get')->will($this->returnValue(null));
        $oCacheBackend->expects($this->any())->method('isActive')->will($this->returnValue(true));

        $oCategoryList = $this->getMock('oxCategoryList', array('_getCacheBackend'), array(), '', false);
        $oCategoryList->expects($this->any())->method('_getCacheBackend')->will($this->returnValue($oCacheBackend));
        $oCategoryList->setLoadFull(true);
        $oCategoryList->load();

        $this->assertSame(28, count($oCategoryList));
        $this->assertTrue($oCategoryList->getLoadFull());
    }

    /**
     * Calling function load in case cache is not activated.
     */
    public function testLoadFromDB()
    {
        $this->markTestSkipped('EE only');

        $oCacheBackend = $this->getMock('oxCacheBackend', array('get', 'set', 'isActive'), array(), '', false);
        $oCacheBackend->expects($this->any())->method('get')->will($this->returnValue(null));
        $oCacheBackend->expects($this->any())->method('isActive')->will($this->returnValue(false));

        $oCategoryList = $this->getMock('oxCategoryList', array('_getCacheBackend'), array(), '', false);
        $oCategoryList->expects($this->any())->method('_getCacheBackend')->will($this->returnValue($oCacheBackend));
        $oCategoryList->load();

        $this->assertSame(21, count($oCategoryList));
    }
}
