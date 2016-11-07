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
namespace Unit\Core;

use \oxFileException;
use \Exception;
use \oxRegistry;
use \oxTestModules;

//copied the implementation from http://php.net/sys_get_temp_dir
//in case it is needed more often it should be moved to some generic place
//T2009-04-16
if (!function_exists('sys_get_temp_dir')) {
    function sys_get_temp_dir()
    {
        if (!empty($_ENV['TMP'])) {
            return realpath($_ENV['TMP']);
        }

        if (!empty($_ENV['TMPDIR'])) {
            return realpath($_ENV['TMPDIR']);
        }

        if (!empty($_ENV['TEMP'])) {
            return realpath($_ENV['TEMP']);
        }

        $tempfile = tempnam(uniqid(rand(), true), '');
        if (file_exists($tempfile)) {
            unlink($tempfile);

            return realpath(dirname($tempfile));
        }
    }
}

class UtilsFileTest extends \OxidTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->aFiles = $_FILES;
        $aTmpDirectories[] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "targetDir";
        $aTmpDirectories[] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "sourceDir";

        foreach ($aTmpDirectories as $sDirectory) {
            if (is_dir(realpath($sDirectory))) {
                oxRegistry::get('oxUtilsFile')->deleteDir($sDirectory);
            }
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function tearDown()
    {
        oxRegistry::get("oxUtilsFile")->setConfig(null);
        $_FILES = $this->aFiles;
        $sDir = $this->getConfig()->getConfigParam('sShopDir') . "/$sOut/1/html/1";
        if (is_dir(realpath($sDir))) {
            oxRegistry::get("oxUtilsFile")->deleteDir($sDir);
        }

        parent::tearDown();
    }

    public function testGetUniqueFileName()
    {
        $sFilePath = $this->getConfig()->getPictureDir(false) . "/master/product/1/";

        $oUtilsFile = oxNew('oxUtilsFile');
        $this->assertEquals("2010_speed3_120_1(1).jpg", $oUtilsFile->UNITgetUniqueFileName($sFilePath, "2010_speed3_120_1", "jpg"));
    }

    public function testGetImageSize()
    {
        $oUtilsFile = oxNew('oxUtilsFile');

        $aDetailImageSizes = array("oxpic1" => "251*201", "oxpic2" => "252*202", "oxpic3" => "253*203");
        $this->getConfig()->setConfigParam("aDetailImageSizes", $aDetailImageSizes);
        $this->getConfig()->setConfigParam("sZoomImageSize", '450*450');
        $this->getConfig()->setConfigParam("sThumbnailsize", '100*100');

        // details img size
        $this->assertEquals(array(251, 201), $oUtilsFile->UNITgetImageSize(null, 1, 'aDetailImageSizes'));

        // details img size
        $this->assertEquals(array(253, 203), $oUtilsFile->UNITgetImageSize(null, 3, 'aDetailImageSizes'));

        // zoom img size
        $this->assertEquals(array(450, 450), $oUtilsFile->UNITgetImageSize(null, 2, 'sZoomImageSize'));

        // thumbnail img size
        $this->assertEquals(array(100, 100), $oUtilsFile->UNITgetImageSize(null, null, 'sThumbnailsize'));

        // non existing img type size
        $this->assertNull($oUtilsFile->UNITgetImageSize('nonexisting', '666', 'nonexisting'));
    }

    public function testUrlValidateBadUrl()
    {
        $oUtilsFile = oxNew('oxUtilsFile');
        $this->assertFalse($oUtilsFile->urlValidate("test/notvalid"));
        $this->assertFalse($oUtilsFile->urlValidate("http://www.oxid_non_existing_page.com"));

        $shopUrl = $this->getTestConfig()->getShopUrl();
        $this->assertTrue($oUtilsFile->urlValidate($shopUrl ."?param=value"));
    }

    public function testCheckFile()
    {
        $sName1 = time();
        $sName2 = __FILE__;

        $oUtilsFile = $this->getMock('oxUtilsFile', array('urlValidate'));
        $oUtilsFile->expects($this->once())->method('urlValidate')->will($this->returnValue(true));
        $this->assertTrue($oUtilsFile->checkFile($sName1));
        $this->assertTrue($oUtilsFile->checkFile($sName1));
        $this->assertTrue($oUtilsFile->checkFile($sName2));

        $aCache = oxRegistry::getSession()->getVariable("checkcache");
        $this->assertTrue($aCache[$sName1]);
        $this->assertTrue($aCache[$sName1]);
        $this->assertTrue($aCache[$sName2]);
    }

    public function testProcessFilesCallState()
    {
        $oObject = oxNew('oxbase');
        $oObject->testfield = $this->getMock('oxfield', array('setValue'));
        $oObject->testfield->expects($this->once())->method('setValue')->with($this->equalTo('testfilename'));

        $sProcessPath = $this->getConfig()->getConfigParam("sCompileDir");

        $aFiles = array();
        $aFiles['myfile']['name']['gif@testfield'] = 'testfilename.gif';
        $aFiles['myfile']['tmp_name']['gif@testfield'] = 'testimagesource/testfilename';

        $oUtilsFile = $this->getMock('oxutilsfile', array('_prepareImageName', '_getImagePath', '_moveImage'));
        $oUtilsFile->expects($this->once())->method('_prepareImageName')->with($this->equalTo('testfilename.gif'), $this->equalTo('gif'), $this->equalTo($this->getConfig()->isDemoShop()))->will($this->returnValue('testfilename'));
        $oUtilsFile->expects($this->once())->method('_getImagePath')->with($this->equalTo('gif'))->will($this->returnValue('testimagepath/'));
        $oUtilsFile->expects($this->once())->method('_moveImage')->with($this->equalTo('testimagesource/testfilename'), $this->equalTo('testimagepath/testfilename'))->will($this->returnValue(true));

        $oUtilsFile->processFiles($oObject, $aFiles);
    }

    public function testProcessFiles()
    {
        oxTestModules::addFunction('oxUtilspic', 'resizeImage', '{return true;}');

        $_FILES['myfile']['name'] = array('P1@oxarticles__oxpic1' => 'testname.gif');
        $_FILES['myfile']['tmp_name'] = array('P1@oxarticles__oxpic1' => 'testimagesource');
        $_FILES['myfile']['error'] = array('P1@oxarticles__oxpic1' => 0);

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getPictureDir'));
        $oConfig->expects($this->once())->method('getPictureDir')->will($this->returnValue('pictures_dir'));

        /** @var oxUtilsFile|PHPUnit_Framework_MockObject_MockObject $oUtilsFile */
        $oUtilsFile = $this->getMock("oxUtilsFile", array("_moveImage"));
        $oUtilsFile->expects($this->once())->method('_moveImage')->will($this->returnValue(true));

        $oUtilsFile->setConfig($oConfig);
        $oUtilsFile->processFiles(oxNew('oxArticle'));
    }

    public function testProcessNonImageFiles()
    {
        $_FILES['myfile']['name'] = array('FL@oxarticles__oxfile' => 'testname.pdf');
        $_FILES['myfile']['tmp_name'] = array('FL@oxarticles__oxfile' => 'testpdfsource');
        $_FILES['myfile']['error'] = array('P1@oxarticles__oxpic1' => 0);

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getPictureDir'));
        $oConfig->expects($this->once())->method('getPictureDir')->will($this->returnValue('pictures_dir'));

        /** @var oxUtilsFile|PHPUnit_Framework_MockObject_MockObject $oUtilsFile */
        $oUtilsFile = $this->getMock("oxUtilsFile", array("_moveImage", "_copyFile"));
        $oUtilsFile->expects($this->once())->method('_moveImage')->will($this->returnValue(true));
        $oUtilsFile->expects($this->never())->method('_copyFile')->will($this->returnValue(false));

        $oUtilsFile->setConfig($oConfig);
        $oUtilsFile->processFiles();
    }

    public function testProcessFilesSkipBadFiles()
    {
        $this->setExpectedException('oxFileException', 'this is ok');

        $_FILES['myfile']['name'] = array('testname.php5');
        $_FILES['myfile']['tmp_name'] = 'testname';

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('isDemoShop'));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        /** @var oxUtilsFile|PHPUnit_Framework_MockObject_MockObject $oUtilsFile */
        $oUtilsFile = oxRegistry::get("oxUtilsFile");
        $oUtilsFile->setConfig($oConfig);
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{throw new oxFileException("this is ok");}');
        $oUtilsFile->processFiles();
    }

    public function testProcessFilesAllowsOnlySomeFilesOnDemo()
    {
        $_FILES['myfile']['name'] = array('testname.unknown');
        $_FILES['myfile']['tmp_name'] = 'testname';
        //$oConfig = $this->getMock('oxConfig', array('hasModule'));
        //$oConfig->expects( $this->once() )->method('hasModule')->with( $this->equalTo( 'demoshop' ) )->will( $this->returnValue( true ) );
        $oConfig = $this->getMock('oxConfig', array('isDemoShop'));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));
        $oUF = oxRegistry::get("oxUtilsFile");
        $oUF->setConfig($oConfig);
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{throw new Exception("this is ok");}');
        try {
            $oUF->processFiles();
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('this is ok', $e->getMessage());
        }
    }

    public function testCopyDir()
    {
        $sTempDir = sys_get_temp_dir();
        $sTargetDir = $sTempDir . DIRECTORY_SEPARATOR . "targetDir";
        $sSourceDir = $sTempDir . DIRECTORY_SEPARATOR . "sourceDir";

        $sSourceDeeperDir = $sSourceDir . DIRECTORY_SEPARATOR . "deeper";
        $sTargetDeeperDir = $sTargetDir . DIRECTORY_SEPARATOR . "deeper";
        $sSourceFilePathText = $sSourceDir . DIRECTORY_SEPARATOR . "test.txt";
        $sTargetFilePathText = $sTargetDir . DIRECTORY_SEPARATOR . "test.txt";
        $sSourceFilePathnopic = $sSourceDir . DIRECTORY_SEPARATOR . "nopic.jpg";
        $sTargetFilePathnopic = $sTargetDir . DIRECTORY_SEPARATOR . "nopic.jpg";
        $sSourceFilePathnopicIco = $sSourceDir . DIRECTORY_SEPARATOR . "nopic_ico.jpg";
        $sTargetFilePathnopicIco = $sTargetDir . DIRECTORY_SEPARATOR . "nopic_ico.jpg";
        $sSourceFilePathCVS = $sSourceDir . DIRECTORY_SEPARATOR . "deeper" . DIRECTORY_SEPARATOR . "CVS";
        $sTargetFilePathCVS = $sTargetDir . DIRECTORY_SEPARATOR . "deeper" . DIRECTORY_SEPARATOR . "CVS";

        //test with textfile
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText)) {

            oxRegistry::get("oxUtilsFile")->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathText), is_file($sTargetFilePathText));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText, $sTargetFilePathText);
        }

        //test with nopic.jpg
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic)) {

            oxRegistry::get("oxUtilsFile")->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathnopic), is_file($sTargetFilePathnopic));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic, $sTargetFilePathnopic);
        }

        //test with nopic_ico.jpg
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopicIco)) {

            oxRegistry::get("oxUtilsFile")->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathnopicIco), is_file($sTargetFilePathnopicIco));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopicIco, $sTargetFilePathnopicIco);
        }

        //test with textfile and sub folder with CVS file
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText)) {
            $this->_prepareCopyDir($sSourceDeeperDir, $sTargetDeeperDir, $sSourceFilePathCVS);

            oxRegistry::get("oxUtilsFile")->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathCVS), is_file($sTargetFilePathCVS));
            $this->_cleanupCopyDir($sSourceDeeperDir, $sTargetDeeperDir, $sSourceFilePathCVS, $sTargetFilePathCVS);
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText, $sTargetFilePathText);
        }
    }


    public function testDeleteDir()
    {
        //set-up a directory and a subdirectory
        $sTempDir = rtrim(sys_get_temp_dir(), '/') . '/';
        $sDir = $sTempDir . 'TestDirectory';
        $sSubDir = 'SubTestDirectory';
        $sFileName = 'testFile.txt';
        if (mkdir($sDir) && mkdir($sDir . DIRECTORY_SEPARATOR . $sSubDir) && is_dir($sDir) && is_dir($sDir . addslashes(DIRECTORY_SEPARATOR) . $sSubDir)) {
            $hFileHandle = fopen($sDir . DIRECTORY_SEPARATOR . $sFileName, 'w');
            if (!$hFileHandle) {
                $this->_cleanupDeleteDir($hFileHandle, $sDir, $sFileName, $sSubDir);
                $this->fail('Failed to create file!');
            }
        } else {
            $this->_cleanupDeleteDir(null, $sDir, $sFileName, $sSubDir);
            $this->fail('Failed to set up test dirs');
        }
        $this->_cleanupDeleteDir($hFileHandle, $sDir, $sFileName, $sSubDir);
    }


    public function testReadRemoteFileAsString()
    {
        $oUtilsFile = oxNew('oxUtilsFile');
        $this->assertEquals("", $oUtilsFile->readRemoteFileAsString(getShopBasePath() . time()));
        $this->assertEquals("<?php", substr($oUtilsFile->readRemoteFileAsString(getShopBasePath() . "index.php"), 0, 5));
    }


    public function testHandleUploadedWrongFileType()
    {
        $aFiles['name'] = 'testfile';
        $aFiles['tmp_name'] = 'testfile';

        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        oxRegistry::get("oxUtilsFile")->handleUploadedFile($aFiles, '/out/media/');
    }

    public function testProcessFileEmpty()
    {
        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\StandardException', 'EXCEPTION_NOFILE');
        oxRegistry::get("oxUtilsFile")->processFile(null, '/out/media/');
    }

    public function testProcessFileWrongChar1()
    {

        $_FILES['fileItem']['name'] = 'testfile_\xc4\xaf\xc5\xa1.jpg';
        $_FILES['fileItem']['tmp_name'] = 'testfile';

        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\StandardException', 'EXCEPTION_FILENAMEINVALIDCHARS');
        oxRegistry::get("oxUtilsFile")->processFile('fileItem', '/out/media/');
    }

    public function testProcessFileWrongChar2()
    {
        $_FILES['fileItem']['name'] = 'TEST.te.stfile_0__.jpg';
        $_FILES['fileItem']['tmp_name'] = 'testfile';
        oxRegistry::get("oxUtilsFile")->processFile('fileItem', '/out/media/');
    }

    public function testProcessFileWrongFileType()
    {
        $_FILES['fileItem']['name'] = 'testfile';
        $_FILES['fileItem']['tmp_name'] = 'testfile';

        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        oxRegistry::get("oxUtilsFile")->processFile('fileItem', '/out/media/');
    }

    public function testProcessFileTooBigFile()
    {
        $_FILES['fileItem']['name'] = 'testfile.jpg';
        $_FILES['fileItem']['tmp_name'] = 'testfile.jpg';
        $_FILES['fileItem']['error'] = 1;

        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        oxRegistry::get("oxUtilsFile")->processFile('fileItem', '/out/media/');
    }

    public function testNormalizeDir()
    {
        $sFullDir = "/test/good/dir/";
        $this->assertEquals($sFullDir, oxRegistry::get("oxUtilsFile")->normalizeDir($sFullDir));

        $sHalfDir = "/test/good/dir";
        $this->assertEquals($sFullDir, oxRegistry::get("oxUtilsFile")->normalizeDir($sHalfDir));

        $this->assertEquals('', oxRegistry::get("oxUtilsFile")->normalizeDir(''));
        $this->assertEquals(null, oxRegistry::get("oxUtilsFile")->normalizeDir(null));
    }


    public function testTranslateError()
    {
        $oUF = oxNew('oxUtilsFile');
        $this->assertEquals(
            '',
            $oUF->translateError(0, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_1',
            $oUF->translateError(1, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_2',
            $oUF->translateError(2, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_3',
            $oUF->translateError(3, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_4',
            $oUF->translateError(4, 'fileName')
        );
        $this->assertEquals(
            '',
            $oUF->translateError(5, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_6',
            $oUF->translateError(6, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_7',
            $oUF->translateError(7, 'fileName')
        );
        $this->assertEquals(
            'EXCEPTION_FILEUPLOADERROR_8',
            $oUF->translateError(8, 'fileName')
        );
        $this->assertEquals(
            '',
            $oUF->translateError(9, 'fileName')
        );
        $this->assertEquals(
            '',
            $oUF->translateError(-1, 'fileName')
        );
    }

    public function testProcessFilesNewCounter()
    {
        oxTestModules::addFunction('oxUtilspic', 'resizeImage', '{return true;}');

        $_FILES['myfile']['name'] = array('P1@oxarticles__oxpic1' => 'testname.gif', 'P1@oxarticles__oxpic2' => 'testname2.gif');
        $_FILES['myfile']['tmp_name'] = array('P1@oxarticles__oxpic1' => 'testimagesource', 'P1@oxarticles__oxpic2' => 'testimagesource2');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getPictureDir'));
        $oConfig->expects($this->any())->method('getPictureDir')->will($this->returnValue('pictures_dir'));

        /** @var oxUtilsFile|PHPUnit_Framework_MockObject_MockObject $oUtilsFile */
        $oUtilsFile = $this->getMock("oxUtilsFile", array("_moveImage"));
        $oUtilsFile->expects($this->any())->method('_moveImage')->will($this->returnValue(true));

        $oUtilsFile->setConfig($oConfig);
        $oUtilsFile->processFiles(oxNew('oxArticle'));

        $this->assertEquals($oUtilsFile->getNewFilesCounter(), 2, "Check how much new files add.");
    }

    // 20070720-AS - End setup
    // 20070720-AS - assure generated file exists and it's handle is closed before deleting
    protected function _cleanupDeleteDir($hFileHandle, $sDir, $sFileName, $sSubDir)
    {
        if (($hFileHandle != null) && (fclose($hFileHandle))) {
            $blDeleted = oxRegistry::get("oxUtilsFile")->deleteDir($sDir); //actual test
            $this->assertNotEquals($blDeleted, is_dir($sDir));
        } else {
            // cleanup the created dirs/subdirs/file
            $blFileDeleted = unlink($sDir . DIRECTORY_SEPARATOR . $sFileName);
            $blTestSubDirDeleted = rmDir($sDir . DIRECTORY_SEPARATOR . $sSubDir);
            $blTestDirDeleted = rmDir($sDir);
            if (!($blFileDeleted && $blTestSubDirDeleted && $blTestDirDeleted)) {
                $this->fail('Failed to delete dirs and/or test file!');
            }
        }
    }

    /**
     * creates directory and file for copyDirTest
     */
    protected function _prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePath)
    {

        // try to create source dir
        if (!is_dir($sSourceDir)) {
            if (mkdir($sSourceDir)) {
                //create textfile
                $hHandle = fopen($sSourceFilePath, w);
                if ($hHandle) {
                    if (!fclose($hHandle)) {
                        $this->fail("could not close file: $sSourceFilePath ");
                    }
                } else {
                    $this->fail("could not open file: $sSourceFilePath ");
                }
            } else {
                $this->fail("could not create directory: $sSourceDir ");
            }
        }

        //try to create target dir
        if (!is_dir($sTargetDir)) {
            if (!mkdir($sTargetDir)) {
                $this->fail("could not create directory: $sTargetDir ");
            }
        }

        return true;
    }

    protected function _cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePath, $sTargetFilePath)
    {
        //try to remove dir and delete files
        if (file_exists ($sTargetFilePath) && unlink($sTargetFilePath)) {
            //$dirTargetHandle = opendir($sTargetDir);
            //closedir($dirTargetHandle);
            if (!rmDir($sTargetDir)) {
                $this->fail("could not remove $sTargetDir ");
            }
        } else {
            $this->fail("could not delete $sTargetFilePath ");
        }

        if (file_exists ($sSourceFilePath) && unlink($sSourceFilePath)) {
            //$dirSourceHandle = opendir($sSourceDir);
            //closedir($dirSourceHandle);
            if (!rmDir($sSourceDir)) {
                $this->fail("after remove not remove $sSourceDir ");
            }
        } else {
            $this->fail("could not delete $sSourceFilePath ");
        }
    }
}
