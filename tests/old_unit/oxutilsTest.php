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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'OxidTestCase.php';
require_once 'test_config.inc.php';

// Test derivations so to stub some functions
// REVIEW - this was done in innocence ;-)
// 20070810-AS - Commented out
//class testBaseOxUtils extends oxUtils {
//    public function ___getClassNameArray() {
//        return $this->_aClassNameCache;
//    }
//}
//
//class testOxUtils extends testBaseOxUtils {
//    // make stubs
//    public function showMessageAndExit($sMsg) {
//        throw new Exception ($sMsg);
//    }
//}

/**
 * @author  AS 20070813
 * Derived class from oxUtils to test protected functions
 * @todo to be completed
 */
class oxUtilsTestTest extends oxUtils{
    public function getClassFileTest($ClassName) {
        return $this->_getClassFile($ClassName);
    }

    public function getCatCacheTest() {
        return $this->_getCatCache();
    }

    public function setCatCacheTest($aCache) {
        $this->_setCatCache($aCache);
    }

    public function getVendorCacheTest() {
        return $this->_getVendorCache();
    }

    public function setVendorCacheTest($aCache) {
        $this->_setVendorCache($aCache);
    }

    public function getUserViewIdTest($blReset = false) {
        return $this->_getUserViewId($blReset);
    }

    public function addUrlParametersTest($sUrl, $aParams) {
        return $this->_addUrlParameters($sUrl, $aParams);
    }
}

class modUtils_oxUtils extends oxUtils {
    // needed 4 modOXID
    public static $unitMOD = null;
    protected static $_inst = null;

    function modAttach(){
        //parent::modAttach();
        $this->oRealInstance = getInstance();
        self::$unitMOD = $this;
    }

    public static function getInstance(){
        return parent::getInstance();
    }

    public function cleanup(){
        self::$unitMOD = null;
        parent::cleanup();
    }

    public function _getStaticCache() {
        return $this->_aStaticCache;
    }

    public function _setStaticCache($sName, $Content, $key = null) {
        if( $key) {
            $this->_aStaticCache[$sName][$key] = $Content;
        } else {
            $this->_aStaticCache_[$sName] = $Content;
        }
    }
    /**
     * 20070810-AS
     * overrides parent function, as it would interfere with tests
     *
     */
    public function showMessageAndExit($sMsg) {
        throw new Exception ($sMsg);
    }

    public function getSEOActive() {
        return $this->_blSEOIsActive;
    }

    public function setSEOActive($blActive = true) {
        $this->_blSEOIsActive = $blActive;
    }

    public function resetLangCache(){
        $this->_aLangCache = null;
    }

    // needed 4 oxConfig
    static function getParameter(  $paramName, $blRaw = false) {
        // should throw exception if original functionality is needed.
        return self::getInstance()->params[$paramName];
    }

    static function setParameter( $paramName, $paramValue ) {
        // should throw exception if original functionality is needed.
        self::getInstance()->params[$paramName] = $paramValue;
    }

    public function getListTypeParamDirectly() {
        return $this->_sListType;
    }

    public function getCategoryIDParamDirectly() {
        return $this->_sCategoryID;
    }
}

class Unit_oxutilsTest extends OxidTestCase {
    public static $test_sql_used = null;
    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     * @access protected
     */
    protected function setUp() {
        //neccessary due to avoid dependencies to other tests... it was impossible to hunt down the destructive test F
        $myConfig->oRRoles = null;

        oxAddClassModule('modConfig', 'oxConfig');
        oxAddClassModule('modUtils_oxUtils', 'oxutils');
        oxAddClassModule('modSession', 'oxSession');
        oxAddClassModule('modDB', 'oxConfig');

        modConfig::getInstance()->cleanup();

        modConfig::getInstance()->addClassFunction( 'hasModule', create_function( '$sModule', 'return true;') );
     //   $sFileName = oxConfig::getInstance()->sShopDir."/out/admin/html/0/templates";
      //  modConfig::getInstance()->addClassFunction('getTemplateDir', create_function('', "return '$sFileName';"));
    }
    /**
     * Tears down the fixture.
     * This method is called after a test is executed.
     * @access protected
     */
    protected function tearDown() {
        $this->_clearNavParams();
        modConfig::getInstance()->cleanup();
        modSession::getInstance()->cleanup();
        modDB::getInstance()->cleanup();
        oxRemClassModule('modUtils_oxUtils');
        modDB::getInstance()->cleanup();
        parent::tearDown();
    }

