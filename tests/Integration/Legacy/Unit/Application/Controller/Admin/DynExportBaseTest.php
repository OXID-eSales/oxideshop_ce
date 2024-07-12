<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use DynExportBase;
use Exception;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Application\Model\Article;
use oxDb;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\Facts\Facts;
use oxRegistry;
use oxTestModules;
use stdClass;

/**
 * Tests module for DynExportBase class
 */
class _DynExportBase extends DynExportBase
{
    public function initArticle($heapTable, $count, &$continue)
    {
        return parent::initArticle($heapTable, $count, $continue);
    }

    /**
     * Get private variable.
     *
     * @param string $sName variable name
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
     */
    public function setVar($sName, $sValue)
    {
        $this->{'_' . $sName} = $sValue;
    }
}

/**
 * Tests for DynExportBase class
 */
class DynExportBaseTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("drop TABLE if exists testdynexportbasetable");

        parent::tearDown();
    }

    /**
     * DynExportBase::Construct() test case
     */
    public function testConstruct()
    {
        $sFilePath = $this->getConfig()->getConfigParam('sShopDir') . "/export/dynexport.txt";

        $oView = $this->getProxyClass("DynExportBase");
        $this->assertEquals($sFilePath, $oView->getNonPublicVar("_sFilePath"));
    }

    /**
     * DynExportBase::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('DynExportBase');
        $this->assertEquals('dynexportbase', $oView->render());
    }

    /**
     * DynExportBase::CreateMainExportView() test case
     */
    public function testCreateMainExportView()
    {

        // testing..
        oxTestModules::addFunction('oxCategoryList', 'loadList', '{ throw new Exception( "buildList" ); }');

        // testing..
        try {
            $oView = oxNew('DynExportBase');
            $oView->createMainExportView();
        } catch (Exception $exception) {
            $this->assertEquals("buildList", $exception->getMessage(), "error in DynExportBase::createMainExportView()");

            return;
        }

        $this->fail("error in DynExportBase::createMainExportView()");
    }

    /**
     * DynExportBase::Start() test case
     */
    public function testStart()
    {
        $testFile = $this->createFile('test.txt', '');
        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\_DynExportBase::class, ["prepareExport"]);
        $oView->expects($this->once())->method('prepareExport')->will($this->returnValue(5));
        $oView->setVar('sFilePath', $testFile);
        $oView->start();
        $this->assertEquals(0, $oView->getViewDataElement("refresh"));
        $this->assertEquals(0, $oView->getViewDataElement("iStart"));
        $this->assertEquals(5, $oView->getViewDataElement("iEnd"));
    }

    /**
     * DynExportBase::Stop() test case
     */
    public function testStop()
    {
        $oDb = oxDb::getDb();
        $sTableName = 'testdynexportbasetable';
        $oDb->execute(sprintf('CREATE TABLE `%s` (`oxid` TINYINT( 1 ) NOT NULL) ENGINE = InnoDB', $sTableName));
        $this->assertEquals(0, $oDb->getOne('select count(*) from ' . $sTableName));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController::class, ["getHeapTableName"]);
        $oView->expects($this->once())->method('getHeapTableName')->will($this->returnValue($sTableName));
        $oView->stop(999);

        $this->assertEquals(999, $oView->getViewDataElement("iError"));
        $this->assertEquals(0, count($oDb->getAll(sprintf('show tables like \'%s\'', $sTableName))));
    }

    /**
     * DynExportBase::NextTick() test case
     */
    public function testNextTick()
    {
        // defining parameters
        $oView = oxNew('DynExportBase');
        $this->assertFalse($oView->nextTick(999));
    }

    /**
     * DynExportBase::Write() test case
     */
    public function testWrite()
    {
        $testFile = $this->createFile('test.txt', '');
        $sLine = 'TestExport';

        $oView = oxNew('DynExportBase');
        $oView->fpFile = @fopen($testFile, "w");
        $oView->write($sLine);
        fclose($oView->fpFile);

        $sFileCont = file_get_contents($testFile, true);
        $this->assertEquals($sLine . "\r\n", $sFileCont);
    }

    /**
     * DynExportBase::Run() test case
     */
    public function testRun()
    {
        $this->setRequestParameter("iStart", 0);
        $this->setRequestParameter("aExportResultset", ["aaaaa"]);
        $testFile = $this->createFile('test.txt', '');

        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\_DynExportBase::class, ["nextTick"]);
        $oView->expects($this->any())->method('nextTick')->will($this->returnValue(5));
        $oView->setVar('sFilePath', $testFile);
        $oView->setExportPerTick(30);
        $oView->run();
        $this->assertEquals(0, $oView->getViewDataElement("refresh"));
        $this->assertEquals(30, $oView->getViewDataElement("iStart"));
        $this->assertEquals(5, $oView->getViewDataElement("iExpItems"));
    }

    /**
     * DynExportBase::Run() test case with default per tick count
     */
    public function testRunWithDefaultConfigPerTickCount()
    {
        $this->setRequestParameter("iStart", 0);
        $this->setRequestParameter("aExportResultset", ["aaaaa"]);
        $this->getConfig()->setConfigParam("iExportNrofLines", 10);
        $testFile = $this->createFile('test.txt', '');

        $oView = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\_DynExportBase::class, ["nextTick"]);
        $oView->expects($this->any())->method('nextTick')->will($this->returnValue(5));
        $oView->setVar('sFilePath', $testFile);
        $oView->run();
        $this->assertEquals(0, $oView->getViewDataElement("refresh"));
        $this->assertEquals(10, $oView->getViewDataElement("iStart"));
        $this->assertEquals(5, $oView->getViewDataElement("iExpItems"));
    }

    /**
     * test setting and getting export per tick count
     */
    public function testSetGetExportPerTick()
    {
        $oView = oxNew('DynExportBase');

        // if not set yet, should take value from config
        $this->getConfig()->setConfigParam("iExportNrofLines", 150);
        $this->assertEquals(150, $oView->getExportPerTick());

        // if not set in config, should use default value
        $oView->setExportPerTick(null);
        $this->getConfig()->setConfigParam("iExportNrofLines", 0);
        $this->assertEquals($oView->iExportPerTick, $oView->getExportPerTick());

        // Should be able to set this value too
        $oView->setExportPerTick(190);
        $this->assertEquals(190, $oView->getExportPerTick());
    }

    /**
     * DynExportBase::RemoveSID() test case
     */
    public function testRemoveSid()
    {
        $sSid = oxRegistry::getSession()->getId();

        // defining parameters
        $sInput = sprintf('testStartsid=%s/sid/%s/sid=%s&amp;sid=%s&sid=%sTestEnd', $sSid, $sSid, $sSid, $sSid, $sSid);

        $oView = oxNew('DynExportBase');
        $this->assertEquals("testStartTestEnd", $oView->removeSid($sInput));
    }

    /**
     * DynExportBase::Shrink() test case
     */
    public function testShrink()
    {
        // defining parameters
        $sInput = "testStart\r\n\n\tTestEnd";
        $sOutput = "testStart ...";

        // testing..
        $oView = oxNew('DynExportBase');

        $this->assertEquals($sOutput, $oView->shrink($sInput, 15, true));
    }

    /**
     * DynExportBase::GetCategoryString() test case
     */
    public function testGetCategoryString()
    {
        $sOxid = '1126';
        $sCatString = 'Geschenke/Bar-Equipment';
        if ((new Facts())->getEdition() === 'EE') {
            $sCatString = 'Party/Bar-Equipment';
        }

        // defining parameters
        $oArticle = oxNew('oxArticle');
        $oArticle->load($sOxid);

        $oView = oxNew('DynExportBase');
        $this->assertEquals($sCatString, $oView->getCategoryString($oArticle));
    }

    /**
     * DynExportBase::GetDefaultCategoryString() test case
     */
    public function testGetDefaultCategoryString()
    {
        $sOxid = '1126';
        $sCatString = 'Bar-Equipment';

        // defining parameters
        $oArticle = oxNew('oxArticle');
        $oArticle->load($sOxid);

        $oView = oxNew('DynExportBase');
        $this->assertEquals($sCatString, $oView->getDefaultCategoryString($oArticle));
    }

    /**
     * DynExportBase::PrepareCSV() test case
     */
    public function testPrepareCSV()
    {
        // defining parameters
        $sInput = 'testStart&nbsp;&euro;|TestStop';
        $sOutput = '"testStart TestStop"';

        // testing..
        $oView = oxNew('DynExportBase');
        $this->assertEquals($sOutput, $oView->prepareCSV($sInput));
    }

    /**
     * DynExportBase::PrepareXML() test case
     */
    public function testPrepareXML()
    {
        // defining parameters
        $sInput = 'testStart&"><\'TestStop';
        $sOutput = 'testStart&amp;&quot;&gt;&lt;&apos;TestStop';

        $oView = oxNew('DynExportBase');
        $this->assertEquals($sOutput, $oView->prepareXML($sInput));
    }

    /**
     * DynExportBase::GetDeepestCategoryPath() test case
     */
    public function testGetDeepestCategoryPath()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController::class, ["findDeepestCatPath"]);
        $oView->expects($this->once())->method('findDeepestCatPath')->with($this->isInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class));
        $oView->getDeepestCategoryPath(oxNew('oxarticle'));
    }

    /**
     * DynExportBase::PrepareExport() test case
     */
    public function testPrepareExport()
    {
        $this->setRequestParameter("acat", "testCatId");
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{}');

        $oView = $this->getMock(
            "DynExportBase",
            ["getHeapTableName", "generateTableCharSet", "createHeapTable", "getCatAdd", "insertArticles", "removeParentArticles", "setSessionParams"]
        );
        $oView->expects($this->once())->method('getHeapTableName')->will($this->returnValue("oxarticles"));
        $oView->expects($this->once())->method('generateTableCharSet')->will($this->returnValue("testCharSet"));
        $oView->expects($this->once())->method('createHeapTable')->with($this->equalTo("oxarticles"), $this->equalTo("testCharSet"))->will($this->returnValue(false));
        $oView->expects($this->once())->method('getCatAdd')->with($this->equalTo("testCatId"))->will($this->returnValue("testCatId"));
        $oView->expects($this->once())->method('insertArticles')->with($this->equalTo("oxarticles"), $this->equalTo("testCatId"))->will($this->returnValue(false));
        $oView->expects($this->once())->method('removeParentArticles')->with($this->equalTo("oxarticles"));
        $oView->expects($this->once())->method('setSessionParams');

        $this->assertEquals(oxDb::getDb()->getOne("select count(*) from oxarticles"), $oView->prepareExport());
    }

    /**
     * DynExportBase::GetOneArticle() test case
     */
    public function testGetOneArticle()
    {
        $blContinue = null;
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController::class, ["initArticle", "getHeapTableName", "setCampaignDetailLink"]);
        $oView->expects($this->once())->method('initArticle')->with($this->equalTo("oxarticles"), $this->equalTo(0))->will($this->returnValue(oxNew('oxarticle')));
        $oView->expects($this->once())->method('getHeapTableName')->will($this->returnValue("oxarticles"));
        $oView->expects($this->once())->method('setCampaignDetailLink')->with($this->isInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class))->will($this->returnValue(oxNew('oxarticle')));

        $this->assertTrue($oView->getOneArticle(0, $blContinue) instanceof article);
        $this->assertTrue($blContinue);
    }

    /**
     * DynExportBase::AssureContent() test case
     */
    public function testAssureContent()
    {
        // defining parameters
        $oView = oxNew('DynExportBase');
        $this->assertEquals("test", $oView->assureContent("test"));
        $this->assertEquals("-", $oView->assureContent(""));
    }

    /**
     * DynExportBase::UnHTMLEntities() test case
     */
    public function testUnHtmlEntities()
    {
        $oView = oxNew('DynExportBase');
        $this->assertEquals("&", $oView->unHtmlEntities("&amp;"));
        $this->assertEquals('"', $oView->unHtmlEntities("&quot;"));
        $this->assertEquals(">", $oView->unHtmlEntities("&gt;"));
        $this->assertEquals("<", $oView->unHtmlEntities("&lt;"));
        $this->assertEquals("test", $oView->unHtmlEntities("test"));
    }

    /**
     * DynExportBase::GetHeapTableName() test case
     */
    public function testGetHeapTableName()
    {
        // testing..
        $oView = oxNew('DynExportBase');
        $this->assertEquals("tmp_" . str_replace("0", "", md5((string) oxRegistry::getSession()->getId())), $oView->getHeapTableName());
    }

    /**
     * DynExportBase::GenerateTableCharSet() test case
     */
    public function testGenerateTableCharSet()
    {
        // defining parameters
        $oView = oxNew('DynExportBase');
        $this->assertEquals("DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci", $oView->generateTableCharSet(5));
        $this->assertEquals("", $oView->generateTableCharSet(3));
    }

    /**
     * DynExportBase::CreateHeapTable() test case
     */
    public function testCreateHeapTable()
    {
        // defining parameters
        $sHeapTable = "testdynexportbasetable";
        $sTableCharset = "DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci";

        $oView = oxNew('DynExportBase');
        $this->assertTrue($oView->createHeapTable($sHeapTable, $sTableCharset));
        $this->assertEquals(0, oxDb::getDb()->getOne('select count(*) from ' . $sHeapTable));
    }

    /**
     * DynExportBase::GetCatAdd() test case
     */
    public function testGetCatAdd()
    {
        $sQ = " and ( oxobject2category.oxcatnid = 'catId1' or oxobject2category.oxcatnid = 'catId2' or oxobject2category.oxcatnid = 'catId3')";

        // defining parameters
        $oView = oxNew('DynExportBase');
        $this->assertNull($oView->getCatAdd([]));
        $this->assertNull($oView->getCatAdd("something"));
        $this->assertEquals($sQ, $oView->getCatAdd(["catId1", "catId2", "catId3"]));
    }

    /**
     * DynExportBase::InsertArticles() test case
     */
    public function testInsertArticlesNoVariantsNoCategoryFilterNoSearchParamNoStockCheck()
    {
        $this->getConfig()->setConfigParam("blExportVars", false);
        $this->getConfig()->setConfigParam("blUseStock", false);
        $this->setRequestParameter("search", false);
        $this->setRequestParameter("sExportMinStock", false);

        $sHeapTable = "testdynexportbasetable";
        $sCatAdd = '';

        $oDb = oxDb::getDb();
        $oDb->execute(sprintf('CREATE TABLE `%s` (`oxid` TINYINT( 1 ) NOT NULL) ENGINE = InnoDB', $sHeapTable));

        $oView = oxNew('DynExportBase');
        $this->assertTrue($oView->insertArticles($sHeapTable, $sCatAdd));

        $oArticle = oxNew('oxArticle');
        $sArticleTable = $oArticle->getViewName();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');

        $iRealCnt = $oDb->getOne(sprintf('select count(*) from ( select %s.oxid from %s, %s where %s.oxid = %s.oxobjectid and %s.oxparentid = \'\' and ', $sArticleTable, $sArticleTable, $sO2CView, $sArticleTable, $sO2CView, $sArticleTable) . $oArticle->getSqlActiveSnippet() . sprintf(' group by %s.oxid) AS counttable', $sArticleTable));
        $iCurrCnt = $oDb->getOne('select count(*) from ' . $sHeapTable);
        $this->assertEquals($iRealCnt, $iCurrCnt);
    }

    /**
     * DynExportBase::InsertArticles() test case
     */
    public function testInsertArticles()
    {
        $this->getConfig()->setConfigParam("blExportVars", true);
        $this->getConfig()->setConfigParam("blUseStock", true);
        $this->setRequestParameter("search", "bar");
        $this->setRequestParameter("sExportMinStock", 1);

        $oDb = oxDb::getDb();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');

        $sHeapTable = "testdynexportbasetable";
        $oDb->execute(sprintf('CREATE TABLE `%s` (`oxid` varchar( 32 ) NOT NULL) ENGINE = InnoDB', $sHeapTable));

        $sCatAdd = "and ( oxobject2category.oxcatnid = '" . $oDb->getOne(sprintf('select oxcatnid from %s where oxobjectid=\'1126\'', $sO2CView)) . "')";

        $oView = oxNew('DynExportBase');
        $this->assertTrue($oView->insertArticles($sHeapTable, $sCatAdd));

        $oArticle = oxNew('oxArticle');
        $sArticleTable = $oArticle->getViewName();

        $sQ = "select count(*) from ( select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} as oxobject2category
               where ( {$sArticleTable}.oxid = oxobject2category.oxobjectid or {$sArticleTable}.oxparentid = oxobject2category.oxobjectid )
               {$sCatAdd} and " . $oArticle->getSqlActiveSnippet() . " and {$sArticleTable}.oxstock >= 1
               and ( {$sArticleTable}.OXTITLE" . $iLanguage . " like '%bar%'
               or {$sArticleTable}.OXSHORTDESC" . $iLanguage . "  like '%bar%'
               or {$sArticleTable}.oxsearchkeys  like '%bar%' )
               group by {$sArticleTable }.oxid) AS counttable";

        $this->assertEquals($oDb->getOne($sQ), $oDb->getOne('select count(*) from ' . $sHeapTable));
    }

    /**
     * DynExportBase::RemoveParentArticles() test case
     */
    public function testRemoveParentArticles()
    {
        $oDb = oxDb::getDb();

        // defining parameters
        $sHeapTable = "testdynexportbasetable";
        $oDb->execute(sprintf('CREATE TABLE `%s` (`oxid` varchar( 32 ) NOT NULL) ENGINE = InnoDB', $sHeapTable));

        $sQ = sprintf('insert into %s ( select oxid from ( select oxid from oxarticles where oxparentid != \'\' union select oxparentid from oxarticles where oxparentid != \'\') as toptable group by oxid )', $sHeapTable);
        $oDb->execute($sQ);

        $oView = oxNew('DynExportBase');
        $oView->removeParentArticles($sHeapTable);

        $sQ1 = "select count(*) from ( select oxid from oxarticles where oxparentid != '') as toptable";
        $sQ2 = 'select count(*) from ' . $sHeapTable;

        $this->assertEquals($oDb->getOne($sQ1), $oDb->getOne($sQ2));
    }

    /**
     * DynExportBase::SetSessionParams() test case
     */
    public function testSetSessionParams()
    {
        $this->setRequestParameter("sExportDelCost", "123;");
        $this->setRequestParameter("sExportMinPrice", "123;");
        $this->setRequestParameter("sExportCampaign", "123;");
        $this->setRequestParameter("blAppendCatToCampaign", "123");
        //#3611
        $this->setRequestParameter("sExportCustomHeader", "testHeader");

        $oView = oxNew('DynExportBase');
        $oView->setSessionParams();

        $this->assertEquals("123", oxRegistry::getSession()->getVariable("sExportDelCost"));
        $this->assertEquals("123", oxRegistry::getSession()->getVariable("sExportMinPrice"));
        $this->assertEquals("123", oxRegistry::getSession()->getVariable("sExportCampaign"));
        $this->assertEquals("123", oxRegistry::getSession()->getVariable("blAppendCatToCampaign"));
        //#3611
        $this->assertEquals("testHeader", oxRegistry::getSession()->getVariable("sExportCustomHeader"));
    }

    /**
     * DynExportBase::LoadRootCats() test case
     */
    public function testLoadRootCats()
    {
        $oView = oxNew('DynExportBase');
        $aCats = $oView->loadRootCats();

        $aCatIds = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("select oxid from oxcategories");
        foreach ($aCatIds as $aCatInfo) {
            if (!isset($aCats[$aCatInfo["oxid"]])) {
                $this->fail("missing category");
            }

            unset($aCats[$aCatInfo["oxid"]]);
        }

        if (count($aCats) > 0) {
            $this->fail("unknown category");
        }
    }

    /**
     * DynExportBase::FindDeepestCatPath() test case
     */
    public function testFindDeepestCatPathNoCatIdsFound()
    {
        // defining parameters
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getCategoryIds"]);
        $oArticle->expects($this->once())->method('getCategoryIds')->will($this->returnValue([]));

        $oView = oxNew('DynExportBase');
        $this->assertEquals("", $oView->findDeepestCatPath($oArticle));
    }

    /**
     * DynExportBase::FindDeepestCatPath() test case
     */
    public function testFindDeepestCatPath()
    {
        // defining parameters
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getCategoryIds"]);
        $oArticle->expects($this->once())->method('getCategoryIds')->will($this->returnValue(["cat1", "cat2", "cat3"]));

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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController::class, ["loadRootCats"]);
        $oView->expects($this->once())->method('loadRootCats')->will($this->returnValue($aCache));
        $this->assertEquals("cat1/cat2/cat3", $oView->findDeepestCatPath($oArticle));
    }

    /**
     * DynExportBase::InitArticle() test case
     */
    public function testInitArticleProductIsNotAvailable()
    {
        $heapTableName = "testdynexportbasetable";

        $databaseMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database::class, ['selectLimit']);
        $databaseMock->expects($this->any())
            ->method('selectLimit')
            ->with($this->equalTo('select oxid from ' . $heapTableName));

        $dynamicExportControllerMock = $this->getMock(\OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicExportBaseController::class, ['getDb', 'getHeapTableName']);
        $dynamicExportControllerMock->expects($this->any())->method('getDb')->willReturn($databaseMock);
        $dynamicExportControllerMock->expects($this->any())->method('getHeapTableName')->willReturn($heapTableName);

        $close = true;
        $dynamicExportControllerMock->getOneArticle($heapTableName, $close);
    }

    /**
     * DynExportBase::InitArticle() test case
     */
    public function testInitArticle()
    {
        $blContinue = true;
        $this->setRequestParameter("sExportMinPrice", "1");
        $sProdId = '8a142c4113f3b7aa3.13470399';
        $sParentId = '2077';
        $sTitle = 'violett';
        if ((new Facts())->getEdition() === 'EE') {
            $sProdId = '1661-02';
            $sParentId = '1661';
            $sTitle = 'Bayer';
        }

        $oParent = oxNew('oxArticle');
        $oParent->load($sParentId);

        $oDb = oxDb::getDb();

        // defining parameters
        $sHeapTable = "testdynexportbasetable";
        $oDb->execute(sprintf('CREATE TABLE `%s` (`oxid` varchar( 32 ) NOT NULL) ENGINE = InnoDB', $sHeapTable));
        $oDb->execute(sprintf('INSERT INTO `%s` values ( \'%s\' )', $sHeapTable, $sProdId));

        $oView = new _DynExportBase();
        $oArticle = $oView->initArticle("testdynexportbasetable", 0, $blContinue);
        $this->assertNotNull($oArticle);
        $this->assertTrue($oArticle instanceof \OxidEsales\EshopCommunity\Application\Model\Article);
        $this->assertEquals($oParent->oxarticles__oxtitle->value . " " . $sTitle, $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * DynExportBase::SetCampaignDetailLink() test case
     */
    public function testSetCampaignDetailLink()
    {
        // defining parameters
        $this->setRequestParameter("sExportCampaign", "testCampaign");
        $this->setRequestParameter("blAppendCatToCampaign", 1);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["appendLink"]);
        $oArticle
            ->method('appendLink')
            ->withConsecutive(['campaign=testCampaign'], ['/testCat']);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController::class, ["getCategoryString"]);
        $oView->expects($this->once())->method('getCategoryString')->with($this->isInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class))->will($this->returnValue("testCat"));
        $oView->setCampaignDetailLink($oArticle);
    }

    /**
     * DynExportBase::GetViewId() test case
     */
    public function testGetViewId()
    {
        $oView = oxNew('DynExportBase');
        $this->assertEquals('dyn_interface', $oView->getViewId());
    }
}
