<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxFileException;
use \Exception;
use \oxRegistry;
use \oxTestModules;

class UtilsFileTest extends \OxidTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->aFiles = $_FILES;
        $aTmpDirectories[] = $this->getDirectoryPathToCreateFiles() . DIRECTORY_SEPARATOR . "targetDir";
        $aTmpDirectories[] = $this->getDirectoryPathToCreateFiles() . DIRECTORY_SEPARATOR . "sourceDir";

        foreach ($aTmpDirectories as $sDirectory) {
            if (is_dir(realpath($sDirectory))) {
                \OxidEsales\Eshop\Core\Registry::getUtilsFile()->deleteDir($sDirectory);
            }
        }
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

    /**
     * @group slow-tests
     */
    public function testUrlValidateBadUrl()
    {
        $oUtilsFile = oxNew('oxUtilsFile');
        $this->assertFalse($oUtilsFile->urlValidate("test/notvalid"));
        $this->assertFalse($oUtilsFile->urlValidate("http://www.oxid_non_existing_page.com"));

        $this->activateTheme('azure');
        $shopUrl = $this->getTestConfig()->getShopUrl();
        $this->assertTrue($oUtilsFile->urlValidate($shopUrl ."?param=value"));
    }

    public function testCheckFile()
    {
        $sName1 = time();
        $sName2 = __FILE__;

        $oUtilsFile = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array('urlValidate'));
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
        $oObject->testfield = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('setValue'));
        $oObject->testfield->expects($this->once())->method('setValue')->with($this->equalTo('testfilename'));

        $sProcessPath = $this->getConfig()->getConfigParam("sCompileDir");

        $aFiles = array();
        $aFiles['myfile']['name']['gif@testfield'] = 'testfilename.gif';
        $aFiles['myfile']['tmp_name']['gif@testfield'] = 'testimagesource/testfilename';

        $oUtilsFile = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array('_prepareImageName', '_getImagePath', '_moveImage'));
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

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getPictureDir'));
        $oConfig->expects($this->once())->method('getPictureDir')->will($this->returnValue('pictures_dir'));

        /** @var oxUtilsFile|PHPUnit\Framework\MockObject\MockObject $oUtilsFile */
        $oUtilsFile = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array("_moveImage"));
        $oUtilsFile->expects($this->once())->method('_moveImage')->will($this->returnValue(true));

        $oUtilsFile->setConfig($oConfig);
        $oUtilsFile->processFiles(oxNew('oxArticle'));
    }

    public function testProcessNonImageFiles()
    {
        $_FILES['myfile']['name'] = array('FL@oxarticles__oxfile' => 'testname.pdf');
        $_FILES['myfile']['tmp_name'] = array('FL@oxarticles__oxfile' => 'testpdfsource');
        $_FILES['myfile']['error'] = array('P1@oxarticles__oxpic1' => 0);

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getPictureDir'));
        $oConfig->expects($this->once())->method('getPictureDir')->will($this->returnValue('pictures_dir'));

        /** @var oxUtilsFile|PHPUnit\Framework\MockObject\MockObject $oUtilsFile */
        $oUtilsFile = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array("_moveImage", "_copyFile"));
        $oUtilsFile->expects($this->once())->method('_moveImage')->will($this->returnValue(true));
        $oUtilsFile->expects($this->never())->method('_copyFile')->will($this->returnValue(false));

        $oUtilsFile->setConfig($oConfig);
        $oUtilsFile->processFiles();
    }

    public function testProcessFilesSkipBadFiles()
    {
        $this->expectException('oxFileException');
        $this->expectExceptionMessage('this is ok');

        $_FILES['myfile']['name'] = array('testname.php5');
        $_FILES['myfile']['tmp_name'] = 'testname';

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('isDemoShop'));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        /** @var oxUtilsFile|PHPUnit\Framework\MockObject\MockObject $oUtilsFile */
        $oUtilsFile = \OxidEsales\Eshop\Core\Registry::getUtilsFile();
        $oUtilsFile->setConfig($oConfig);
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{throw new oxFileException("this is ok");}');
        $oUtilsFile->processFiles();
    }

    public function testProcessFilesAllowsOnlySomeFilesOnDemo()
    {
        $_FILES['myfile']['name'] = array('testname.unknown');
        $_FILES['myfile']['tmp_name'] = 'testname';
        //$oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('hasModule'));
        //$oConfig->expects( $this->once() )->method('hasModule')->with( $this->equalTo( 'demoshop' ) )->will( $this->returnValue( true ) );
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('isDemoShop'));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));
        $oUF = \OxidEsales\Eshop\Core\Registry::getUtilsFile();
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
        $sTempDir = $this->getDirectoryPathToCreateFiles();
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
            \OxidEsales\Eshop\Core\Registry::getUtilsFile()->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathText), is_file($sTargetFilePathText));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText, $sTargetFilePathText);
        }

        //test with nopic.jpg
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsFile()->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathnopic), is_file($sTargetFilePathnopic));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopic, $sTargetFilePathnopic);
        }

        //test with nopic_ico.jpg
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopicIco)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsFile()->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathnopicIco), is_file($sTargetFilePathnopicIco));
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathnopicIco, $sTargetFilePathnopicIco);
        }

        //test with textfile and sub folder with CVS file
        if ($this->_prepareCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText)) {
            $this->_prepareCopyDir($sSourceDeeperDir, $sTargetDeeperDir, $sSourceFilePathCVS);

            \OxidEsales\Eshop\Core\Registry::getUtilsFile()->copyDir($sSourceDir, $sTargetDir);
            $this->assertEquals(is_file($sSourceFilePathCVS), is_file($sTargetFilePathCVS));
            $this->_cleanupCopyDir($sSourceDeeperDir, $sTargetDeeperDir, $sSourceFilePathCVS, $sTargetFilePathCVS);
            $this->_cleanupCopyDir($sSourceDir, $sTargetDir, $sSourceFilePathText, $sTargetFilePathText);
        }
    }


    public function testDeleteDir()
    {
        //set-up a directory and a subdirectory
        $sTempDir = rtrim($this->getDirectoryPathToCreateFiles(), '/') . '/';
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

        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        \OxidEsales\Eshop\Core\Registry::getUtilsFile()->handleUploadedFile($aFiles, '/out/media/');
    }

    public function testProcessFileEmpty()
    {
        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        $this->expectExceptionMessage('EXCEPTION_NOFILE');
        \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile(null, '/out/media/');
    }

    public function testProcessFileWrongChar1()
    {
        $_FILES['fileItem']['name'] = 'testfile_\xc4\xaf\xc5\xa1.jpg';
        $_FILES['fileItem']['tmp_name'] = 'testfile';

        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        $this->expectExceptionMessage('EXCEPTION_FILENAMEINVALIDCHARS');
        \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile('fileItem', '/out/media/');
    }

    public function testProcessFileWrongChar2()
    {
        $_FILES['fileItem']['name'] = 'TEST.te.stfile_0__.jpg';
        $_FILES['fileItem']['tmp_name'] = 'testfile';
        \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile('fileItem', '/out/media/');
    }

    public function testProcessFileWrongFileType()
    {
        $_FILES['fileItem']['name'] = 'testfile';
        $_FILES['fileItem']['tmp_name'] = 'testfile';

        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile('fileItem', '/out/media/');
    }

    public function testProcessFileTooBigFile()
    {
        $_FILES['fileItem']['name'] = 'testfile.jpg';
        $_FILES['fileItem']['tmp_name'] = 'testfile.jpg';
        $_FILES['fileItem']['error'] = 1;

        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\StandardException');
        \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile('fileItem', '/out/media/');
    }

    public function testNormalizeDir()
    {
        $sFullDir = "/test/good/dir/";
        $this->assertEquals($sFullDir, \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir($sFullDir));

        $sHalfDir = "/test/good/dir";
        $this->assertEquals($sFullDir, \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir($sHalfDir));

        $this->assertEquals('', \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir(''));
        $this->assertEquals(null, \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir(null));
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

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getPictureDir'));
        $oConfig->expects($this->any())->method('getPictureDir')->will($this->returnValue('pictures_dir'));

        /** @var oxUtilsFile|PHPUnit\Framework\MockObject\MockObject $oUtilsFile */
        $oUtilsFile = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array("_moveImage"));
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
            $blDeleted = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->deleteDir($sDir); //actual test
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
                $hHandle = fopen($sSourceFilePath, 'w');
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
        if (file_exists($sTargetFilePath) && unlink($sTargetFilePath)) {
            //$dirTargetHandle = opendir($sTargetDir);
            //closedir($dirTargetHandle);
            if (!rmDir($sTargetDir)) {
                $this->fail("could not remove $sTargetDir ");
            }
        } else {
            $this->fail("could not delete $sTargetFilePath ");
        }

        if (file_exists($sSourceFilePath) && unlink($sSourceFilePath)) {
            //$dirSourceHandle = opendir($sSourceDir);
            //closedir($dirSourceHandle);
            if (!rmDir($sSourceDir)) {
                $this->fail("after remove not remove $sSourceDir ");
            }
        } else {
            $this->fail("could not delete $sSourceFilePath ");
        }
    }

    /**
     * Return path to directory where files could be created.
     *
     * @return string
     */
    private function getDirectoryPathToCreateFiles()
    {
        $vfsStream = $this->getVfsStreamWrapper();
        $pathToRoot = $vfsStream->createStructure([]);

        return $pathToRoot;
    }
}