    public function test_GetInstance() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $this->assertEquals($myUtils, getInstance());
//        ___testProperty = 'Whatever';
//        $this->assertEquals(___testProperty(), 'Whatever');
//        unset(___testProperty);
//        $this->assertEquals(&$myUtils, getInstance());

    }

    public function test_GetSmarty() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();

        modConfig::getInstance()->addClassVar('iDebug', 0);
        modConfig::getInstance()->addClassFunction('isProductiveMode', create_function('', "return true;"));
        modConfig::getInstance()->addClassVar('blAdmin', true);

        $oSmarty = getSmarty();
        $this->assertEquals(false, $oSmarty->debugging);

        $this->assertEquals($myConfig->blProductive, $oSmarty->compile_check);
        $this->assertContains("admin", $oSmarty->compile_id);

        modConfig::getInstance()->addClassVar('iDebug', 3);
        modConfig::getInstance()->addClassVar('blAdmin', false);
        modConfig::getInstance()->addClassFunction('isProductiveMode', create_function('', "return false;"));

        $oSmarty = getSmarty();
        $this->assertEquals(true, $oSmarty->debugging);
        $this->assertEquals($myConfig->blCheckTemplates, $oSmarty->compile_check);
        $this->assertNotContains("admin", $oSmarty->compile_id);
        $this->assertEquals(true, $oSmarty->security);
     }

    public function test_GetActiveShop() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();

        $oExpectedShop = oxNew( "oxshop" );
        $oExpectedShop->Load( $myConfig->getShopId() );
        $oTestShop = getActiveShop();
        $this->assertEquals($oExpectedShop, $oTestShop);

        modConfig::getInstance()->addClassVar('oActShop', $oExpectedShop);
        $oTestShop = getActiveShop();
        $this->assertEquals($oExpectedShop, $oTestShop);
    }

    public function test_GetSerial() {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $oDB = oxDB::getDb();

        $iShopID = oxConfig::getInstance()->getShopId();
        $sResult = $oDB->GetOne("select oxserial from oxshops where oxid = '$iShopID'");
        $this->assertEquals($sResult, getSerial()->sSerial);

        $sBackUp = oxConfig::getInstance()->oSerial;
        $sBackUpSession = oxSession::getInstance()->getVar( "1"."oxserial");
        unset(oxConfig::getInstance()->oSerial);
        oxSession::getInstance()->setVar( "1"."oxserial", null);
        $this->assertEquals($sResult, getSerial()->sSerial);
        oxConfig::getInstance()->oSerial = $sBackUp;
        oxSession::getInstance()->setVar( "1"."oxserial", $sBackUpSession);

    }

    public function test_GetClassFile() {
        $myUtilsTest = new oxUtilsTestTest();
        $aTestArray = array('oxCategory' => 'oxCategory', 'oxutils' => 'modUtils_oxUtils');

        foreach($aTestArray as $ClassName => $ClassFile) {
           $this->assertEquals($myUtilsTest->getClassFileTest($ClassName),$ClassFile);
        }
    }

    public function test_OxNewArticle() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $oArticle = oxNew("oxarticle", "core");
        $oArticle->Load("2177");

        // 20070808-AS - without additional properties
        $oNewArticle = oxNewArticle("2177");
        $this->assertTrue($oArticle == $oNewArticle);
        unset($oArticle);

        // 20070808-AS - modify properties
        $oArticle = oxNew("oxarticle", "core");
        $oArticle->blDontLoadPrice = true;
        $oArticle->blCalcPrice = false;
        $oArticle->Load("2177");

        $aProperties = array('blDontLoadPrice' => true, 'blCalcPrice' => false);
        $oNewArticle = oxNewArticle("2177", $aProperties);
        $this->assertTrue($oArticle == $oNewArticle);
        unset($oArticle);

        // 20070808-AS - with set blAllowArticlesubclass
        $oArticle = oxNew("oxarticle", "core");
        $oArticle->Load("2177");

        // 20070808-AS - without additional properties
        modConfig::getInstance()->addClassVar('blAllowArticlesubclass', true);
        $oNewArticle = oxNewArticle("2177");
        $this->assertTrue($oArticle == $oNewArticle);
    }

    /**
     * @todo review when exception handling is implemented
     */
    public function test_OxNew() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();
        // 20070808-AS - check known classnames
        $oArticle = oxNew('oxarticle', 'core');
        $this->assertTrue($oArticle instanceof oxarticle);
        try {
           // $sShopDir = substr_replace($myConfig->sShopDir, "tests/misc", (strrpos($myConfig->sShopDir, "/",-2)) + 1);
            $sShopDir = "misc/";
            $aModules = array(strtolower('oxNewDummyModule') => 'oxNewDummyUserModule&oxNewDummyUserModule2');
            modConfig::getInstance()->addClassVar("aModules", $aModules);
            modConfig::getInstance()->addClassVar("sShopDir", $sShopDir); // not working yet
            require_once ($sShopDir."/modules/oxNewDummyModule.php");

            $oNewDummyModule = modUtils_oxNew("oxNewDummyModule", 'core');
            $this->assertTrue($oNewDummyModule instanceof oxNewDummyModule);
            $oNewDummyUserModule = modUtils_oxNew("oxNewDummyUserModule");
            $this->assertTrue($oNewDummyModule instanceof $oNewDummyUserModule);
            $oNewDummyUserModule2 = modUtils_oxNew("oxNewDummyUserModule2");
            $this->assertTrue($oNewDummyModule instanceof $oNewDummyUserModule2);
            try {
                // This code is expected to raise an exception ...
                $oNewExc = modUtils_oxNew("non_existing_class");
            } catch (Exception $expected) {
                // Expected result if oxNew cannot create an object.
                $this->assertTrue(true);
                return;
            }

            $this->fail('An expected Exception has not been raised.');

        } catch (Exception $e) {
            // Debuging test only - remove later on ...
            echo "\n\n Exc: ".$e->getFile().", ".$e->getLine().", ".$e->getMessage().", ".$e->getTraceAsString()."\n\n";
            $this->fail('An exception was raised.');
        }
    }

    public function test_OxCopyNew() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $oArticle = oxNew("oxarticle");
        $oArticle->Load("2177");

        $oRet = oxCopyNew( $oArticle, $location, $params);
        $this->assertTrue(($oArticle == $oRet) && ($oArticle instanceof oxarticle) && ($oRet instanceof oxarticle));
    }

    public function test_IsQuoteNeeded() {

        $aNonQuoteTypes = array('int', 'decimal', 'float', 'tinyint', 'smallint', 'mediumint', 'bigint', 'double');
        // types "integer", "real", "numeric", "blob" missing ???
        foreach ($aNonQuoteTypes as $values) {
            $this->assertFalse(isQuoteNeeded($values));
        }

        $aQuoteTypes = array('date', 'datetime', 'timestamp', 'time', 'year', 'char', 'varchar', 'text', 'enum', 'set');
        foreach ($aQuoteTypes as $values) {
            $this->assertTrue(isQuoteNeeded($values));
        }

    }

    /**
     * @todo should not write into source folder aka. getShopURL()
     */
    public function test_ReadRemoteFile() {

        $myConfig = oxConfig::getInstance();
        $sPath = $myConfig->getShopURL();
        $sFileName = 'test.html';
        $sData = '<html>';
        $sData .= '<head>';
        $sData .= '<title>';
        $sData .= 'A title for a test file';
        $sData .= '</title>';
        $sData .= '</head>';
        $sData .= '<body>';
        $sData .= 'Some test text!';
        $sData .= '</body>';
        $sData .= '</html>';
        $sWritePath = "..".DIRECTORY_SEPARATOR.$sFileName; //"source".DIRECTORY_SEPARATOR.
        $hFile = fopen($sWritePath, 'w');
        if(!$hFile) {
            $this->fail("Failed to open file!");
        }
        $mRet = fwrite($hFile, $sData);
        if(false == $mRet) {
            $this->fail("Failed to write file!");
        }
        if(!fclose($hFile)) {
            $this->fail("Failed to close file!");
        }
        $sReadPath = $sWritePath;
        $this->assertEquals(readRemoteFile($sReadPath), $sData);
        if(!unlink($sWritePath))  {
            $this->fail("Failed to delete test file!");
        }
    }

    public function test_GetTableDescription() {
        $myConfig = oxConfig::getInstance();
        $rs = oxDb::getDb()->Execute( "show tables");
        $icount = 3;
        if ($rs != false && $rs->RecordCount() > 0)
        {
            while (!$rs->EOF && $icount--)
            {
                $sTable = $rs->fields[0];
                if ( OXID_VERSION_EE ) {
                    @unlink(oxPATH."tmp/oxc_tbdsc_$sTable.txt");
                }
                $amc = oxDB::getDb()->MetaColumns($sTable);
                // db retr
                $rmc1 = GetTableDescription($sTable);
                // simple cache
                $rmc2 = GetTableDescription($sTable);
                $this->assertEquals( $amc, $rmc1, "not cached return is bad [shouldn't be] of $sTable.");
                $this->assertEquals( $amc, $rmc2, "cached [simple] return is bad of $sTable.");
                if ( OXID_VERSION_EE ) {
                    @unlink(oxPATH."tmp/oxc_tbdsc_$sTable.txt");
                }
                $rs->MoveNext();
            }
        }else $this->fail("no tables???");
    }
    // 20070801-AS - START
    public function test_GetArrFldName()
    {
        $sTestString = ".S.o.me.. . Na.me.";
        $sShouldBeResult = "__S__o__me____ __ Na__me__";

        $this->assertEquals($sShouldBeResult, getArrFldName($sTestString));
    }

    public function test_GenerateUID()
    {

        $mySession = oxSession::getInstance();
        $suID = substr($mySession->id, 0, 3) . uniqid( "", true);
        $suIDUnEqual = substr($mySession->id, 0, 3) . uniqid( "", true);
        $this->assertEquals(strlen($suID) , strlen(generateUID()));
        $this->assertEquals(substr($suID, -9, 1), ".");
        $this->assertNotEquals($suID, $suIDUnEqual);
    }

    public function test_AssignValuesFromText()
    {

        $myConfig = oxConfig::getInstance();
        $oCurrency = $myConfig->getActShopCurrencyObject();
        modConfig::getInstance()->addClassVar('blAdmin', false);
        modConfig::getInstance()->addClassVar('bl_perfLoadSelectLists', true);
        modConfig::getInstance()->addClassVar('bl_perfUseSelectlistPrice', true);
        $sTestString = "one!P!99.5%__oneValue@@two!P!12,41__twoValue@@three!P!-5,99__threeValue@@Lagerort__Lager 1@@";
        $aResult = assignValuesFromText($sTestString);
        $aShouldBe = array();
        $oObject = new stdClass();
        $oObject->price = '99.5';
        $oObject->priceUnit = '%';
        $oObject->fprice = '99.5%';
        $oObject->name = 'one +99.5%';
        $oObject->value = 'oneValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->price = '12,41';
        $oObject->fprice = '12,00';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'two +12,00 '.$oCurrency->sign;
        $oObject->value = 'twoValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->price = '-5,99';
        $oObject->fprice = '-5,00';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'three -5,00 '.$oCurrency->sign;
        $oObject->value = 'threeValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->name = 'Lagerort';
        $oObject->value = 'Lager 1';
        $aShouldBe[] = $oObject;

        $this->assertTrue($aShouldBe == $aResult);

    }

    public function test_AssignValuesToText()
    {

        $aTestArray = array('one' => 11, 'two' => 22, 'three' => 33, 'fourfour' => 44.44);
        $sResult = assignValuesToText($aTestArray);
        $sShouldBeResult = "one__11@@two__22@@three__33@@fourfour__44.44@@";
        $sShouldNotBeResult = "on__11@@two__22@@three__33@@fourfour__44.44@@";
        $this->assertEquals($sShouldBeResult, $sResult);
        $this->assertNotEquals($sShouldNotBeResult, $sResult);
    }

    public function test_copyDir() {


        $sTargetDir = "targetDir";
        $sSourceDir = "sourceDir";
        $sSourceDeeperDir = "sourceDir/deeper";
        $sTargetDeeperDir = "targetDir/deeper";
        $sSourceFilePathText = "sourceDir/test.txt";
        $sTargetFilePathText = "targetDir/test.txt";
        $sSourceFilePathnopic = "sourceDir/nopic.jpg";
        $sTargetFilePathnopic = "targetDir/nopic.jpg";
        $sSourceFilePathnopic_ico = "sourceDir/nopic_ico.jpg";
        $sTargetFilePathnopic_ico = "targetDir/nopic_ico.jpg";
        $sSourceFilePathCVS = "sourceDir/deeper/CVS";
        $sTargetFilePathCVS = "targetDir/deeper/CVS";


        //test with textfile
        if ( $this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText) ) {

            copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals( is_file($sSourceFilePathText), is_file($sTargetFilePathText));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText, $sTargetFilePathText);
        }

        //test with nopic.jpg
        if ( $this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic) ) {

            copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals( is_file($sSourceFilePathnopic), is_file($sTargetFilePathnopic));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic, $sTargetFilePathnopic);
        }

        //test with nopic_ico.jpg
        if ( $this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic_ico) ) {

            copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals( is_file($sSourceFilePathnopic_ico), is_file($sTargetFilePathnopic_ico));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic_ico, $sTargetFilePathnopic_ico);
        }

        //test with textfile and sub folder with CVS file
        if ( $this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText) ) {
            $this->_prepareCopyDir($sSourceDeeperDir, $sTargetDeeperDir, $sSourceFilePathCVS);

            copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals( is_file($sSourceFilePathCVS), is_file($sTargetFilePathCVS));
            $this->_cleanupCopyDir($sSourceDeeperDir, $sTargetDeeperDir, $sSourceFilePathCVS, $sTargetFilePathCVS);
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText, $sTargetFilePathText);
        }
    }


    public function test_DeleteDir()
    {
        //setup a directory and a subdirectory

        $sDir = 'TestDirectory';
        $sSubDir = 'SubTestDirectory';
        $sFileName = 'testFile.txt';
        if(mkdir($sDir) && mkdir($sDir.DIRECTORY_SEPARATOR.$sSubDir) && is_dir($sDir) && is_dir($sDir."\\".$sSubDir)) {
            $hFileHandle = fopen($sDir.DIRECTORY_SEPARATOR.$sFileName, 'w');
            if(!$hFileHandle) {
                $this->fail('Failed to create file!');
            }
        } else {
            $this->fail('Failed to set up test dirs');
        }
        // 20070720-AS - End setup
        // 20070720-AS - assure generated file exists and it's handle is closed before deleting
        if (($hFileHandle != NULL) && (fclose($hFileHandle))) {
            $blDeleted = deleteDir($sDir); //actual test
            $this->assertNotEquals($blDeleted, is_dir($sDir));
        } else {
            // cleanup the created dirs/subdirs/file
            $blFileDeleted = unlink($sDir.DIRECTORY_SEPARATOR.$sFileName);
            $blTestSubDirDeleted = rmDir($sDir.DIRECTORY_SEPARATOR.$sSubDir);
            $blTestDirDeleted = rmDir($sDir);
            if (!($blFileDeleted && $blTestSubDirDeleted && $blTestDirDeleted)) {
                $this->fail('Failed to delete dirs and/or test file!');
            }
        }

    }

    public function test_ResizeImage()
    {
        $sTestImageFileJPG = "test.jpg";
        $sTestImageFileJPGSmall = "test_smaller.jpg";
        $sTestImageFileResizedJPG = "test_resized.jpg";
        $sTestImageFileSmallResizedJPG = "test_smallresized.jpg";
        // actual test
        $this->assertTrue($this->_resizeImageTest($sTestImageFileJPG, $sTestImageFileResizedJPG));
        // do not resize smaller pics
        $this->assertFalse($this->_resizeImageTest($sTestImageFileJPGSmall, $sTestImageFileSmallResizedJPG));

        $sTestImageFileGIF = "test.gif";
        $sTestImageFileGIFSmall = "test_smaller.gif";
        $sTestImageFileResizedGIF = "test_resized.gif";
        $sTestImageFileSmallResizedGIF = "test_smallresized.gif";
        // actual test
        $this->assertTrue($this->_resizeImageTest($sTestImageFileGIF, $sTestImageFileResizedGIF));
        // do not resize smaller pics
        $this->assertFalse($this->_resizeImageTest($sTestImageFileGIFSmall, $sTestImageFileSmallResizedGIF));

        $sTestImageFilePNG = "test.png";
        $sTestImageFilePNGSmall = "test_smaller.png";
        $sTestImageFileResizedPNG = "test_resized.png";
        $sTestImageFileSmallResizedPNG = "test_smallresized.png";
        // actual test
        $this->assertTrue($this->_resizeImageTest($sTestImageFilePNG, $sTestImageFileResizedPNG));
        // do not resize smaller pics
        $this->assertFalse($this->_resizeImageTest($sTestImageFilePNGSmall, $sTestImageFileSmallResizedPNG));
    }

    public function test_FormatDBDate() {

        $null_var = NULL;

        $this->assertEquals(formatDBDate(is_null($null_var)), is_null($null_var));
        $this->assertNotEquals(formatDBDate(is_null($null_var)), !is_null($null_var));
        $this->assertNotEquals(formatDBDate(!is_null($null_var)), is_null($null_var));

        $this->assertEquals(formatDBDate("2008-11-14"), "2008-11-14");

        $this->assertEquals(formatDBDate("2007-07-20 12:02:07", true), "2007-07-20 12:02:07");
        $this->assertEquals(formatDBDate("2007-07-20", true), "2007-07-20");

        $this->assertEquals(formatDBDate("0000-00-00 00:00:00"), "-");
        $this->assertEquals(formatDBDate("-"), "0000-00-00 00:00:00");
        $this->assertEquals(formatDBDate("19.08.2007"), "2007-08-19");
        $this->assertEquals(formatDBDate("19.08.2007 12:02:07"), "2007-08-19 12:02:07");

        $this->assertEquals(formatDBDate("19.08.2007", true), "2007-08-19");
        $this->assertEquals(formatDBDate("19.08.2007 12:02:07", true), "2007-08-19 12:02:07");
    }

    public function test_FormatDBDateForcedEnglish() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $this->assertEquals(formatDBDate("20070724122112", true), "2007-07-24 12:21:12");
        $this->assertNotEquals(formatDBDate("20070724122112", true), "24.07.2007 12:21:12");
        $this->assertEquals(formatDBDate("20070724122112", true), "2007-07-24 12:21:12");
        $this->assertEquals(formatDBDate("24.07.2007 12:21:12", true), "2007-07-24 12:21:12");
        $this->assertNotEquals(formatDBDate("24.07.2007 12:21:12", true), "24.07.2007 12:21:12");
    }

    public function test_FormatCurreny() {

        $myConfig = oxConfig::getInstance();
        $oActCur = null;
        $sFormatted = formatCurrency(10322.324, $oActCur);
        $this->assertEquals($sFormatted, "10.322,32");
        $oActCur = $myConfig->getActShopCurrencyObject();
        $sFormatted = formatCurrency(10322.324, $oActCur);
        $this->assertEquals($sFormatted, "10.322,32");
        $sFormatted = formatCurrency(10322.325, $oActCur);
        $this->assertEquals($sFormatted, "10.322,33");
        $sFormatted = formatCurrency(10322.326, $oActCur);
        $this->assertEquals($sFormatted, "10.322,33");
    }

    public function test_Currency2Float() {

        $myConfig = oxConfig::getInstance();
        $oActCur = $myConfig->getActShopCurrencyObject();
        $fFloat = currency2Float("10.322,32", $oActCur);
        $this->assertEquals($fFloat, 10322.32);
        $fFloat = currency2Float("10,322.32", $oActCur);
        $this->assertEquals($fFloat, (float)"10.322.32");
        $fFloat = currency2Float("10 322,32", $oActCur);
        $this->assertEquals($fFloat, (float)"10322.32");
        $fFloat = currency2Float("10 322.32", $oActCur);
        $this->assertEquals($fFloat, (float)"10322.32");
    }

    /**
     * @todo add test for rights and roles branch in OXID_VERSION_EE
     */
    public function test_GetActiveSnippet() {

        $myConfig = oxConfig::getInstance();
        $sNow = date("Y-m-d H:i:s", getTime());


                $aTablesWithStock = array(  getViewName("oxcategories") => "( ( oxcategories.oxactive = 1 or ( oxcategories.oxactivefrom < '$sNow' and oxcategories.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxreviews") => "( ( oxreviews.oxactive = 1 or ( oxreviews.oxactivefrom < '$sNow' and oxreviews.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxselectlist") => "( ( oxselectlist.oxactive = 1 or ( oxselectlist.oxactivefrom < '$sNow' and oxselectlist.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxpayments") => "( ( oxpayments.oxactive = 1 or ( oxpayments.oxactivefrom < '$sNow' and oxpayments.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxnews") => "( ( oxnews.oxactive = 1 or ( oxnews.oxactivefrom < '$sNow' and oxnews.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxarticles") => "( ( oxarticles.oxactive = 1 or ( oxarticles.oxactivefrom < '$sNow' and oxarticles.oxactiveto > '$sNow'))  and ( oxarticles.oxstockflag != 2 or (oxarticles.oxstock + oxarticles.oxvarstock) > 0  )  ) "
                                  );
        foreach($aTablesWithStock as $key => $values) {
            //echo "\nActive Snippet->".getActiveSnippet($key)."<-\n";
            $myConfig->oRRoles = null;
            $this->assertEquals(getActiveSnippet($key), $values);
        }
        modConfig::getInstance()->addClassVar('blUseStock', false);

        $aTablesNoStock = array(    getViewName("oxcategories") => "( ( oxcategories.oxactive = 1 or ( oxcategories.oxactivefrom < '$sNow' and oxcategories.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxreviews") => "( ( oxreviews.oxactive = 1 or ( oxreviews.oxactivefrom < '$sNow' and oxreviews.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxselectlist") => "( ( oxselectlist.oxactive = 1 or ( oxselectlist.oxactivefrom < '$sNow' and oxselectlist.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxpayments") => "( ( oxpayments.oxactive = 1 or ( oxpayments.oxactivefrom < '$sNow' and oxpayments.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxnews") => "( ( oxnews.oxactive = 1 or ( oxnews.oxactivefrom < '$sNow' and oxnews.oxactiveto > '$sNow'))  ) ",
                                    getViewName("oxarticles") => "( ( oxarticles.oxactive = 1 or ( oxarticles.oxactivefrom < '$sNow' and oxarticles.oxactiveto > '$sNow'))  ) "
                                  );
        foreach($aTablesNoStock as $key => $values) {
            //echo "\nActive Snippet->".getActiveSnippet($key)."<-\n";
            $this->assertEquals(getActiveSnippet($key), $values);
        }
    }

    public function test_GetTime() {

        $myConfig = oxConfig::getInstance();
        modConfig::getInstance()->addClassVar('iServerTimeShift', null); //explicitly set timezone to null
        $this->assertEquals(getTime() , time());
        for ($iTimeZone = -12; $iTimeZone < 15; $iTimeZone++) {
            modConfig::getInstance()->addClassVar('iServerTimeShift', $iTimeZone);
            $this->assertEquals(getTime() , (time() + (modConfig::getInstance()->iServerTimeShift * 3600)));
        }
    }

    public function test_GetLanguageTag() {

        $myConfig = oxConfig::getInstance();
        modConfig::getInstance()->addClassVar('$iLanguage', null);
        //language is null
        $this->assertEquals(getLanguageTag() , "");

        // language is not null
        modConfig::getInstance()->addClassVar('iLanguage', 1);
        $iLang = modConfig::getInstance()->iLanguage;
        $this->assertEquals(getLanguageTag($iLang) , "_1");
        modConfig::getInstance()->addClassVar('iLanguage', 0);
        $iLang = modConfig::getInstance()->iLanguage;
        $this->assertEquals(getLanguageTag($iLang) , "");
    }

    public function test_AddUserSQL() {

        $sSelect = "select * from oxuser";
        $sSelectNoAdmin = "select * from oxuser left join oxorder on oxorder.oxuserid = oxuser.oxid where ( oxorder.oxshopid = '' or oxuser.oxid = '') ";
        $sSelectRetWithWhere = "select * from oxuser left join oxorder on oxorder.oxuserid = oxuser.oxid  where ( oxorder.oxshopid = '' or oxuser.oxid = '') and  1 = 1";
        // 20070731-AS - case user is not admin
        modSession::getInstance()->addClassFunction( 'getUser', create_function( '', '$oAuthUser = oxNew( "oxuser", "core" ); $oAuthUser->oxuser__oxrights->value = ""; return $oAuthUser;' ) );
        $mySession = oxSession::getInstance();

        // malladmin stuff
        $oAuthUser = $mySession->getUser();
        $this->assertEquals(addUserSQL($sSelect), $sSelectNoAdmin);

        // 20070808-AS - with 'where' clause
        $sSelect = "select * from oxuser where 1 = 1";
        $this->assertEquals(addUserSQL($sSelect), $sSelectRetWithWhere);

        // 20070731-AS - case user is admin
        modSession::getInstance()->addClassFunction( 'getUser', create_function( '', '$oAuthUser = oxNew( "oxuser", "core" ); $oAuthUser->oxuser__oxrights->value = "malladmin"; return $oAuthUser;' ) );
        $this->assertEquals(addUserSQL($sSelect), $sSelect);
    }

    public function test_DeletePicture() {
        //ensures that test runs not as demoshop
        modConfig::getInstance()->addClassFunction( 'hasModule', create_function( '$sModule', 'return false;') );

        $myConfig = oxConfig::getInstance();
        $sZero = "0/";      // required for proper path to pics.
        $blTestPicsAreDeleted = false;
        $sDir = $myConfig->getPictureDir(false)."/";

        // returns false if "nopic.jpg" or "nopic_ico.jpg" is provided
        $sShouldReturnFalse = "nopic.jpg"; // 20070730-AS - files in tests path
        $this->assertFalse(deletePicture($sShouldReturnFalse));
        $this->assertTrue(file_exists($sDir.$sZero.$sShouldReturnFalse));

        // setup-> create a copy of a picture and delete this one for successful test
        $sOrigTestPicFile = "1126_th.jpg";
        $sOrigTestIconFile = "1126_th.jpg"; // we simply fake an icon file by copying the same
        $sCloneTestPicFile = "CC1126_th.jpg";
        $sCloneTestIconFile = "CC1126_th_ico.jpg";

        if (!(copy($sDir.$sZero.$sOrigTestPicFile, $sDir.$sZero.$sCloneTestPicFile) && copy($sDir.$sZero.$sOrigTestIconFile, $sDir.$sZero.$sCloneTestIconFile))) {
            $this->fail("Failed copy testant files");
        }

        // return in case of DemoShop
        if(isset( $myConfig->blIsOXDemoShop) && $myConfig->hasModule("demoshop")) {
            $this->assertFalse(deletePicture($sDir.$sZero.$sCloneTestPicFile));
        }
        //test
        if (file_exists($sDir.$sZero.$sCloneTestPicFile) && file_exists($sDir.$sZero.$sCloneTestIconFile)) {
            deletePicture($sZero.$sCloneTestPicFile); // actual test
            if (file_exists($sDir.$sZero.$sCloneTestPicFile) && file_exists($sDir.$sZero.$sCloneTestPicFile)) {
                $blTestPicsAreDeleted = false;
            } else {
                $blTestPicsAreDeleted = true;
            }
        } else {
            $this->fail("Files for deleting not found");
        }
        if($blTestPicsAreDeleted == false)  { // 20070730-AS - deleting did not work, try clean up
            $this->assertTrue($blTestPicsAreDeleted, "deletePicture() failed");
            if (!(unlink($sDir.$sZero.$sCloneTestPicFile) && unlink($sDir.$sZero.$sCloneTestIconFile))) {
                $this->fail("Cleanup files failed");
            }
        }
    }

    public function test_IsPicDeletable() {
        $myConfig = oxConfig::getInstance();


        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        //actual test
        $blShouldBeTrue =  isPicDeletable("testOK.jpg", "test", "file");
        $this->assertTrue($blShouldBeTrue);

        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 2;'));

        $blShouldBeFalse =  isPicDeletable("testFail.jpg", "test", "file");
        $this->assertFalse($blShouldBeFalse);
    }

    public function test_OverwritePic() {
        //$this->markTestIncomplete("test_OverwritePic()");
        $myConfig = oxConfig::getInstance();

        $mySession = oxSession::getInstance();
        $blSucceeded = false;

        $oArticle = oxNew("oxarticle");
        if(OXID_VERSION_EE) {
            $oArticle->Load("2174");
        } elseif(OXID_VERSION_PE) {
            $oArticle->Load("1849");
        }

        //ensures that test run not as demoshop
        modConfig::getInstance()->addClassFunction( 'hasModule', create_function( '$sModule', 'return false;') );
        // 20070731-AS - this test setup should not succeed
        $sShopID = $mySession->getVar( "actshop");
        $aParams['oxarticles__oxshopid'] = $sShopID;

        $blShouldFail = overwritePic($oArticle, 'oxarticles', 'oxpic0', 'P0', 0, $aParams);
        $this->assertFalse($blShouldFail);

        if (OXID_VERSION_EE) {
            $aParams['oxcategories__oxshopid'] = $sShopID;
            $blShouldSucceed = overwritePic($oArticle, 'oxarticles', 'oxpic1', 'P1', 1, $aParams);
            $this->assertTrue($blShouldSucceed);
        } elseif(OXID_VERSION_PE) {
            $blShouldSucceed = overwritePic( $oArticle, 'oxarticles', 'oxthumb', 'TH', '0', $aParams );
            $this->assertTrue($blShouldSucceed);
        }
    }

    public function test_getTemplateOutput() {
           //$this->markTestIncomplete("test_getTemplateOutput()");

           $myConfig = oxConfig::getInstance();
           modConfig::getInstance()->addClassVar('iDebug', 4);
           require_once('misc\\testTempOut.php');

           $myClassObject = oxNew('testTempOut');
           $oArticle = oxNew("oxarticle");
           $oArticle->Load("2177");
           modConfig::getInstance()->addClassFunction( 'hasModule', create_function( '$sModule', 'return false;') );
           $myClassObject->aViewData['articletitle'] = $oArticle->oxarticles__oxtitle->value;
           $this->assertEquals($oArticle->oxarticles__oxtitle->value, getTemplateOutput( getTestBasePath()."\\misc\\testTempOut.tpl", $myClassObject));
           modConfig::getInstance()->addClassVar('iDebug', 0);

           $this->assertEquals($oArticle->oxarticles__oxtitle->value, getTemplateOutput( getTestBasePath()."\\misc\\testTempOut.tpl", $myClassObject));
    }

    public function test_setPersistentParams() {
           //$this->markTestIncomplete("test_setPersistentParams()");
           $mySession = oxSession::getInstance();


           $aTestParams = array('oxid', 'esales', 'halle');

           //var_dump($aPersParam);
           setPersistentParams('0815', $aTestParams);
           $aPersParam = $mySession->getVar( "persparam");
           $this->assertContains($aTestParams, $aPersParam);


           setPersistentParams('0815', $aUndefined);
           $aPersParam = $mySession->getVar( "persparam");
           $this->assertNotContains($aTestParams, $aPersParam);
    }

    public function test_replaceExtendedChars() {
           //$this->markTestIncomplete("test_replaceExtendedChars()");


           $this->assertEquals('©€"\'&><ä', replaceExtendedChars('©€"\'&><ä'));
           $this->assertEquals('©€"\'&><ä', replaceExtendedChars('©€"\'&><ä', false));
           $this->assertEquals('©€"\'&><ä', replaceExtendedChars('©€"\'&><ä', true));
           $this->assertEquals('©€"\'&><ä', replaceExtendedChars('&copy;&euro;&quot;&#039;&amp;&gt;&lt;&auml;', true));
    }

    public function test_translateString() {
           //$this->markTestIncomplete("test_translateString()");
           //checks only admin languages

           $myConfig = oxConfig::getInstance();
           $myConfig->resetStaticVar();
           modConfig::getInstance()->cleanup();
               modConfig::getInstance()->addClassVar('blAdmin', true);
               if ( $myConfig->getParameter('blAdminTemplateLanguage') == 0) {
                    $this->assertEquals('Falsche e-Mail oder Passwort!', translateString("EXCEPTION_USER_NOVALIDLOGIN"));
               }
               if ( $myConfig->getParameter('blAdminTemplateLanguage') == 1) {
                    $this->assertEquals('Wrong e-Mail or password!', translateString("EXCEPTION_USER_NOVALIDLOGIN"));
               }
           $this->assertEquals('blafoowashere123', translateString("blafoowashere123"));
           $this->assertEquals('', translateString(""));
           $this->assertEquals('\/ß[]~ä#-', translateString("\/ß[]~ä#-"));
    }

    public function test_prepareCSVField() {
          // $this->markTestIncomplete("test_prepareCSVField()");

          $this->assertEquals('"blafoo;wurst;suppe"', prepareCSVField("blafoo;wurst;suppe"));
          $this->assertEquals('"bl""afoo;wurst;suppe"', prepareCSVField("bl\"afoo;wurst;suppe"));
          $this->assertEquals('"blafoo;wu"";rst;suppe"', prepareCSVField("blafoo;wu\";rst;suppe"));
          $this->assertEquals('', prepareCSVField(""));
          $this->assertEquals('""""', prepareCSVField("\""));
          $this->assertEquals('";"', prepareCSVField(";"));
    }

    public function test_checkForSearchEngines() {
           //$this->markTestIncomplete("test_checkForSearchEngines()");


           modConfig::getInstance()->addClassVar('iDebug', 1);
           modConfig::getInstance()->addClassVar('blAdmin', true);

           $this->assertEquals(false, checkForSearchEngines());
           $this->assertEquals(false, checkForSearchEngines(true));
           $this->assertEquals(false, checkForSearchEngines(false));
           $this->assertEquals(false, checkForSearchEngines(-1));
           modConfig::getInstance()->addClassVar('iDebug', 0);
           modConfig::getInstance()->addClassVar('blAdmin', false);

           putenv('HTTP_USER_AGENT=OXID_TEST_BOT');
           $sClient = strtolower( getenv("HTTP_USER_AGENT"));
           $aTestRobots = array(0 => $sClient);
           modConfig::getInstance()->addClassVar('aRobotsExcept', $aTestRobots );

           $this->assertEquals(true, checkForSearchEngines(null));
           $this->assertEquals(true, checkForSearchEngines());
           $this->assertNotEquals(false, checkForSearchEngines(true));
           $this->assertEquals(false, checkForSearchEngines(false));
           $this->assertEquals(false, checkForSearchEngines(-1));


           modConfig::getInstance()->cleanup();
           //modConfig::getInstance()->addClassVar('aRobots', $bla);
           $this->assertEquals(false, checkForSearchEngines(null));
           $this->assertEquals(false, checkForSearchEngines());
           $this->assertNotEquals(false, checkForSearchEngines(true));
           $this->assertEquals(false, checkForSearchEngines(false));
           $this->assertEquals(false, checkForSearchEngines(-1));
   }

    public function test_prepareStrForSearch() {
           //$this->markTestIncomplete("test_prepareStrForSearch()");


           $this->assertEquals(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', prepareStrForSearch('ä ö ü Ä Ö Ü ß &amp;'));
           $this->assertEquals(' h&auml;user', prepareStrForSearch('häuser'));
           $this->assertEquals('', prepareStrForSearch('qwertz'));
           $this->assertEquals('', prepareStrforSearch(''));


    }

    public function test_getWordForSearch() {
           //$this->markTestIncomplete("test_getWordForSearch()");


           $this->assertEquals('&auml;&ouml;&uuml;&Auml;&Ouml;&Uuml;&szlig;&', getWordForSearch('äöüÄÖÜß&amp;'));
           $this->assertEquals('', getWordForSearch(''));
           $this->assertEquals('', getWordForSearch('qwertz'));
           $this->assertEquals('h&auml;user', getWordForSearch('häuser'));
    }

    public function test_validateEmail() {
           //$this->markTestIncomplete("test_validateEmail()");

           $myConfig = oxConfig::getInstance();



           modConfig::getInstance()->addClassVar('iValidateEMail', 1);
           $this->assertEquals(true, validateEmail('mathias.krieck@oxid-esales.com'));
           $this->assertEquals(false, validateEmail("ßmathias.krieck@oxid-esales.com"));

           //deactivated till decision is made F
           //modConfig::getInstance()->addClassVar('iValidateEMail', 2);
           //$this->assertEquals(true, validateEmail('mathias.fiedler@oxid-esales.com'));
           //$this->assertEquals(false, validateEmail("?m.krieck@kort-systems.com"));
    }

    public function test_IsPathSecure() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        modConfig::getInstance()->addClassVar('iSecurityLevel', 1);
        $this->assertEquals(true, isPathSecure('misc\\secureDir', $aError));
        $this->assertEquals(false, isPathSecure('blafoo\\not_exists', $aError));
        $this->assertEquals(true, isPathSecure('misc\\secureDir', $aError));
        $this->assertEquals(null, isPathSecure('misc', $aError));
        modConfig::getInstance()->addClassVar('iSecurityLevel', 0);
        $this->assertEquals(true, isPathSecure('misc\\secureDir', $aError));
    }


    public function test_RebuildCache() {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $this->assertNull(RebuildCache());
    }

    public function test_LoadAdminProfile() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $mySession = oxSession::getInstance();
        $myConfig = oxConfig::getInstance();


        modConfig::getInstance()->addClassVar('aInterfaceProfiles', array('640x480', '14'));
        $aProfiles = loadAdminProfile();
        $this->assertContains('640x480', $aProfiles[0]);

        modConfig::getInstance()->addClassVar('aInterfaceProfiles', array());
        $aProfiles = loadAdminProfile();
        $this->assertNull($aProfiles);

        modConfig::getInstance()->addClassVar('aInterfaceProfiles', "teststring");
        $aProfiles = loadAdminProfile();
        $this->assertNull($aProfiles);
    }

    public function test_FRound() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();

        $this->assertEquals('9.84', fRound('9.844'));
        $this->assertEquals('9.85', fRound('9.845'));
        $this->assertEquals('9.85', fRound('9.849'));
        $this->assertEquals('0', fRound('blafoo'));
        $this->assertEquals('9', fRound('9,849'));

        $aCur = $myConfig->getCurrencyArray();
        $oCur = $aCur[1];
        $this->assertEquals('9.84', fRound('9.844', $oCur));
        $this->assertEquals('9.85', fRound('9.845', $oCur));
        $this->assertEquals('9.85', fRound('9.849', $oCur));
        $this->assertEquals('0', fRound('blafoo', $oCur));
        $this->assertEquals('9', fRound('9,849', $oCur));

    }

    public function test_GetWeekNumber() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();
        $sTimeStamp = '1186052540'; // from 2007-08-02 -> week nr = 31;


        $this->assertEquals(31, getWeekNumber($sTimeStamp));
        $this->assertEquals(30, getWeekNumber($sTimeStamp, '%U'));
        $this->assertEquals(31, getWeekNumber($sTimeStamp, '%W'));

        modConfig::getInstance()->addClassVar('iFirstWeekDay', 1);
        $this->assertEquals(30, getWeekNumber($sTimeStamp));

        $sCurTimeStamp = time();
        $iCurWeekNr = (int) strftime( '%U', $sCurTimeStamp);
        $this->assertEquals($iCurWeekNr, getWeekNumber());
    }

    public function test_GetObjectFields() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $oObject = oxNew( "oxarticle", "core");
        $this->assertContains($oObject->aIdx2FldName[0], getObjectFields());
        $this->assertNotContains('oxblfixedprice', getObjectFields()); // 'oxblfixedprice 'skipped in getObjectsFields()

        $this->assertEquals(array(), getObjectFields('oxuser'));
    }

    public function test_IconName() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $this->assertEquals('TEST_ico.jpg', IconName('TEST.jpg'));
        $this->assertEquals('TEST_ico.gif', IconName('TEST.gif'));
        $this->assertEquals('TEST_ico.png', IconName('TEST.png'));

        $this->assertNotEquals('TEST_ico.bmp', IconName('TEST.bmp'));
        $this->assertEquals('', IconName('') );
    }
    // 20070801-AS - START
    public function test_ConvertDBDateTime() {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $sZeroTimeStandard = '0000-00-00 00:00:00';
        $sZeroTimeMySQL = '0000-00-00 00:00:00';
        $sZeroFormattedDate = '0000-00-00';

        $sDateTime = '2007-08-01 11:56:25';
        $sDateTimeStandard = '2007-08-01 11:56:25';
        $sDateTimeMySQL = '2007-08-01 11:56:25';
        $sDateFormattedDate = '2007-08-01';

        $sEURDateTime = '01.08.2007 11.56.25';

        $sUSADateTimeAM = '08/01/2007 11:56:25 AM';
        $sUSADateTimeAMExpected = '2007-08-01 11:56:25';

        $sUSADateTimePM = '08/01/2007 11:56:25 PM';
        $sUSADateTimePMStandard = '2007-08-01 23:56:25';
        $sUSADateTimePMMySQL = '2007-08-01 23:56:25';

        // standard
        $this->assertTrue($this->_ConvertDBDateTimeTest("", $sZeroTimeStandard));
        // mySQL compatible
        $this->assertTrue($this->_ConvertDBDateTimeTest("", $sZeroTimeMySQL, true));
        // format date
        $this->assertTrue($this->_ConvertDBDateTimeTest("",$sZeroFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest("",$sZeroFormattedDate, false, true));
        // ISO
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateFormattedDate, false, true));
        // EUR
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateTimeStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateTimeMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateFormattedDate, false, true));
        // USA pattern AM
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateTimeStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateTimeMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateFormattedDate, false, true));
        // USA pattern PM
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sUSADateTimePMStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sUSADateTimePMMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sDateFormattedDate, false, true));

        // skip is set as exports may need to skip format conversion
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeStandard, false, false, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeStandard, true, false, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeStandard, true, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeStandard, false, true, true));
    }

    /**
     * Note:    ConvertDBTimestamp() uses mktime() which is known to have issues with dates
     *          before 1970-01-01 00:00:00
     *          Before this date, all timestamps are computed in a cyclic interval of (2038-1970) in seconds
     *          and stored in a big int.
     *          so use caution with dates before the magic unix date!!
     */
    public function test_ConvertDBTimestamp() {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $DateTimeStamp = '20070801115625';
        $DateTime = '2007-08-01 11:56:25';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($DateTime, $DateTimeStamp, true));
        // input timestamp expect datetime
        $this->assertTrue($this->_ConvertDBTimestampTest($DateTimeStamp, $DateTime));

        $DateTimeStamp = '20070801115625';
        $EURDateTime = '01.08.2007 11.56.25';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($EURDateTime, $DateTimeStamp, true));
        // input timestamp expect datetime
        $this->assertTrue($this->_ConvertDBTimestampTest($DateTimeStamp, $DateTime));

        $DateTimeStamp = '20070801115625';
        $USADateTime = '08/01/2007 11:56:25 AM';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($USADateTime, $DateTimeStamp, true));
        // input timestamp expect datetime
        $this->assertTrue($this->_ConvertDBTimestampTest($DateTimeStamp, $DateTime));

        $DateTimeStamp = '20070801235625';
        $USADateTime = '08/01/2007 11:56:25 PM';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($USADateTime, $DateTimeStamp, true));
        // input timestamp expect datetime
        $DateTime = '2007-08-01 23:56:25';
        $this->assertTrue($this->_ConvertDBTimestampTest($DateTimeStamp, $DateTime));

        // skip is set as exports may need to skip format conversion
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTime, $sDateTimeStandard, false, true));
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTime, $sDateTimeStandard, true, true));

        $sZeroTimeStamp = '00000000000000';
        $sZeroDateTime = '0000-00-00 00:00:00';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroDateTime, $sZeroTimeStamp, true));
        // input timestamp expect datetime
        $sZeroTimeStamp = '19700101000000';
        $sZeroDateTime = '1970-01-01 00:00:00';
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));
        // 20070801-AS - timestamps works only for dates including 19011213205513
        $sZeroTimeStamp = '19111213205513';
        $sZeroDateTime = '1911-12-13 20:55:13';
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));

        // 20070801-AS - timestamps earlier than 19011213205513 return 1970-01-01 01:00:00
        $sZeroTimeStamp = '19711213205512';
        $sZeroDateTime = '1901-12-13 20:55:12';
        $this->assertFalse($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));
        // 20070801-AS - timestamps earlier than 19011213205513 return 1970-01-01 01:00:00 or differnt (depends on GMT + and - )
        $sZeroTimeStamp = '18710130105512';
       // $sZeroDateTime = '1970-01-01 01:00:00';
        $sZeroDateTime = date("Y-m-d H:i:s",0);
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));
    }

    public function test_ConvertDBDate() {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $sDateStamp = '20070801';
        $sDateStandard = '2007-08-01 11:56:25';
        // skip is set as exports may need to skip format conversion
        $this->assertTrue($this->_ConvertDBDateTest($sDateStandard, $sDateStandard, true, true));
        $this->assertTrue($this->_ConvertDBDateTest($sDateStandard, $sDateStandard, false, true));
        $sDateTime = '2007-08-01 11:56:25';
        $sDate = '2007-08-01';
        $this->assertTrue($this->_ConvertDBDateTest($sDateTime, $sDate, false, false));
    }

    public function test_ResizeGif() {
        $sTestImageFileGIF = "test.gif";
        $sTestImageFileResizedGIF = "test_resized_ResizeGIF.gif";
        // actual test
        $this->assertTrue($this->_resizeGIFTest($sTestImageFileGIF, $sTestImageFileResizedGIF));
    }

    public function test_ToStaticCache() {

        $sName = "SomeName";
        $mContent = "SomeContent";
        $sKey = "SomeKey";

        modUtils_toStaticCache($sName, $mContent);
        $aCache = modUtils__getStaticCache();
        $this->assertEquals($aCache[$sName], $mContent);

        $sName = "SomeOtherName";
        $mContent = "SomeOtherContent";
        $sKey = "SomeOtherKey";
        modUtils_toStaticCache($sName, $mContent, $sKey);
        $aCache = modUtils__getStaticCache();
        //echo "\nReturned: ->".$aCache[$sName][$sKey]."<-\nExpected: ->".$mContent."<-";
        $this->assertEquals($aCache[$sName][$sKey], $mContent);
     }

    public function test_FromStaticCache() {

        $sName = "SomeName";
        $mContent = "SomeContent";
        $sKey = "SomeKey";
        modUtils__setStaticCache($sName, $mContent);
        $this->assertEquals(modUtils_fromStaticCache($sName), $mContent);
     }

    public function test_OxFileCache() {

        $blMode = true;            // write
        $sName = "testFileCache";
        $sInput = "test_test_test";
        $this->assertNull(oxFileCache($blMode, $sName, $sInput));//actual test
        $blMode = false;           // read
        $this->assertEquals(oxFileCache($blMode, $sName, $sInput), $sInput);
    }

    public function test_OxResetFileCache() {


        $this->_fillFileCache();
        $this->assertNull(oxResetFileCache());//actual test

        $myConfig = oxConfig::getInstance();

        $sFilePath = $myConfig->sCompileDir . "/oxc_*.txt";
        $aPathes   = glob( $sFilePath);
        $this->assertTrue($aPathes == null);
    }

    public function test_GetCatCache() {

        $myUtilsTest = new oxUtilsTestTest();
        //after last test, cache should be empty, we clear it just in case
        oxResetFileCache();
        //it is neccessary also to reset global params!
        $myConfig = oxConfig::getInstance();
        $aLocalCatCache = $myConfig->setGlobalParameter('aLocalCatCache',null);

        $this->assertNull($myUtilsTest->getCatCacheTest());//actual test
        // previous test (oxResetFileCache)erases all data, so we provide some data
        $sName = "aLocalCatCache";
        $sInput = "a:1:{s:26:\"30e44ab83159266c7.83602558\";a:1:{s:32:\"2fb5911b89dddda329c256f56d1f60c5\";s:1:\"5\";}}";
        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "5");
        $aRetCache = array("30e44ab83159266c7.83602558" => $aArray);
        oxFileCache(true, $sName, $sInput);

        $aLocalCache = $myUtilsTest->getCatCacheTest(); // actual test
        $this->assertTrue($aRetCache === $aLocalCache);

        //cleanup
        oxResetFileCache();
    }

    public function test_SetCatCache() {

        $myConfig = oxConfig::getInstance();
        $myUtilsTest = new oxUtilsTestTest();
        //after last test, cache should be empty, we clear it just in case
        oxResetFileCache();

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "5");
        $aCache = array("30e44ab83159266c7.83602558" => $aArray);

        $this->assertNull($myUtilsTest->setCatCacheTest($aCache));//actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalCatCache'), $aCache);
        $sName = "aLocalCatCache";
        $sInput = "a:1:{s:26:\"30e44ab83159266c7.83602558\";a:1:{s:32:\"2fb5911b89dddda329c256f56d1f60c5\";s:1:\"5\";}}";

        $this->assertEquals(oxFileCache(false, $sName, $sInput), $sInput);
        //cleanup
        oxResetFileCache();
    }

    public function test_SetVendorCache() {

        $myConfig = oxConfig::getInstance();
        $myUtilsTest = new oxUtilsTestTest();
        //after last test, cache should be empty, we clear it just in case
        oxResetFileCache();
        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "14");
        $aCache = array("d2e44d9b31fcce448.08890330" => $aArray);

        $this->assertNull($myUtilsTest->setVendorCacheTest($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalVendorCache'), $aCache);
        $sName = "aLocalVendorCache";
        $sInput = "a:1:{s:26:\"d2e44d9b31fcce448.08890330\";a:1:{s:32:\"2fb5911b89dddda329c256f56d1f60c5\";s:2:\"14\";}}";
        //echo "\n->".oxFileCache(false, $sName, $sInput)."<-";
        //echo "\n->".$sInput."<-";
        $this->assertEquals(oxFileCache(false, $sName, $sInput), $sInput);
        //cleanup
        $this->assertNull($myUtilsTest->setVendorCacheTest(null));
        oxResetFileCache();
    }
    // 20070801-AS - END

    public function test_GetVendorCache() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();
        $myUtilsTest = new oxUtilsTestTest();

        $aArray = array("2fb5911b89dddda329c256f5614111978" => "14");
        $aCache = array("m4e44d9b31fcce448.08890815" => $aArray);

        oxResetFileCache();
        $myUtilsTest->setVendorCacheTest($aCache);

        $this->assertEquals($aCache, $myUtilsTest->GetVendorCacheTest());
        //clean up
        $myUtilsTest->setVendorCacheTest(null);
        oxResetFileCache();
    }

    public function test_GetRemoteCachePath() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        touch('misc/actions_main.inc.php', time(), time()) ;
        $this->assertEquals('misc/actions_main.inc.php', GetRemoteCachePath('http://www.blafoo.null', 'misc/actions_main.inc.php'));
        //ensure that file is older than 24h
        touch('misc/actions_main.inc.php', time() - 90000, time() - 90000) ;
        $this->assertEquals('misc/actions_main.inc.php', GetRemoteCachePath(oxConfig::getInstance()->getShopURL(), 'misc/actions_main.inc.php'));
        touch('misc/actions_main.inc.php', time() - 90000, time() - 90000) ;
        $this->assertEquals('misc/actions_main.inc.php', GetRemoteCachePath('http://www.blafoo.null', 'misc/actions_main.inc.php'));
        $this->assertEquals(false, GetRemoteCachePath('http://www.blafoo.null', 'misc/blafoo.test'));
    }

    public function test_CheckAccessRights() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $mySession = oxSession::getInstance();
        $backUpAuth = $mySession->getVar( "auth");
        //echo "\nAuth->".$mySession->getVar( "auth")."<-\n";

        $mySession->setVar( "auth", "oxdefaultadmin");
        $this->assertEquals(true, checkAccessRights());

        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        $mySession->setVar( "auth", "oxdefaultadmin");
        $this->assertEquals(true, checkAccessRights());
        $mySession->setVar( "auth", "blafooUser");


        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 0;'));

        $this->assertEquals(false, checkAccessRights());

        $mySession->setVar( "auth", $backUpAuth);
    }

    public function test_CreateSQLList() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $aAllArticels = array(array('Bla'),array('Foo'),array('Bar'));

        $this->assertEquals("", CreateSQLList(array()));
        $this->assertEquals("", CreateSQLList(array(array(''))));
        $this->assertEquals("'Bla','Foo','Bar'", CreateSQLList($aAllArticels));

    }

    public function test_seoIsActive() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        modConfig::getInstance()->addClassVar('blAdmin', true);
        $this->assertEquals(false, seoIsActive());

        modConfig::getInstance()->addClassVar('blAdmin', false);
        modUtils_setSEOActive(true);
        $this->assertEquals(true, seoIsActive());

        modUtils_setSEOActive(false);
        $this->assertEquals(false, seoIsActive());

        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modConfig::getInstance()->setParameter('fnc', 1);
        $this->assertEquals(false, seoIsActive());
    }

    public function test_seoIsActiveBasket() {


        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modSession::getInstance()->addClassFunction('getBasket', create_function('', '$oBasket = oxNew("oxbasket"); $oBasket->iCntProducts = 1; return $oBasket;'));
        $this->assertEquals(false, seoIsActive());
    }

    public function test_seoIsActiveSID() {


        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modConfig::getInstance()->setParameter('sid', 1);
        $this->assertEquals(false, seoIsActive());
    }

    public function test_seoIsActiveFilterComp() {


        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modConfig::getInstance()->setParameter('aFiltcompproducts', 1);
        $this->assertEquals(false, seoIsActive());
    }

    public function test_seoIsActiveUser() {


        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modSession::getInstance()->addClassFunction('getUser', create_function('', 'return true;'));
        $this->assertEquals(false, seoIsActive());
    }

    public function test_seoIsActiveExclSE() {


        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modSession::getInstance()->addClassFunction('getVar', create_function('$sStr', 'return true;'));
        $this->assertEquals(true, seoIsActive());
    }

    public function test_seoIsActiveSEO() {


        modUtils_setSEOActive(null);
        modConfig::getInstance()->addClassvar('lang', 1);
        self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', __CLASS__.'::$test_sql_used=$sql;return 1;'));

        modConfig::getInstance()->setParameter('cl', "search");
        $this->assertEquals(false, seoIsActive());


    }


    public function test_GetCatArticleCount() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $this->assertEquals('0', GetCatArticleCount(''));
            $sCatID = '8a142c3e60a535f16.78077188';
            $sResult = oxDb::getDb()->getOne("SELECT count(*) FROM `oxobject2category` WHERE OXCATNID = '$sCatID'");
            $this->assertEquals($sResult, GetCatArticleCount($sCatID));
        oxResetFileCache();
        $this->assertEquals('0', GetCatArticleCount(''));
            $sCatID = '8a142c3e60a535f16.78077188';
            $sResult = oxDb::getDb()->getOne("SELECT count(*) FROM `oxobject2category` WHERE OXCATNID = '$sCatID'");
            $this->assertEquals($sResult, GetCatArticleCount($sCatID));
    }

    public function test_GetPriceCatArticleCount() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myUtilsTest = new oxUtilsTestTest();
        oxResetFileCache();

        $aCache = $myUtilsTest->getCatCacheTest();
        $sRet = setPriceCatArticleCount($aCache, '30e44ab8338d7bf06.79655612', $myUtilsTest->getUserViewIdTest(), 1, 100);
        $sCount = getPriceCatArticleCount('30e44ab8338d7bf06.79655612', 1, 100);
        $this->assertEquals($sRet, $sCount);
        oxResetFileCache();
    }

    public function test_GetVendorArticleCount() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myUtilsTest = new oxUtilsTestTest();
        oxResetFileCache();

        $aCache = $myUtilsTest->getVendorCacheTest();
        $sRet = setVendorArticleCount($aCache, 'd2e44d9b32fd2c224.65443178', $myUtilsTest->getUserViewIdTest());
        $sCount = getVendorArticleCount('d2e44d9b32fd2c224.65443178');
        $this->assertEquals($sRet, $sCount);
        oxResetFileCache();
    }

    public function test_SetCatArticleCount() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myUtilsTest = new oxUtilsTestTest();

        $sRetSet = setCatArticleCount(array(), '30e44ab8338d7bf06.79655612', $myUtilsTest->getUserViewIdTest() );
        $sRetSet = getCatArticleCount('30e44ab8338d7bf06.79655612');
        $this->assertEquals($sRetSet, $sRetSet);

        oxResetFileCache();
    }

    public function test_SetPriceCatArticleCount() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myUtilsTest = new oxUtilsTestTest();

        $sRetSet = setPriceCatArticleCount(array(), '30e44ab8338d7bf06.79655612', $myUtilsTest->getUserViewIdTest(), 10, 100 );
        $sRetSet = getPriceCatArticleCount('30e44ab8338d7bf06.79655612', 10, 100);
        $this->assertEquals($sRetSet, $sRetSet);

        oxResetFileCache();
    }

    // 20070802-AS - Start
    public function test_SetVendorArticleCount() {

        oxResetFileCache();
        $myUtilsTest = new oxUtilsTestTest();
        $aCache = null;
        $sCatId = 'root';
        $sActIdent = null;

        // always return 0 if $sCatId ='root'
        $this->assertEquals(setVendorArticleCount($aCache, $sCatId, $sActIdent), 0);
        oxResetFileCache();

        if(OXID_VERSION_EE) {
            $aCache = $myUtilsTest->getVendorCacheTest();
            $sVendorID = 'd2e44d9b31fcce448.08890330';  //Hersteller 1 from Demodata
            $sCatId = $sVendorID;
            $sActIdent = $myUtilsTest->getUserViewIdTest();
            //echo "\n->".setVendorArticleCount($aCache, $sCatId, $sActIdent)."<-";
            $this->assertEquals(setVendorArticleCount($aCache, $sCatId, $sActIdent), 14);
        } elseif(OXID_VERSION_PE) {
            $aCache = $myUtilsTest->getVendorCacheTest();
            $sVendorID = '77442e37fdf34ccd3.94620745';  //Hersteller 2 from Demodata
            $sCatId = $sVendorID;
            $sActIdent = $myUtilsTest->getUserViewIdTest();
            //echo "\n->".setVendorArticleCount($aCache, $sCatId, $sActIdent)."<-";
            $this->assertEquals(setVendorArticleCount($aCache, $sCatId, $sActIdent), 1);
        }
    }

    public function test_ResetCatArticleCount() {

        $myUtilsTest = new oxUtilsTestTest();
        //echo "\n->".resetCatArticleCount()."<-";
        $this->assertEquals(resetCatArticleCount(), '');

        $sVendorID = 'd2e44d9b31fcce448.08890330';  //Hersteller 1 from Demodata
        $sCatId = $sVendorID;
        $aCatData = $myUtilsTest->getCatCacheTest();
        $this->assertEquals(resetCatArticleCount($sCatId), '');
        $newCache = $myUtilsTest->getCatCacheTest();
        $this->assertFalse(isset($newCache[$sCatId]));
    }

    public function test_ResetPriceCatArticleCount() {

        $myConfig = oxConfig::getInstance();
        $myUtilsTest = new oxUtilsTestTest();
        oxResetFileCache();
        if(OXID_VERSION_EE) {
            $iPrice = 0;
            if (!$myUtilsTest->getCatCacheTest()) {
               $this->assertNull(resetPriceCatArticleCount($iPrice));   //actual test
            }
            $sInput = 'a:1:{s:26:"30e44ab82c03c3848.49471214";a:1:{s:32:"2fb5911b89dddda329c256f56d1f60c5";s:1:"8";}}';
            $sName = "aLocalCatCache";
            oxFileCache(true, $sName, $sInput);
            $aCache = $myUtilsTest->getCatCacheTest();
            $this->assertNotNull($aCache);
            $this->assertNull(resetPriceCatArticleCount($iPrice)); //actual test
            $aCache = $myUtilsTest->getCatCacheTest();
            $this->assertFalse(isset($aCache['30e44ab82c03c3848.49471214']));
            oxResetFileCache();
        } elseif (OXID_VERSION_PE) {
            $iPrice = 0;
            $this->assertNull(resetPriceCatArticleCount($iPrice));
            $aCache = $myUtilsTest->getCatCacheTest();
            $this->assertNull($aCache);
        }
    }

    public function test_ResetVendorArticleCount() {

        $myConfig = oxConfig::getInstance();
        $myUtilsTest = new oxUtilsTestTest();
        $sVendorID = null;
        oxResetFileCache();
        //case $sVendorID = null;
        $this->assertNull(resetVendorArticleCount($sVendorID)); //actual test
        $this->assertNull($myConfig->getGlobalParameter('aLocalVendorCache'));
        $this->assertEquals(oxFileCache(false, 'aLocalVendorCache', ''), '');

        // case loading from cache
        $sVendorID = 'd2e44d9b31fcce448.08890330';
        $sInput = 'a:2:{s:26:"d2e44d9b31fcce448.08890330";a:1:{s:32:"2fb5911b89dddda329c256f56d1f60c5";s:2:"14";}s:26:"d2e44d9b32fd2c224.65443178";a:1:{s:32:"2fb5911b89dddda329c256f56d1f60c5";s:2:"14";}}';
        $sName = 'aLocalVendorCache';
        oxFileCache(true, $sName, $sInput);
        $aCache = $myUtilsTest->getVendorCacheTest();
        $this->assertNotNull($aCache);
        $this->assertNull(resetVendorArticleCount($sVendorID)); //actual test
        $aCache = $myUtilsTest->getCatCacheTest();
        $this->assertFalse(isset($aCache[$sVendorID]));
        oxResetFileCache();
      }

    public function test_IsDerived() {

        if (OXID_VERSION_EE) {
            //$this->markTestIncomplete('This test has not been implemented yet.');
            $this->assertFalse(isDerived('a7c44be4a5ddee114.67356237','oxarticles'));
            $this->assertFalse(isDerived('2177','oxarticles'));

            //fake a ShopID different from one
            modConfig::getInstance()->addClassFunction( 'getShopID', create_function( '', 'return 2;' ) );
            $this->assertTrue(isDerived('a7c44be4a5ddee114.67356237','oxarticles'));
            $this->assertTrue(isDerived('2177','oxarticles'));
        } elseif (OXID_VERSION_PE) {
            // 20070817-AS - in PE there are no Articles, that can be derived from other shops, since there's only one shop
            $this->assertFalse(isDerived('d8842e3cbb8ac9238.37666205','oxarticles'));
        }
    }

    /**
     * @todo tests for shopID values up to 100 (usually <= 64)
     */
    public function test_GetShopBit() {

        // create an array with corresponding test data (not all just a random mix)
        $aArray = array(   0 => '0',
                           1 => '1',
                           2 => '2',
                           3 => '4',
                           4 => '8',
                           5=> '16',
                           6 => '32',
                           7 => '64',
                           39 => '274877906944',
                           52 => '2251799813685248',
                           53 => '4503599627370496',
                           62 => '2305843009213693952',
                           63 => '4611686018427387904',
                           64 => '9223372036854775808',
                           65 => '0',
                           66 => '0',
                           100 => '0');

        foreach ($aArray as $key => $value) {
            //echo "\n".$key." => '".$value."', ";
            $this->assertEquals(getShopBit($key), $value);
        }
    }

    /**
     * @todo runs with 32bit values only ( < 2147483647)
     */
    public function test_BitwiseAnd() {

        for ($iCountA = 2147483645; $iCountA <= 2147483647; $iCountA++) {
            for ($iCountB = 2147483645; $iCountB <= 2147483647; $iCountB++) {
                $this->assertEquals(bitwiseAnd($iCountA, $iCountB),($iCountA & $iCountB));
            }
        }
    }

    /**
     * @todo runs with 32bit values only ( < 2147483647)
     */
    public function test_BitwiseOr() {

        for ($iCountA = 2147483645; $iCountA <= 2147483647; $iCountA++) {
            for ($iCountB = 2147483645; $iCountB <= 2147483647; $iCountB++) {
                $this->assertEquals(bitwiseOr($iCountA, $iCountB),($iCountA | $iCountB));
            }
        }
    }
    // 20070802-AS - End
    public function test_BigintSum() {
        //$this->markTestIncomplete('This test has not been implemented yet.');


        $iVal1 = "92233720368540";
        $iVal2 = "-2233720368540";
        $sSum = '90000000000000';
        $this->assertEquals($sSum, bigintSum($iVal1, $iVal2));
        $iVal1 = "90000000000000";
        $iVal2 =  "2233720368540";
        $sSum = '92233720368540';
        $this->assertEquals($sSum, bigintSum($iVal1, $iVal2));
        $iVal1 = "-999999999999999";
        $iVal2 =  "999999999999999";
        $sSum = '0';
        $this->assertEquals($sSum, bigintSum($iVal1, $iVal2));


    }

    public function test_CleanMultishopFields() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myDB = oxDb::getDb();

        $myDB->Execute("Insert into oxfield2shop values ('testcase','blafooartid', 1, 22, 23, 24, 25)");
        cleanMultishopFields('blafooartid');
        $sResult = $myDB->GetOne("Select * from oxfield2shop where OXARTID = 'blafooartid'");
        $this->assertEquals('', $sResult);
        $myDB->Execute("Insert into oxfield2shop values ('testcase1','blafooartid1', 1, 22, 23, 24, 25)");
        $myDB->Execute("Insert into oxfield2shop values ('testcase2','blafooartid2', 1, 22, 23, 24, 25)");
        cleanMultishopFields();
        $sResult = $myDB->GetOne("Select * from oxfield2shop");
        $this->assertEquals('', $sResult);
    }

    public function test_IsValidFieldName() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $this->assertEquals(true, isValidFieldName('oxid'));
        $this->assertEquals(true, isValidFieldName('oxid_1'));
        $this->assertEquals(true, isValidFieldName('oxid.1'));
        $this->assertEquals(false, isValidFieldName('oxid{1'));
    }

    public function test_IsValidAlpha() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $this->assertEquals(true, isValidAlpha('oxid'));
        $this->assertEquals(true, isValidAlpha('oxid1'));
        $this->assertEquals(false, isValidAlpha('oxid.'));
        $this->assertEquals(false, isValidAlpha('oxid{'));
    }

    public function test_GetUserViewId() {
        //$this->markTestIncomplete('This test has not been implemented yet.');

        $myConfig = oxConfig::getInstance();
        $myUtilsTest = new oxUtilsTestTest();

        modconfig::getInstance()->addclassvar('blAdmin', false);
        $sExpected = md5($myConfig->GetShopID().getLanguageTag().serialize(NULL).(int)$myConfig->blAdmin);
        $this->assertEquals($sExpected, $myUtilsTest->getUserViewIdTest());
    }

    public function test_Redirect() {
        $this->markTestSkipped('Not testable, kills test as well.');

        //redirect('http://www.oxid-esales.com');
    }

    public function test_ShowMessageAndExit() {
        $this->markTestSkipped('Not testable, kills test as well.');
    }

    /**
     * test is actually nonsense under unit testing
     * Reason: The testant immediately and explicitly returns on defined('OXID_PHP_UNIT')
     */
    public function test_SetCookie() {
        $myUtils =  getInstance();

        $sName = "someName";
        $sValue = "someValue";
        $this->assertNull(setCookie($sName, $sValue));
    }

    public function test_GetCookie() {

        // $sName = null
        $aCookie = getCookie();
        $this->assertTrue((isset($aCookie) && ($aCookie[0] == null)));
    }

    public function test_ResetStaticVar() {


        modUtils_setSEOActive(true);
        $this->assertNull(resetStaticVar("_blSEOIsActive"));
        $this->assertNull(modUtils_getSEOActive());
    }

    public function test_GetRemoteAddress() {

        $sIP = '127.0.0.1';
        // in test mode, there are no remote adresses, thus null
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
        unset($_SERVER["HTTP_CLIENT_IP"]);
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $this->assertNull(getRemoteAddress());
        } else {
            $_SERVER["REMOTE_ADDR"] = $sIP;
            $this->assertEquals(getRemoteAddress(), $sIP);
        }

        $_SERVER["HTTP_X_FORWARDED_FOR"] = $sIP;
        $this->assertEquals(getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
        $_SERVER["HTTP_CLIENT_IP"] = $sIP;
        $this->assertEquals(getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_CLIENT_IP"]);
    }

    public function test_AddUrlParameters() {

        $myUtilsTest = new oxUtilsTestTest();

        $sURL = 'http://www.url.com';
        $aParams = array('string' => 'someString', 'bool1' => false, 'bool2' => true, 'int' => 1234, 'float' => 123.45, 'negfloat' => -123.45);

        $sReturnURL = "http://www.url.com?string=someString&bool1=&bool2=1&int=1234&float=123.45&negfloat=-123.45";
        $this->assertEquals($myUtilsTest->addUrlParametersTest($sURL, $aParams), $sReturnURL);
    }

    public function test_GetServerVar() {

        $sServVar = 'TEST';
        $_SERVER[$sServVar] = "myTest";
        $this->assertEquals(getServerVar($sServVar), 'myTest');
        $this->assertEquals(getServerVar(), $_SERVER);
        unset($sServVar);
        if(!isset($_SERVER)) {
            $this->assertNull(getServerVar($sServVar));
        }
    }

    /**
     * Testing Vendors articles count
     */
    public function test_VendorArticleCountIsSetAndCached() {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $myConfig = oxConfig::getInstance();

        $myUtilsTest = new oxUtilsTestTest();
        $rs = oxDb::getDb()->getArray( "select oxid from oxvendor");
        //get vendors list
        foreach ($rs AS $aValue) {
            $aVendors[] = $aValue[0];
        }

        //reset Vendors cache and check, if vendors cache is empty
        $myConfig->setGlobalParameter('aLocalVendorCache', null);
        oxFileCache( true, 'aLocalVendorCache', '');

        $this->assertNull($myUtilsTest->getVendorCacheTest(), "Vendors cache not reseted");

        $sTable = getViewName("oxarticles");

        // get each vendor articles count
        $aVendorsArticlesCount = oxDb::getDb()->getAssoc("select $sTable.oxvendorid AS vendorId, count(*) from $sTable where $sTable.oxvendorid <> '' and ".getActiveSnippet( $sTable)." group by $sTable.oxvendorid ");

        // get first vendor articles count
        // calling first time must load all vendors articles count into cache
        $sFirstVendorArticlesCount = getVendorArticleCount($aVendors[0]);

        $this->assertEquals($aVendorsArticlesCount[$aVendors[0]], $sFirstVendorArticlesCount, "Failed counting first vendors articles" );

        // get vendors data from cache
        $aVendorCacheData = $myUtilsTest->getVendorCacheTest();

        $this->assertEquals(count($aVendorsArticlesCount), count($aVendorCacheData), "Not all vendors articles count was cached");

        // current category unique ident
        $sActIdent = $myUtilsTest->getUserViewIdTest();

        //check, if getVendorArticleCount() returns value from cache
        $aTempCache[$aVendors[0]][$sActIdent] = '999999';
        $myUtilsTest->setVendorCacheTest($aTempCache);

        // set Global Parameter aLocalVendorCache as null to force getting data from file cache
        $myConfig->setGlobalParameter('aLocalVendorCache', null);
        $sFirstVendorArticlesCount = getVendorArticleCount($aVendors[0]);

        $this->assertEquals('999999', $sFirstVendorArticlesCount, "Vendor article count was returned not from cache" );

        //restore orginal count data
        $myConfig->setGlobalParameter('aLocalVendorCache', null);
        oxFileCache( true, 'aLocalVendorCache', '');
        getVendorArticleCount($aVendors[0]);

    }

    /**
     * Testing setting List Type
     */
    public function test_setListType() {

        $sDefVal = '999G';

        setListType($sDefVal);

        $this->assertEquals($sDefVal, getListTypeParamDirectly());
    }

    /**
     * Testing setting Category ID
     */
    public function test_setCategoryID() {

        $sDefVal = '999B';

        setCategoryID($sDefVal);

        $this->assertEquals($sDefVal, getCategoryIDParamDirectly());
    }

    /**
     * Testing getting list type
     */
    public function test_getListType() {



        $sDefVal = '999999D';
        modConfig::getInstance()->setParameter( 'listtype', $sDefVal );

        // must get from outside
        $this->assertEquals($sDefVal, getListType());

        $sDefVal = '999C';
        setListType($sDefVal);
        // must get from _sListType
        $this->assertEquals($sDefVal, getListType());
    }

    /**
     * Testing getting category ID
     */
    public function test_getCategoryID() {



        $sDefVal = '999999Z';
        modConfig::getInstance()->setParameter( 'cnid', $sDefVal );

        // must get from outside
        $this->assertEquals($sDefVal, getCategoryID());

        $sDefVal = '999Z';
        setCategoryID($sDefVal);
        // must get from _sListType
        $this->assertEquals($sDefVal, getCategoryID());
    }

    protected function _resizeImageTest($sTestImageFile, $sTestImageFileResized) {


        $sDir = "misc".DIRECTORY_SEPARATOR;
        $iWidth = 100;
        $iHeight = 48;
        if(!file_exists($sDir.$sTestImageFile)) {
            $sMsg = "Failed to find the image file: ".$sDir.$sTestImageFile;
            $this->fail($sMsg);
        }
        //actual test
        if(!(resizeImage($sDir.$sTestImageFile, $sDir.$sTestImageFileResized, $iWidth, $iHeight))) {
            $this->fail("Failed to call resizeImage()");
        }
        if(!is_file($sDir.$sTestImageFileResized)) {
            $this->fail("Failed to find the resized image file.");
        }
        $aImageSizeResized = getImageSize($sDir.$sTestImageFileResized);
        $iImageResizedWidth = $aImageSizeResized[0];
        $iImageResizedHeight = $aImageSizeResized[1];
        if(($iImageResizedWidth == $iWidth ) && ($iImageResizedHeight == $iHeight)) {
            //echo "Width: $iImageResizedWidth - Height: $iImageResizedHeight";
            unlink($sDir.$sTestImageFileResized);
            return true;
        }
        unlink($sDir.$sTestImageFileResized);
        return false;
    }

    /**
     * creates directory and file for copyDirTest
     */
    protected function _prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePath) {

        // try to create source dir
        if ( !is_dir($sSourceDir) ) {
           if ( mkdir($sSourceDir) ) {
                //create textfile
                $hHandle = fopen($sSourceFilePath, w);
                if ( $hHandle ) {
                    if ( !fclose($hHandle) ) {
                        echo "could not close file: $sSourceFilePath ";
                        return false;
                   }
                } else {
                    echo "could not open file: $sSourceFilePath ";
                    return false;
                }
           } else {
                echo "could not create directory: $sSourceDir ";
                return false;
           }
        }

        //try to create target dir
        if (!is_dir($sTargetDir)) {
             if ( !mkdir($sTargetDir) ) {
                echo "could not create directory: $sTargetDir ";
                return false;
             }
        }
        return true;
    }

    protected function _cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePath, $sTargetFilePath) {
        //try to remove dir and delete files
        if ( unlink($sTargetFilePath) ) {
            //$dirTargetHandle = opendir($sTargetDir);
            //closedir($dirTargetHandle);
            if ( !rmDir($sTargetDir) ) {
                echo "could not remove $sTargetDir ";
            }
        } else {
            echo "could not delete $sTargetFilePath ";
        }

        if ( unlink($sSourceFilePath) ) {
            //$dirSourceHandle = opendir($sSourceDir);
            //closedir($dirSourceHandle);
            if ( !rmDir($sSourceDir) ) {
                echo "after remove not remove $sSourceDir ";
            }
        } else {
            echo "could not delete $sSourceFilePath ";
        }

        return true;
    }

    /**
     * _ConvertDBDateTimeTest
     * @param   string  datetime to be converted
     * @param   string  datetime expected after conversion
     * @param   bool    format as mysql compatible
     * @param   bool    format to date only
     * @param   bool    skip
     */
    protected function _ConvertDBDateTimeTest($sInput = "", $sExpected, $blMysql = false, $blFormatDate = false, $blSkip = false) {

        $oConvObject = new oxField();
        if (!empty($sInput)) {
            $oConvObject->value = $sInput;
            $oConvObject->fldmax_length = strlen($sInput);
            $oConvObject->fldtype = "datetime";
        }
        modConfig::getInstance()->addClassVar('blSkipFormatConversion', $blSkip );
        convertDBDateTime($oConvObject, $blMysql, $blFormatDate);
        //echo "\nReturned: ->".$oConvObject->value."<-\nExpected: ->".$sExpected.'<-';
        if ($oConvObject->value == $sExpected) {
            return true;
        }
        return false;
    }

    /**
     * _ConvertDBTimestampTest
     * @param   string  datetime/timestamp to be converted
     * @param   string  datetime/timestamp expected after conversion
     * @param   bool    if true convert to timestamp
     * @param   bool    skip
     */
    protected function _ConvertDBTimestampTest($sInput = "", $sExpected, $blToTimeStamp = false, $blSkip = false) {

        $oConvObject = new oxField();
        if (!empty($sInput)) {
            $oConvObject->value = $sInput;
        }
        modConfig::getInstance()->addClassVar('blSkipFormatConversion', $blSkip );
        convertDBTimestamp($oConvObject, $blToTimeStamp);
        //echo "\nInput: ->$sInput<-\nReturned: ->".$oConvObject->value."<-\nExpected: ->".$sExpected.'<-';
        if ($oConvObject->value == $sExpected) {
            return true;
        }
        //echo "\nReturned: ->".$oConvObject->value."<-\nExpected: ->".$sExpected.'<-';
        return false;
    }

    /**
     * _ConvertDBDateTest
     * @param   string  date/timestamp to be converted
     * @param   string  date/timestamp expected after conversion
     * @param   bool    if true convert to timestamp
     * @param   bool    skip
     */
    protected function _ConvertDBDateTest($sInput = "", $sExpected, $blToTimeStamp = false, $blSkip = false) {

        $oConvObject = new oxField();
        if (!empty($sInput)) {
            $oConvObject->value = $sInput;
        }
        modConfig::getInstance()->addClassVar('blSkipFormatConversion', $blSkip );
        convertDBDate($oConvObject, $blToTimeStamp);
        //echo "\nReturned: ->".$oConvObject->value."<-\nExpected: ->".$sExpected.'<-';
        if ($oConvObject->value == $sExpected) {
            return true;
        }
        return false;

    }

    protected function _resizeGIFTest($sTestImageFile, $sTestImageFileResized) {


        $myConfig = oxConfig::getInstance();
        $gdver = $myConfig->iUseGDVersion;
        $sDir = "misc".DIRECTORY_SEPARATOR;
        $iWidth = 100;
        $iHeight = 48;
        if(!file_exists($sDir.$sTestImageFile)) {
            $sMsg = "Failed to find the GIF file: ".$sDir.$sTestImageFile;
            $this->fail($sMsg);
        }
        $aImageSizeOriginal = getImageSize($sDir.$sTestImageFile);
        $iImageOriginalWidth = $aImageSizeOriginal[0];
        $iImageOriginalHeight = $aImageSizeOriginal[1];
        //actual test
        if(!(resizeGif($sDir.$sTestImageFile, $sDir.$sTestImageFileResized, $iWidth, $iHeight, $iImageOriginalWidth, $iImageOriginalHeight, $gdver))) {
            $this->fail("Failed to call resizeGIF()");
        }
        if(!is_file($sDir.$sTestImageFileResized)) {
            $this->fail("Failed to find the resized image file.");
        }
        $aImageSizeResized = getImageSize($sDir.$sTestImageFileResized);
        $iImageResizedWidth = $aImageSizeResized[0];
        $iImageResizedHeight = $aImageSizeResized[1];
        if(($iImageResizedWidth == $iWidth ) && ($iImageResizedHeight == $iHeight)) {
            unlink($sDir.$sTestImageFileResized);
            return true;
        }
        unlink($sDir.$sTestImageFileResized);
        return false;
    }

    protected function _fillFileCache() {

        $sName = "testFileCache";
        $sInput = "test_test_test";
        for ($iMax = 0; $iMax < 10; $iMax++) {
            oxFileCache(true, $sName."_".$iMax, $sInput."_".$iMax);
        }
    }

    protected function _clearNavParams() {

        setListType('');
        setCategoryID('');
    }
}

