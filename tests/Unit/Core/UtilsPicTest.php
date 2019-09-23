<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxField;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use oxRegistry;
use oxTestModules;
use stdClass;

class UtilsPicTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        // preparing data for spec test
        switch ($this->getName()) {

            case "testDeletePictureExisting":

                $myConfig = $this->getConfig();

                // setup-> create a copy of a picture and delete this one for successful test
                $sOrigTestPicFile = "detail1_z3_ico_th.jpg";
                $sOrigTestIconFile = "detail1_z3_ico_th.jpg"; // we simply fake an icon file by copying the same
                $sCloneTestPicFile = "CCdetail1_z3_ico_th.jpg";
                $sCloneTestIconFile = "CCdetail1_z3_ico_th.jpg";

                $sDir = $myConfig->getPictureDir(false) . "master/product/thumb/";

                copy($sDir . $sOrigTestPicFile, $sDir . $sCloneTestPicFile);
                copy($sDir . $sOrigTestIconFile, $sDir . $sCloneTestIconFile);

                break;

        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // preparing data for spec test
        switch ($this->getName()) {

            case "testDeletePictureExisting":

                $myConfig = $this->getConfig();

                // setup-> create a copy of a picture and delete this one for successful test
                $sCloneTestPicFile = "CC1672_th.jpg";
                $sCloneTestIconFile = "CC1672_th_ico.jpg";

                $sDir = $myConfig->getPictureDir(false) . "/master/product/thumb/";

                @unlink($sDir . $sCloneTestPicFile);
                @unlink($sDir . $sCloneTestIconFile);

                break;
        }
        parent::tearDown();
    }

    public function testResizeImage()
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

        // resizing according to height param
        $this->assertTrue($this->_resizeImageTest($sTestImageFilePNG, $sTestImageFileResizedPNG, 21, 10));
    }

    protected function _resizeImageTest($sTestImageFile, $sTestImageFileResized, $iWidth = 100, $iHeight = 48)
    {
        $sDir = __DIR__ ."/../testData/misc" . DIRECTORY_SEPARATOR;
        if (!file_exists($sDir . $sTestImageFile)) {
            $sMsg = "Failed to find the image file: " . $sDir . $sTestImageFile;
            $this->fail($sMsg);
        }
        //actual test
        if (!(\OxidEsales\Eshop\Core\Registry::getUtilsPic()->resizeImage($sDir . $sTestImageFile, $sDir . $sTestImageFileResized, $iWidth, $iHeight))) {
            $this->fail("Failed to call resizeImage()");
        }

        if (!is_file($sDir . $sTestImageFileResized)) {
            $this->fail("Failed to find the resized image file.");
        }

        $aImageSizeResized = getImageSize($sDir . $sTestImageFileResized);
        $iImageResizedWidth = $aImageSizeResized[0];
        $iImageResizedHeight = $aImageSizeResized[1];
        if (($iImageResizedWidth == $iWidth) && ($iImageResizedHeight == $iHeight)) {
            //echo "Width: $iImageResizedWidth - Height: $iImageResizedHeight";
            unlink($sDir . $sTestImageFileResized);

            return true;
        }
        unlink($sDir . $sTestImageFileResized);

        return false;
    }

    /**
     * Testing image deletion code
     */
    // no deletion in demoshop is allowed
    public function testDeletePictureDemoshop()
    {
        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        $oConfig = $this->getMock('oxconfig');

        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('getConfig'));
        $oUtilsPic->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertFalse($oUtilsPic->UNITdeletePicture('xxx', 'yyy'));
    }

    // blank (nopic) images are not allowed to be deleted
    public function testDeletePictureBlankImages()
    {
        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        //$oConfig->expects( $this->exactly( 2 ) )->method( 'hasModule')->will( $this->returnValue( false ) );
        $oConfig = $this->getMock('oxconfig');

        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('getConfig'));
        $oUtilsPic->expects($this->exactly(2))->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertFalse($oUtilsPic->UNITdeletePicture('nopic.jpg', 'yyy'));
        $this->assertFalse($oUtilsPic->UNITdeletePicture('nopic_ico.jpg', 'yyy'));
    }

    // deleting non existing
    public function testDeletePictureNonExisting()
    {
        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        //$oConfig->expects( $this->once() )->method( 'hasModule')->will( $this->returnValue( false ) );
        $oConfig = $this->getMock('oxconfig');

        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('getConfig'));
        $oUtilsPic->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertFalse($oUtilsPic->UNITdeletePicture(time(), 'yyy'));
    }

    // deleting existing
    public function testDeletePictureExisting()
    {
        $oUtilsPic = oxNew('oxutilspic');
        $this->assertTrue($oUtilsPic->UNITdeletePicture('CCdetail1_z3_ico_th.jpg', $this->getConfig()->getPictureDir(false) . "master/product/thumb/"));
    }

    /**
     * Data provider for testIsPicDeletable.
     *
     * @return array
     */
    public function testIsPicDeletableDataProvider()
    {
        return array(
            array('testOK.jpg', 1, true),
            array('testFail.jpg', 2, false),
        );
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
        $utilsPicMock = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('fetchIsImageDeletable'));
        $utilsPicMock->method('fetchIsImageDeletable')->willReturn($response);

        $this->assertEquals($expectedResult, $utilsPicMock->UNITisPicDeletable($filename, 'test', 'file'));
    }

    /**
     * Test IsPicDeletable with nopic.jpg case.
     */
    public function testIsPicDeletableNoPic()
    {
        $utilsPicMock = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('fetchIsImageDeletable'));
        $utilsPicMock->expects($this->never())->method('fetchIsImageDeletable');

        $this->assertEquals(false, $utilsPicMock->UNITisPicDeletable('nopic.jpg', 'test', 'file'));
    }

    /**
     * Testing OverwritePic logics
     */
    // bad input
    public function testOverwritePicBadInput()
    {
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('safePictureDelete'));
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        $blFalse = $oUtilsPic->overwritePic(new stdClass(), 'xxx', 'xxx', '', '', '', '');
        $this->assertFalse($blFalse);
    }

    // params are not ok
    public function testOverwritePicBadParams()
    {
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('safePictureDelete'));
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        $oObject = new stdClass();
        $oObject->oxtbl__oxpic = new oxField('yyy', oxField::T_RAW);

        $blFalse = $oUtilsPic->overwritePic($oObject, 'oxtbl', 'oxpic', '', '', array('oxtbl__oxpic' => 'yyy'), '');
        $this->assertFalse($blFalse);
    }

    // all is fine
    public function testOverwritePicGoodParams()
    {
        $oFiles = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array('getImageDirByType'));
        $oFiles->expects($this->atLeastOnce())->method('getImageDirByType')->will($this->returnValue('/test_image_dir/'));
        oxTestModules::addModuleObject('oxUtilsFile', $oFiles);

        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('safePictureDelete'));
        $oUtilsPic->expects($this->once())->method('safePictureDelete')->with($this->equalTo('yyy'), $this->equalTo('yyy/test_image_dir/'), $this->equalTo('oxtbl'), $this->equalTo('oxpic'))->will($this->returnValue(true));

        $oObject = new stdClass();
        $oObject->oxtbl__oxpic = new oxField('yyy', oxField::T_RAW);

        $blTrue = $oUtilsPic->overwritePic($oObject, 'oxtbl', 'oxpic', 'TEST_TYPE', '', array('oxtbl__oxpic' => 'xxx'), 'yyy');
        $this->assertTrue($blTrue);
    }

    // Test if corect path to file is generated (M:1268)
    public function testOverwritePic_generatesCorectFilePath()
    {
        $oFiles = $this->getMock(\OxidEsales\Eshop\Core\UtilsFile::class, array('getImageDirByType'));
        $oFiles->expects($this->atLeastOnce())->method('getImageDirByType')->will($this->returnValue('/testType_dir/'));
        oxTestModules::addModuleObject('oxUtilsFile', $oFiles);

        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('safePictureDelete'));
        $oUtilsPic->expects($this->once())->method('safePictureDelete')->with($this->equalTo('testPictureName'), $this->equalTo('testAbsPath/testType_dir/'));

        $oObject = new stdclass();
        $oObject->oxtbl__oxpic = new oxField('testPictureName', oxField::T_RAW);

        $blTrue = $oUtilsPic->overwritePic($oObject, 'oxtbl', 'oxpic', 'testType', 'testPath', array('oxtbl__oxpic' => 'xxx'), 'testAbsPath');
    }

    /**
     * Testing safe deletion code
     */
    // deeper code must not allow deletion
    public function testSafePictureDeleteMustFailDeletion()
    {
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('_isPicDeletable', '_deletePicture'));
        $oUtilsPic->expects($this->once())->method('_isPicDeletable')->will($this->returnValue(false));
        $oUtilsPic->expects($this->never())->method('_deletePicture');

        $this->assertFalse($oUtilsPic->safePictureDelete('', '', '', ''));
    }

    //
    public function testSafePictureDeleteMustSucceed()
    {
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, array('_isPicDeletable', '_deletePicture'));
        $oUtilsPic->expects($this->once())->method('_isPicDeletable')->will($this->returnValue(true));
        $oUtilsPic->expects($this->once())->method('_deletePicture')->will($this->returnValue(true));

        $this->assertTrue($oUtilsPic->safePictureDelete('', '', '', ''));
    }

    public function testResizeGif()
    {
        $sTestImageFileGIF = "test.gif";
        $sTestImageFileResizedGIF = "test_resized_ResizeGIF.gif";

        // actual test
        $this->assertTrue($this->_resizeGIFTest($sTestImageFileGIF, $sTestImageFileResizedGIF));

        // checking if works with "gd 1"
        $this->assertTrue($this->_resizeGIFTest($sTestImageFileGIF, $sTestImageFileResizedGIF, 1));
    }

    protected function _resizeGIFTest($sTestImageFile, $sTestImageFileResized, $gdver = 2)
    {
        $myUtils = oxNew('oxUtilsPic');
        $sDir = __DIR__ ."/../testData/misc" . DIRECTORY_SEPARATOR;
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
        if (!($myUtils->UNITresizeGif($sDir . $sTestImageFile, $sDir . $sTestImageFileResized, $iWidth, $iHeight, $iImageOriginalWidth, $iImageOriginalHeight, $gdver, false))) {
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
}
