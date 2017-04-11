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
 * Tests for Article_Extend class
 */
class Unit_Admin_ArticleExtendTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxmediaurls");
        parent::tearDown();
    }

    /**
     * Article_Extend::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $this->getConfig()->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid !='' "));

        // testing..
        $oView = new Article_Extend();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof oxArticle);
        $this->assertTrue($aViewData["artcattree"] instanceof oxCategoryList);
        $this->assertTrue($aViewData["aMediaUrls"] instanceof oxList);

        $this->assertEquals('article_extend.tpl', $sTplName);
    }

    /**
     * Article_Extend::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setRequestParameter("editval", array("oxarticles__oxtprice" => -1));

        // testing..
        try {
            $oView = new Article_Extend();
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Extend::save()");

            return;
        }
        $this->fail("error in Article_Extend::save()");
    }

    /**
     * Article_Extend::Save() test case
     *
     * @return null
     */
    public function testSaveMissingMediaDescription()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{return "EXCEPTION_NODESCRIPTIONADDED";}');
        $this->getConfig()->setRequestParameter("mediaUrl", "testUrl");
        $this->getConfig()->setRequestParameter("mediaDesc", null);

        // testing..
        $oView = new Article_Extend();
        $this->assertEquals("EXCEPTION_NODESCRIPTIONADDED", $oView->save());
    }

    /**
     * Article_Extend::Save() test case
     *
     * @return null
     */
    public function testSaveMissingMediaUrlAndFile()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{return "EXCEPTION_NOMEDIAADDED";}');
        oxTestModules::addFunction('oxConfig', 'getUploadedFile', '{return array( "name" => false );}');

        $this->getConfig()->setRequestParameter("mediaUrl", null);
        $this->getConfig()->setRequestParameter("mediaDesc", "testDesc");

        // testing..
        $oView = new Article_Extend();
        $this->assertEquals("EXCEPTION_NOMEDIAADDED", $oView->save());
    }

    /**
     * Article_Extend::Save() test case
     *
     * @return null
     */
    public function testSaveUnableToMoveUploadedFile()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return $aA[0]; }');
        oxTestModules::addFunction('oxUtilsFile', 'processFile', '{ throw new Exception("handleUploadedFile"); }');

        $this->getConfig()->setRequestParameter("mediaUrl", "testUrl");
        $this->getConfig()->setRequestParameter("mediaDesc", "testDesc");

        $oConfig = $this->getMock("oxConfig", array("getUploadedFile", "isDemoShop"));
        $oConfig->expects($this->exactly(2))->method('getUploadedFile')->will($this->returnValue(array("name" => "testName")));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        // testing..
        $oView = $this->getMock("Article_Extend", array("getConfig", "resetContentCache"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->any())->method('resetContentCache');
        $this->assertEquals("handleUploadedFile", $oView->save());
    }

    /**
     * Article_Extend::Save() test case
     *
     * @return null
     */
    public function testSaveMediaFileUpload()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxmediaurl', 'save', '{ throw new Exception( "oxmediaurl.save" ); }');
        oxTestModules::addFunction('oxUtilsFile', 'processFile', '{}');

        $this->getConfig()->setRequestParameter("mediaUrl", "testUrl");
        $this->getConfig()->setRequestParameter("mediaDesc", "testDesc");

        $oConfig = $this->getMock("oxConfig", array("getUploadedFile", "isDemoShop"));
        $oConfig->expects($this->exactly(2))->method('getUploadedFile')->will($this->returnValue(array("name" => "testName")));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        // testing..
        $oView = $this->getMock("Article_Extend", array("getConfig", "resetContentCache"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->any())->method('resetContentCache');


        // testing..
        try {
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("oxmediaurl.save", $oExcp->getMessage(), "error in Article_Extend::save()");

            return;
        }
        $this->fail("error in Article_Extend::save()");
    }

    /**
     * Article_Extend::Save() test case when demoShop = true and upload file
     *
     * @return null
     */
    public function testSaveDemoShopFileUpload()
    {
        $oConfig = $this->getMock("oxConfig", array("getUploadedFile", "isDemoShop"));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));
        $oConfig->expects($this->exactly(2))->method('getUploadedFile')->will(
            $this->onConsecutiveCalls(
                array("name" => array('FL@oxarticles__oxfile' => "testFile")), array("name" => "testName")
            )
        );
        // testing..
        $oView = $this->getMock("Article_Extend", array("getConfig", "resetContentCache"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->any())->method('resetContentCache');
        $oView->save();
        // testing..
        $aErr = $this->getSession()->getVar('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('ARTICLE_EXTEND_UPLOADISDISABLED', $oErr->getOxMessage());
    }

    /**
     * Article_Extend::DeleteMedia() test case
     *
     * @return null
     */
    public function testDeleteMedia()
    {
        $oMediaUrl = oxNew("oxMediaUrl");
        $oMediaUrl->setId("testMediaId");
        $oMediaUrl->save();

        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxmediaurls where oxid = 'testMediaId'"));

        $this->getConfig()->setRequestParameter("oxid", "testId");
        $this->getConfig()->setRequestParameter("mediaid", "testMediaId");

        // testing..
        $oView = new Article_Extend();
        $oView->deletemedia();

        $this->assertFalse(oxDb::getDb()->getOne("select 1 from oxmediaurls where oxid = 'testMediaId'"));
    }

    /**
     * Article_Extend::AddDefaultValues() test case
     *
     * @return null
     */
    public function testAddDefaultValues()
    {
        $aParams['oxarticles__oxexturl'] = "http://www.delfi.lt";
        $oView = new Article_Extend();
        $aParams = $oView->addDefaultValues($aParams);

        $this->assertEquals("www.delfi.lt", $aParams['oxarticles__oxexturl']);
    }

    /**
     * Article_Extend::UpdateMedia() test case
     *
     * @return null
     */
    public function testUpdateMedia()
    {
        $oMediaUrl = oxNew("oxMediaUrl");
        $oMediaUrl->setId("testMediaId");
        $oMediaUrl->save();

        $aValue = array("testMediaId" => array("oxmediaurls__oxurl" => "testUrl", "oxmediaurls__oxdesc" => "testDesc"));
        $this->getConfig()->setRequestParameter("aMediaUrls", $aValue);

        $oView = new Article_Extend();
        $oView->updateMedia();

        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxmediaurls where oxurl = 'testUrl' and oxdesc='testDesc' "));
    }

    /**
     * Test case for Article_Extend::getUnitsArray()
     *
     * @return null
     */
    public function testGetUnitsArray()
    {
        $aArray = oxRegistry::getLang()->getSimilarByKey("_UNIT_", 0, false);
        $oView = new Article_Extend();

        $this->assertEquals($aArray, $oView->getUnitsArray());
    }
}
