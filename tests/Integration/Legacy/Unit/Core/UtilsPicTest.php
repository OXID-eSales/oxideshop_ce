<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxField;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsPic;
use OxidEsales\EshopCommunity\Core\Registry;
use oxTestModules;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class UtilsPicTest extends \PHPUnit\Framework\TestCase
{
    private $testPicture = 'test.jpg';

    private $testPicturePlaceholder = 'nopic.jpg';

    private $testPicturePlaceholderIco = 'nopic_ico.jpg';

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareTestDirectories();
    }

    protected function tearDown(): void
    {
        $this->clearTestDirectories();
        parent::tearDown();
    }

    public function testResizeImage()
    {
        $sTestImageFileJPG = "test.jpg";
        $sTestImageFileJPGSmall = "test_smaller.jpg";
        $sTestImageFileResizedJPG = "test_resized.jpg";
        $sTestImageFileSmallResizedJPG = "test_smallresized.jpg";

        // actual test
        $this->assertTrue($this->resizeImageTest($sTestImageFileJPG, $sTestImageFileResizedJPG));

        // do not resize smaller pics
        $this->assertFalse($this->resizeImageTest($sTestImageFileJPGSmall, $sTestImageFileSmallResizedJPG));

        $sTestImageFileGIF = "test.gif";
        $sTestImageFileGIFSmall = "test_smaller.gif";
        $sTestImageFileResizedGIF = "test_resized.gif";
        $sTestImageFileSmallResizedGIF = "test_smallresized.gif";

        // actual test
        $this->assertTrue($this->resizeImageTest($sTestImageFileGIF, $sTestImageFileResizedGIF));

        // do not resize smaller pics
        $this->assertFalse($this->resizeImageTest($sTestImageFileGIFSmall, $sTestImageFileSmallResizedGIF));

        $sTestImageFilePNG = "test.png";
        $sTestImageFilePNGSmall = "test_smaller.png";
        $sTestImageFileResizedPNG = "test_resized.png";
        $sTestImageFileSmallResizedPNG = "test_smallresized.png";

        // actual test
        $this->assertTrue($this->resizeImageTest($sTestImageFilePNG, $sTestImageFileResizedPNG));

        // do not resize smaller pics
        $this->assertFalse($this->resizeImageTest($sTestImageFilePNGSmall, $sTestImageFileSmallResizedPNG));

        // resizing according to height param
        $this->assertTrue($this->resizeImageTest($sTestImageFilePNG, $sTestImageFileResizedPNG, 21, 10));
    }

    protected function resizeImageTest($sTestImageFile, $sTestImageFileResized, $iWidth = 100, $iHeight = 48)
    {
        $sDir = __DIR__ . "/../testData/misc" . DIRECTORY_SEPARATOR;
        if (!file_exists($sDir . $sTestImageFile)) {
            $sMsg = "Failed to find the image file: " . $sDir . $sTestImageFile;
            $this->fail($sMsg);
        }

        //actual test
        $resizeResult = Registry::getUtilsPic()
            ->resizeImage($sDir . $sTestImageFile, $sDir . $sTestImageFileResized, $iWidth, $iHeight);
        if (!$resizeResult) {
            $this->fail("Failed to call resizeImage()");
        }

        if (!is_file($sDir . $sTestImageFileResized)) {
            $this->fail("Failed to find the resized image file.");
        }

        $aImageSizeResized = getImageSize($sDir . $sTestImageFileResized);
        $iImageResizedWidth = $aImageSizeResized[0];
        $iImageResizedHeight = $aImageSizeResized[1];
        if (($iImageResizedWidth == $iWidth) && ($iImageResizedHeight == $iHeight)) {
            unlink($sDir . $sTestImageFileResized);

            return true;
        }

        unlink($sDir . $sTestImageFileResized);

        return false;
    }

    public function testDeletePictureWithDemoShop(): void
    {
        $testPicturePath = Path::join(__DIR__, $this->testPicture);
        touch($testPicturePath);
        $config = $this->createConfiguredMock(Config::class, ['isDemoShop' => true]);
        Registry::set(Config::class, $config);

        $result = (new UtilsPic())->deletePicture($this->testPicture, __DIR__);

        $this->assertFileExists($testPicturePath);
        $this->assertFalse($result);
    }

    public function testDeletePictureWithPlaceholderImage(): void
    {
        $placeholderPicturePath = Path::join(__DIR__, $this->testPicturePlaceholder);
        touch($placeholderPicturePath);

        $result = (new UtilsPic())->deletePicture($this->testPicturePlaceholder, __DIR__);

        $this->assertFileExists($placeholderPicturePath);
        $this->assertFalse($result);
    }

    public function testDeletePictureWithPlaceholderIconImage(): void
    {
        $placeholderPictureIcoPath = Path::join(__DIR__, $this->testPicturePlaceholderIco);
        touch($placeholderPictureIcoPath);

        $result = (new UtilsPic())->deletePicture($this->testPicturePlaceholderIco, __DIR__);

        $this->assertFileExists($placeholderPictureIcoPath);
        $this->assertFalse($result);
    }

    public function testDeletePictureWithMissingFile(): void
    {
        $nonExistingDirectory = Path::join('non', 'existing', 'directory');

        $result = (new UtilsPic())->deletePicture('some-filename.jpg', $nonExistingDirectory);

        $this->assertFalse($result);
    }

    public function testDeletePictureWithExistingFile(): void
    {
        $masterPicturesPath = Path::join(__DIR__, 'testFiles', 'master', 'product');
        $testPicturePath = Path::join($masterPicturesPath, $this->testPicture);
        touch($testPicturePath);

        $result = (new UtilsPic())->deletePicture($this->testPicture, $masterPicturesPath);

        $this->assertFileDoesNotExist($testPicturePath);
        $this->assertTrue($result);
    }

    /**
     * Data provider for testIsPicDeletable.
     *
     * @return array
     */
    public function testIsPicDeletableDataProvider()
    {
        return [['testOK.jpg', 1, true], ['testFail.jpg', 2, false]];
    }

    /**
     * Test isPicDeletable method.
     *
     * @param string $filename
     * @param int|null $response
     * @param bool $expectedResult
     *
     * @dataProvider testIsPicDeletableDataProvider
     */
    public function testIsPicDeletable($filename, $response, $expectedResult)
    {
        $utilsPicMock = $this->getMock(UtilsPic::class, ['fetchIsImageDeletable']);
        $utilsPicMock->method('fetchIsImageDeletable')->willReturn($response);

        $this->assertEquals($expectedResult, $utilsPicMock->isPicDeletable($filename, 'test', 'file'));
    }

    /**
     * Test IsPicDeletable with nopic.jpg case.
     */
    public function testIsPicDeletableNoPic()
    {
        $utilsPicMock = $this->getMock(UtilsPic::class, ['fetchIsImageDeletable']);
        $utilsPicMock->expects($this->never())->method('fetchIsImageDeletable');

        $this->assertEquals(false, $utilsPicMock->isPicDeletable('nopic.jpg', 'test', 'file'));
    }

    /**
     * Testing OverwritePic logics
     */
    // bad input
    public function testOverwritePicBadInput()
    {
        $oUtilsPic = $this->getMock(UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        $blFalse = $oUtilsPic->overwritePic(new stdClass(), 'xxx', 'xxx', '', '', '', '');
        $this->assertFalse($blFalse);
    }

    // params are not ok
    public function testOverwritePicBadParams()
    {
        $oUtilsPic = $this->getMock(UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        $oObject = new stdClass();
        $oObject->oxtbl__oxpic = new oxField('yyy', oxField::T_RAW);

        $blFalse = $oUtilsPic->overwritePic($oObject, 'oxtbl', 'oxpic', '', '', ['oxtbl__oxpic' => 'yyy'], '');
        $this->assertFalse($blFalse);
    }

    // all is fine
    public function testOverwritePicGoodParams()
    {
        $oFiles = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, ['getImageDirByType']);
        $oFiles->expects($this->atLeastOnce())->method('getImageDirByType')->will($this->returnValue('/test_image_dir/'));
        oxTestModules::addModuleObject('oxUtilsFile', $oFiles);

        $oUtilsPic = $this->getMock(UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->once())->method('safePictureDelete')->with($this->equalTo('yyy'), $this->equalTo('yyy/test_image_dir/'), $this->equalTo('oxtbl'), $this->equalTo('oxpic'))->will($this->returnValue(true));

        $oObject = new stdClass();
        $oObject->oxtbl__oxpic = new oxField('yyy', oxField::T_RAW);

        $blTrue = $oUtilsPic->overwritePic($oObject, 'oxtbl', 'oxpic', 'TEST_TYPE', '', ['oxtbl__oxpic' => 'xxx'], 'yyy');
        $this->assertTrue($blTrue);
    }

    // Test if corect path to file is generated (M:1268)
    public function testOverwritePic_generatesCorectFilePath()
    {
        $oFiles = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, ['getImageDirByType']);
        $oFiles->expects($this->atLeastOnce())->method('getImageDirByType')->will($this->returnValue('/testType_dir/'));
        oxTestModules::addModuleObject('oxUtilsFile', $oFiles);

        $oUtilsPic = $this->getMock(UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->once())->method('safePictureDelete')->with($this->equalTo('testPictureName'), $this->equalTo('testAbsPath/testType_dir/'));

        $oObject = new stdclass();
        $oObject->oxtbl__oxpic = new oxField('testPictureName', oxField::T_RAW);

        $oUtilsPic->overwritePic($oObject, 'oxtbl', 'oxpic', 'testType', 'testPath', ['oxtbl__oxpic' => 'xxx'], 'testAbsPath');
    }

    /**
     * Testing safe deletion code
     */
    // deeper code must not allow deletion
    public function testSafePictureDeleteMustFailDeletion()
    {
        $oUtilsPic = $this->getMock(UtilsPic::class, ['isPicDeletable', 'deletePicture']);
        $oUtilsPic->expects($this->once())->method('isPicDeletable')->will($this->returnValue(false));
        $oUtilsPic->expects($this->never())->method('deletePicture');

        $this->assertFalse($oUtilsPic->safePictureDelete('', '', '', ''));
    }

    //
    public function testSafePictureDeleteMustSucceed()
    {
        $oUtilsPic = $this->getMock(UtilsPic::class, ['isPicDeletable', 'deletePicture']);
        $oUtilsPic->expects($this->once())->method('isPicDeletable')->will($this->returnValue(true));
        $oUtilsPic->expects($this->once())->method('deletePicture')->will($this->returnValue(true));

        $this->assertTrue($oUtilsPic->safePictureDelete('', '', '', ''));
    }

    public function testResizeGif()
    {
        $sTestImageFileGIF = "test.gif";
        $sTestImageFileResizedGIF = "test_resized_ResizeGIF.gif";

        // actual test
        $this->assertTrue($this->resizeGIFTest($sTestImageFileGIF, $sTestImageFileResizedGIF));

        // checking if works with "gd 1"
        $this->assertTrue($this->resizeGIFTest($sTestImageFileGIF, $sTestImageFileResizedGIF, 1));
    }

    protected function resizeGIFTest($sTestImageFile, $sTestImageFileResized, $gdver = 2)
    {
        $myUtils = oxNew('oxUtilsPic');
        $sDir = __DIR__ . "/../testData/misc" . DIRECTORY_SEPARATOR;
        $iWidth = 100;
        $iHeight = 48;
        if (!file_exists($sDir . $sTestImageFile)) {
            $sMsg = "Failed to find the GIF file: " . $sDir . $sTestImageFile;
            $this->fail($sMsg);
        }

        $aImageSizeOriginal = getImageSize($sDir . $sTestImageFile);
        $iImageOriginalWidth = $aImageSizeOriginal[0];
        $iImageOriginalHeight = $aImageSizeOriginal[1];
        //actual test
        if (!($myUtils->resizeGif($sDir . $sTestImageFile, $sDir . $sTestImageFileResized, $iWidth, $iHeight, $iImageOriginalWidth, $iImageOriginalHeight, $gdver, false))) {
            $this->fail("Failed to call resizeGIF()");
        }

        if (!is_file($sDir . $sTestImageFileResized)) {
            $this->fail("Failed to find the resized image file.");
        }

        $aImageSizeResized = getImageSize($sDir . $sTestImageFileResized);
        $iImageResizedWidth = $aImageSizeResized[0];
        $iImageResizedHeight = $aImageSizeResized[1];
        if (($iImageResizedWidth == $iWidth) && ($iImageResizedHeight == $iHeight)) {
            unlink($sDir . $sTestImageFileResized);

            return true;
        }

        unlink($sDir . $sTestImageFileResized);

        return false;
    }

    private function prepareTestDirectories(): void
    {
        (new Filesystem())->mkdir(Path::join(__DIR__, 'testFiles', 'master', 'product'));
        (new Filesystem())->mkdir(Path::join(__DIR__, 'testFiles', 'generated', 'product'));
    }

    private function clearTestDirectories(): void
    {
        (new Filesystem())->remove(Path::join(__DIR__, 'testFiles'));
    }
}
