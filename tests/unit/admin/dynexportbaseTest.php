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
 * Tests module for DynExportBase class
 */
class _DynExportBase extends DynExportBase
{

    public function initArticle($sHeapTable, $iCnt, & $blContinue)
    {
        try {
            return $this->_initArticle($sHeapTable, $iCnt, $blContinue);
        } catch (Exception $oExcp) {
            throw $oExcp;
        }
    }

    /**
     * Get private variable.
     *
     * @param string $sName variable name
     *
     * @return null
     */
    public function getVar($sName)
    {
        return $this->{'_' . $sName};
    }

    /**
     * Set private variable.
     *
     * @param string $sName  variable name
     * @param string $sValue variable value
     *
     * @return null
     */
    public function setVar($sName, $sValue)
    {
        $this->{'_' . $sName} = $sValue;
    }
}

/**
 * Tests for DynExportBase class
 */
class Unit_Admin_DynExportBaseTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sFile = getTestsBasePath() . "/misc/test.txt";
        if (file_exists($sFile)) {
            unlink($sFile);
        }
        oxDb::getDb()->execute("drop TABLE if exists testdynexportbasetable");

        parent::tearDown();
    }

    /**
     * DynExportBase::Construct() test case
     *
     * @return null
     */
    public function testConstruct()
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . "/export/dynexport.txt";

        $oView = $this->getProxyClass("DynExportBase");
        $this->assertEquals($sFilePath, $oView->getNonPublicVar("_sFilePath"));
    }

    /**
     * DynExportBase::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new DynExportBase();
        $this->assertEquals('dynexportbase.tpl', $oView->render());
    }

    /**
     * DynExportBase::CreateMainExportView() test case
     *
     * @return null
     */
    public function testCreateMainExportView()
    {

        // testing..
        oxTestModules::addFunction('oxCategoryList', 'loadList', '{ throw new Exception( "buildList" ); }');

        // testing..
        try {
            $oView = new DynExportBase();
            $oView->createMainExportView();
        } catch (Exception $oExcp) {
            $this->assertEquals("buildList", $oExcp->getMessage(), "error in DynExportBase::createMainExportView()");

            return;
        }
        $this->fail("error in DynExportBase::createMainExportView()");
    }

    /**
     * DynExportBase::Start() test case
     *
     * @return null
     */
    public function testStart()
    {
        // testing..
        $oView = $this->getMock("_DynExportBase", array("prepareExport"));
        $oView->expects($this->once())->method('prepareExport')->will($this->returnValue(5));
        $oView->setVar('sFilePath', getTestsBasePath() . "/misc/test.txt");
        $oView->start();
        $this->assertEquals(0, $oView->getViewDataElement("refresh"));
        $this->assertEquals(0, $oView->getViewDataElement("iStart"));
        $this->assertEquals(5, $oView->getViewDataElement("iEnd"));
    }

    /**
     * DynExportBase::Stop() test case
     *
     * @return null
     */
    public function testStop()
    {
        $oDb = oxDb::getDb();
        $sTableName = 'testdynexportbasetable';
        $oDb->execute("CREATE TABLE `{$sTableName}` (`oxid` TINYINT( 1 ) NOT NULL) ENGINE = MYISAM");
        $this->assertEquals(0, $oDb->getOne("select count(*) from {$sTableName}"));

        $oView = $this->getMock("DynExportBase", array("_getHeapTableName"));
        $oView->expects($this->once())->method('_getHeapTableName')->will($this->returnValue($sTableName));
        $oView->stop(999);

        $this->assertEquals(999, $oView->getViewDataElement("iError"));
        $this->assertEquals(0, count($oDb->getAll("show tables like '$sTableName'")));
    }

    /**
     * DynExportBase::NextTick() test case
     *
     * @return null
     */
    public function testNextTick()
    {
        // defining parameters
        $oView = new DynExportBase();
        $this->assertFalse($oView->nextTick(999));
    }

    /**
     * DynExportBase::Write() test case
     *
     * @return null
     */
    public function testWrite()
    {
        // defining parameters
        $sLine = 'TestExport';

        // testing..
        $oView = new DynExportBase();
        $oView->fpFile = @fopen(getTestsBasePath() . "/misc/test.txt", "w");
        $oView->write($sLine);
        fclose($oView->fpFile);
        $sFileCont = file_get_contents(getTestsBasePath() . "/misc/test.txt", true);
        $this->assertEquals($sLine . "\r\n", $sFileCont);
    }

    /**
     * DynExportBase::Run() test case
     *
     * @return null
     */
    public function testRun()
    {
        modConfig::setRequestParameter("iStart", 0);
        modConfig::setRequestParameter("aExportResultset", array("aaaaa"));

        // testing..
        $oView = $this->getMock("_DynExportBase", array("nextTick"));
        $oView->expects($this->any())->method('nextTick')->will($this->returnValue(5));
        $oView->setVar('sFilePath', getTestsBasePath() . "/misc/test.txt");
        $oView->setExportPerTick(30);
        $oView->run();
        $this->assertEquals(0, $oView->getViewDataElement("refresh"));
        $this->assertEquals(30, $oView->getViewDataElement("iStart"));
        $this->assertEquals(5, $oView->getViewDataElement("iExpItems"));
    }

    /**
     * DynExportBase::Run() test case with default per tick count
     *
     * @return null
     */
    public function testRunWithDefaultConfigPerTickCount()
    {
        modConfig::setRequestParameter("iStart", 0);
        modConfig::setRequestParameter("aExportResultset", array("aaaaa"));
        modConfig::getInstance()->setConfigParam("iExportNrofLines", 10);

        // testing..
        $oView = $this->getMock("_DynExportBase", array("nextTick"));
        $oView->expects($this->any())->method('nextTick')->will($this->returnValue(5));
        $oView->setVar('sFilePath', getTestsBasePath() . "/misc/test.txt");
        $oView->run();
        $this->assertEquals(0, $oView->getViewDataElement("refresh"));
        $this->assertEquals(10, $oView->getViewDataElement("iStart"));
        $this->assertEquals(5, $oView->getViewDataElement("iExpItems"));
    }

    /**
     * test setting and getting export per tick count
     *
     * @return null
     */
    public function testSetGetExportPerTick()
    {
        $oView = new DynExportBase();

        // if not set yet, should take value from config
        modConfig::getInstance()->setConfigParam("iExportNrofLines", 150);
        $this->assertEquals(150, $oView->getExportPerTick());

        // if not set in config, should use default value
        $oView->setExportPerTick(null);
        modConfig::getInstance()->setConfigParam("iExportNrofLines", 0);
        $this->assertEquals($oView->iExportPerTick, $oView->getExportPerTick());

        // Should be able to set this value too
        $oView->setExportPerTick(190);
        $this->assertEquals(190, $oView->getExportPerTick());
    }

    /**
     * DynExportBase::RemoveSID() test case
     *
     * @return null
     */
    public function testRemoveSid()
    {
        $sSid = oxRegistry::getSession()->getId();

        // defining parameters
        $sInput = "testStartsid={$sSid}/sid/{$sSid}/sid={$sSid}&amp;sid={$sSid}&sid={$sSid}TestEnd";

        $oView = new DynExportBase();
        $this->assertEquals("testStartTestEnd", $oView->removeSid($sInput));
    }

    /**
     * DynExportBase::Shrink() test case
     *
     * @return null
     */
    public function testShrink()
    {
        // defining parameters
        $sInput = "testStart\r\n\n\tTestEnd";
        $sOutput = "testStart ...";

        // testing..
        $oView = new DynExportBase();

        $this->assertEquals($sOutput, $oView->shrink($sInput, 15, true));
    }

    /**
     * DynExportBase::GetCategoryString() test case
     *
     * @return null
     */
    public function testGetCategoryString()
    {
        $sOxid = '1126';
        $sCatString = 'Geschenke/Bar-Equipment';

        // defining parameters
        $oArticle = new oxArticle();
        $oArticle->load($sOxid);

        $oView = new DynExportBase();
        $this->assertEquals($sCatString, $oView->getCategoryString($oArticle));
    }

    /**
     * DynExportBase::GetDefaultCategoryString() test case
     *
     * @return null
     */
    public function testGetDefaultCategoryString()
    {
        $sOxid = '1126';
        $sCatString = 'Bar-Equipment';

        // defining parameters
        $oArticle = new oxArticle();
        $oArticle->load($sOxid);

        $oView = new DynExportBase();
        $this->assertEquals($sCatString, $oView->getDefaultCategoryString($oArticle));
    }

    /**
     * DynExportBase::PrepareCSV() test case
     *
     * @return null
     */
    public function testPrepareCSV()
    {
        // defining parameters
        $sInput = 'testStart&nbsp;&euro;|TestStop';
        $sOutput = '"testStart TestStop"';

        // testing..
        $oView = new DynExportBase();
        $this->assertEquals($sOutput, $oView->prepareCSV($sInput));
    }

    /**
     * DynExportBase::PrepareXML() test case
     *
     * @return null
     */
    public function testPrepareXML()
    {
        // defining parameters
        $sInput = 'testStart&"><\'TestStop';
        $sOutput = 'testStart&amp;&quot;&gt;&lt;&apos;TestStop';

        $oView = new DynExportBase();
        $this->assertEquals($sOutput, $oView->prepareXML($sInput));
    }

    /**
     * DynExportBase::GetDeepestCategoryPath() test case
     *
     * @return null
     */
    public function testGetDeepestCategoryPath()
    {
        $oView = $this->getMock("DynExportBase", array("_findDeepestCatPath"));
        $oView->expects($this->once())->method('_findDeepestCatPath')->with($this->isInstanceOf(oxarticle));
        $oView->getDeepestCategoryPath(new oxarticle);
    }

    /**
     * DynExportBase::PrepareExport() test case
     *
     * @return null
     */
    public function testPrepareExport()
    {
        modConfig::setRequestParameter("acat", "testCatId");
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{}');

        $oView = $this->getMock(
            "DynExportBase", array("_getHeapTableName", "_generateTableCharSet",
                                   "_createHeapTable", "_getCatAdd",
                                   "_insertArticles", "_removeParentArticles",
                                   "_setSessionParams")
        );
        $oView->expects($this->once())->method('_getHeapTableName')->will($this->returnValue("oxarticles"));
        $oView->expects($this->once())->method('_generateTableCharSet')->will($this->returnValue("testCharSet"));
        $oView->expects($this->once())->method('_createHeapTable')->with($this->equalTo("oxarticles"), $this->equalTo("testCharSet"))->will($this->returnValue(false));
        $oView->expects($this->once())->method('_getCatAdd')->with($this->equalTo("testCatId"))->will($this->returnValue("testCatId"));
        $oView->expects($this->once())->method('_insertArticles')->with($this->equalTo("oxarticles"), $this->equalTo("testCatId"))->will($this->returnValue(false));
        $oView->expects($this->once())->method('_removeParentArticles')->with($this->equalTo("oxarticles"));
        $oView->expects($this->once())->method('_setSessionParams');

        $this->assertEquals(oxDb::getDb()->getOne("select count(*) from oxarticles"), $oView->prepareExport());
    }

    /**
     * DynExportBase::GetOneArticle() test case
     *
     * @return null
     */
    public function testGetOneArticle()
    {
        $blContinue = null;
        $oView = $this->getMock("DynExportBase", array("_initArticle", "_getHeapTableName", "_setCampaignDetailLink"));
        $oView->expects($this->once())->method('_initArticle')->with($this->equalTo("oxarticles"), $this->equalTo(0))->will($this->returnValue(new oxarticle));
        $oView->expects($this->once())->method('_getHeapTableName')->will($this->returnValue("oxarticles"));
        $oView->expects($this->once())->method('_setCampaignDetailLink')->with($this->isInstanceOf(oxarticle))->will($this->returnValue(new oxarticle));

        $this->assertTrue($oView->getOneArticle(0, $blContinue) instanceof oxarticle);
        $this->assertTrue($blContinue);
    }

    /**
     * DynExportBase::AssureContent() test case
     *
     * @return null
     */
    public function testAssureContent()
    {
        // defining parameters
        $oView = new DynExportBase();
        $this->assertEquals("test", $oView->assureContent("test"));
        $this->assertEquals("-", $oView->assureContent(""));
    }

    /**
     * DynExportBase::UnHTMLEntities() test case
     *
     * @return null
     */
    public function testUnHtmlEntities()
    {
        $oView = new DynExportBase();
        $this->assertEquals("&", $oView->UNITunHtmlEntities("&amp;"));
        $this->assertEquals("\"", $oView->UNITunHtmlEntities("&quot;"));
        $this->assertEquals(">", $oView->UNITunHtmlEntities("&gt;"));
        $this->assertEquals("<", $oView->UNITunHtmlEntities("&lt;"));
        $this->assertEquals("test", $oView->UNITunHtmlEntities("test"));
    }

    /**
     * DynExportBase::GetHeapTableName() test case
     *
     * @return null
     */
    public function testGetHeapTableName()
    {
        // testing..
        $oView = new DynExportBase();
        $this->assertEquals("tmp_" . str_replace("0", "", md5(oxRegistry::getSession()->getId())), $oView->UNITgetHeapTableName());
    }

    /**
     * DynExportBase::GenerateTableCharSet() test case
     *
     * @return null
     */
    public function testGenerateTableCharSet()
    {
        // defining parameters
        $oView = new DynExportBase();
        $this->assertEquals("DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci", $oView->UNITgenerateTableCharSet(5));
        $this->assertEquals("", $oView->UNITgenerateTableCharSet(3));
    }

    /**
     * DynExportBase::CreateHeapTable() test case
     *
     * @return null
     */
    public function testCreateHeapTable()
    {
        // defining parameters
        $sHeapTable = "testdynexportbasetable";
        $sTableCharset = "DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci";

        $oView = new DynExportBase();
        $this->assertTrue($oView->UNITcreateHeapTable($sHeapTable, $sTableCharset));
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(*) from {$sHeapTable}"));
    }

    /**
     * DynExportBase::GetCatAdd() test case
     *
     * @return null
     */
    public function testGetCatAdd()
    {
        $sQ = " and ( oxobject2category.oxcatnid = 'catId1' or oxobject2category.oxcatnid = 'catId2' or oxobject2category.oxcatnid = 'catId3')";

        // defining parameters
        $oView = new DynExportBase();
        $this->assertNull($oView->UNITgetCatAdd(array()));
        $this->assertNull($oView->UNITgetCatAdd("something"));
        $this->assertEquals($sQ, $oView->UNITgetCatAdd(array("catId1", "catId2", "catId3")));
    }

    /**
     * DynExportBase::InsertArticles() test case
     *
     * @return null
     */
    public function testInsertArticlesNoVariantsNoCategoryFilterNoSearchParamNoStockCheck()
    {
        modConfig::getInstance()->setConfigParam("blExportVars", false);
        modConfig::getInstance()->setConfigParam("blUseStock", false);
        modConfig::setRequestParameter("search", false);
        modConfig::setRequestParameter("sExportMinStock", false);

        $sHeapTable = "testdynexportbasetable";
        $sCatAdd = '';

        $oDb = oxDb::getDb();
        $oDb->execute("CREATE TABLE `{$sHeapTable}` (`oxid` TINYINT( 1 ) NOT NULL) ENGINE = MYISAM");

        $oView = new DynExportBase();
        $this->assertTrue($oView->UNITinsertArticles($sHeapTable, $sCatAdd));

        $oArticle = oxNew('oxarticle');
        $sArticleTable = $oArticle->getViewName();
        $sO2CView = getViewName('oxobject2category');

        $iRealCnt = $oDb->getOne("select count(*) from ( select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} where {$sArticleTable}.oxid = {$sO2CView}.oxobjectid and {$sArticleTable}.oxparentid = '' and " . $oArticle->getSqlActiveSnippet() . " group by {$sArticleTable}.oxid) AS counttable");
        $iCurrCnt = $oDb->getOne("select count(*) from {$sHeapTable}");
        $this->assertEquals($iRealCnt, $iCurrCnt);
    }

    /**
     * DynExportBase::InsertArticles() test case
     *
     * @return null
     */
    public function testInsertArticles()
    {
        modConfig::getInstance()->setConfigParam("blExportVars", true);
        modConfig::getInstance()->setConfigParam("blUseStock", true);
        modConfig::setRequestParameter("search", "bar");
        modConfig::setRequestParameter("sExportMinStock", 1);

        $oDb = oxDb::getDb();
        $sO2CView = getViewName('oxobject2category');

        $sHeapTable = "testdynexportbasetable";
        $oDb->execute("CREATE TABLE `{$sHeapTable}` (`oxid` varchar( 32 ) NOT NULL) ENGINE = MYISAM");

        $sCatAdd = "and ( oxobject2category.oxcatnid = '" . $oDb->getOne("select oxcatnid from $sO2CView where oxobjectid='1126'") . "')";

        $oView = new DynExportBase();
        $this->assertTrue($oView->UNITinsertArticles($sHeapTable, $sCatAdd));

        $oArticle = oxNew('oxarticle');
        $sArticleTable = $oArticle->getViewName();

        $sQ = "select count(*) from ( select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} as oxobject2category
               where ( {$sArticleTable}.oxid = oxobject2category.oxobjectid or {$sArticleTable}.oxparentid = oxobject2category.oxobjectid )
               {$sCatAdd} and " . $oArticle->getSqlActiveSnippet() . " and {$sArticleTable}.oxstock >= 1
               and ( {$sArticleTable}.OXTITLE" . $iLanguage . " like '%bar%'
               or {$sArticleTable}.OXSHORTDESC" . $iLanguage . "  like '%bar%'
               or {$sArticleTable}.oxsearchkeys  like '%bar%' )
               group by {$sArticleTable }.oxid) AS counttable";

        $this->assertEquals($oDb->getOne($sQ), $oDb->getOne("select count(*) from {$sHeapTable}"));
    }

    /**
     * DynExportBase::RemoveParentArticles() test case
     *
     * @return null
     */
    public function testRemoveParentArticles()
    {
        $oDb = oxDb::getDb();

        // defining parameters
        $sHeapTable = "testdynexportbasetable";
        $oDb->execute("CREATE TABLE `{$sHeapTable}` (`oxid` varchar( 32 ) NOT NULL) ENGINE = MYISAM");

        $sQ = "insert into {$sHeapTable} ( select oxid from ( select oxid from oxarticles where oxparentid != '' union select oxparentid from oxarticles where oxparentid != '') as toptable group by oxid )";
        $oDb->execute($sQ);

        $oView = new DynExportBase();
        $oView->UNITremoveParentArticles($sHeapTable);

        $sQ1 = "select count(*) from ( select oxid from oxarticles where oxparentid != '') as toptable";
        $sQ2 = "select count(*) from {$sHeapTable}";

        $this->assertEquals($oDb->getOne($sQ1), $oDb->getOne($sQ2));
    }

    /**
     * DynExportBase::SetSessionParams() test case
     *
     * @return null
     */
    public function testSetSessionParams()
    {
        modConfig::setRequestParameter("sExportDelCost", "123;");
        modConfig::setRequestParameter("sExportMinPrice", "123;");
        modConfig::setRequestParameter("sExportCampaign", "123;");
        modConfig::setRequestParameter("blAppendCatToCampaign", "123");
        //#3611
        modConfig::setRequestParameter("sExportCustomHeader", "testHeader");

        $oView = new DynExportBase();
        $oView->UNITsetSessionParams();

        $this->assertEquals("123", oxRegistry::getSession()->getVariable("sExportDelCost"));
        $this->assertEquals("123", oxRegistry::getSession()->getVariable("sExportMinPrice"));
        $this->assertEquals("123", oxRegistry::getSession()->getVariable("sExportCampaign"));
        $this->assertEquals("123", oxRegistry::getSession()->getVariable("blAppendCatToCampaign"));
        //#3611
        $this->assertEquals("testHeader", oxRegistry::getSession()->getVariable("sExportCustomHeader"));
    }

    /**
     * DynExportBase::LoadRootCats() test case
     *
     * @return null
     */
    public function testLoadRootCats()
    {
        $oView = new DynExportBase();
        $aCats = $oView->UNITloadRootCats();

        $aCatIds = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("select oxid from oxcategories");
        foreach ($aCatIds as $aCatInfo) {
            if (!isset($aCats[$aCatInfo["oxid"]])) {
                $this->fail("missing category");
            }
            unset($aCats[$aCatInfo["oxid"]]);
        }
        if (count($aCats)) {
            $this->fail("unknown category");
        }
    }

    /**
     * DynExportBase::FindDeepestCatPath() test case
     *
     * @return null
     */
    public function testFindDeepestCatPathNoCatIdsFound()
    {
        // defining parameters
        $oArticle = $this->getMock("oxArticle", array("getCategoryIds"));
        $oArticle->expects($this->once())->method('getCategoryIds')->will($this->returnValue(array()));

        $oView = new DynExportBase();
        $this->assertEquals("", $oView->UNITfindDeepestCatPath($oArticle));
    }

    /**
     * DynExportBase::FindDeepestCatPath() test case
     *
     * @return null
     */
    public function testFindDeepestCatPath()
    {
        // defining parameters
        $oArticle = $this->getMock("oxArticle", array("getCategoryIds"));
        $oArticle->expects($this->once())->method('getCategoryIds')->will($this->returnValue(array("cat1", "cat2", "cat3")));

        $aCache["cat1"] = new stdClass();
        $aCache["cat1"]->ilevel = 1;
        $aCache["cat1"]->oxtitle = "cat1";
        $aCache["cat1"]->oxparentid = "oxrootid";

        $aCache["cat2"] = new stdClass();
        $aCache["cat2"]->ilevel = 2;
        $aCache["cat2"]->oxtitle = "cat2";
        $aCache["cat2"]->oxparentid = "cat1";

        $aCache["cat3"] = new stdClass();
        $aCache["cat3"]->ilevel = 3;
        $aCache["cat3"]->oxtitle = "cat3";
        $aCache["cat3"]->oxparentid = "cat2";

        $oView = $this->getMock("DynExportBase", array("_loadRootCats"));
        $oView->expects($this->once())->method('_loadRootCats')->will($this->returnValue($aCache));
        $this->assertEquals("cat1/cat2/cat3", $oView->UNITfindDeepestCatPath($oArticle));
    }

    /**
     * DynExportBase::InitArticle() test case
     *
     * @return null
     */
    public function testInitArticleProductIsNotAwailable()
    {
        modDb::getInstance()->addClassFunction('selectLimit', create_function('$s, $i, $c', 'throw new Exception($s.$i.$c);'));
        $oView = new _DynExportBase();
        $blClose = true;
        try {
            $oView->initArticle("testdynexportbasetable", 0, $blClose);
        } catch (Exception $oExcp) {
            $this->assertEquals("select oxid from testdynexportbasetable10", $oExcp->getMessage(), "Error in DynExportBase::InitArticle()");

            return;
        }
        $this->fail("Error in DynExportBase::InitArticle()");
    }

    /**
     * DynExportBase::InitArticle() test case
     *
     * @return null
     */
    public function testInitArticle()
    {
        $blContinue = true;
        modConfig::setRequestParameter("sExportMinPrice", "1");
        $sProdId = '8a142c4113f3b7aa3.13470399';
        $sParentId = '2077';
        $sTitle = 'violett';
        $oParent = new oxArticle();
        $oParent->load($sParentId);

        $oDb = oxDb::getDb();

        // defining parameters
        $sHeapTable = "testdynexportbasetable";
        $oDb->execute("CREATE TABLE `{$sHeapTable}` (`oxid` varchar( 32 ) NOT NULL) ENGINE = MYISAM");
        $oDb->execute("INSERT INTO `{$sHeapTable}` values ( '{$sProdId}' )");

        $oView = new _DynExportBase();
        $oArticle = $oView->initArticle("testdynexportbasetable", 0, $blContinue);
        $this->assertNotNull($oArticle);
        $this->assertTrue($oArticle instanceof oxarticle);
        $this->assertEquals($oParent->oxarticles__oxtitle->value . " " . $sTitle, $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * DynExportBase::SetCampaignDetailLink() test case
     *
     * @return null
     */
    public function testSetCampaignDetailLink()
    {
        // defining parameters
        modConfig::setRequestParameter("sExportCampaign", "testCampaign");
        modConfig::setRequestParameter("blAppendCatToCampaign", 1);

        $oArticle = $this->getMock("oxarticle", array("appendLink"));
        $oArticle->expects($this->at(0))->method('appendLink')->with($this->equalTo("campaign=testCampaign"));
        $oArticle->expects($this->at(1))->method('appendLink')->with($this->equalTo("/testCat"));

        $oView = $this->getMock("DynExportBase", array("getCategoryString"));
        $oView->expects($this->once())->method('getCategoryString')->with($this->isInstanceOf(oxarticle))->will($this->returnValue("testCat"));
        $oView->UNITsetCampaignDetailLink($oArticle);
    }

    /**
     * DynExportBase::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = new DynExportBase();
        $this->assertEquals('dyn_interface', $oView->getViewId());
    }
}
