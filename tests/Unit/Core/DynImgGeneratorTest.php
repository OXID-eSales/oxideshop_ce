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

use oxDynImgGenerator;
use OxidEsales\EshopCommunity\Core\DynamicImageGenerator;
use OxidEsales\EshopCommunity\Core\Exception\StandardException;

/**
 * Tests for Actions_List class
 */
class DynImgGeneratorTest extends \OxidTestCase
{

    /**
     * Testing instance getter
     *
     * @return null
     */
    public function testGetInstance()
    {
        $this->assertTrue(oxDynImgGenerator::getInstance() instanceof \OxidEsales\EshopCommunity\Core\DynamicImageGenerator);
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImageUri()
    {
        $oGen = oxNew('oxDynImgGenerator');
        $this->assertEquals(isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "", $oGen->UNITgetImageUri());
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImageName()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageUri"));
        $oGen->expects($this->at(0))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/test4/test.jpg"));
        $oGen->expects($this->at(1))->method('_getImageUri')->will($this->returnValue(""));

        $this->assertEquals("test.jpg", $oGen->UNITgetImageName());
        $this->assertEquals("", $oGen->UNITgetImageName());
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImageMasterPath()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageUri"));
        $oGen->expects($this->at(0))->method('_getImageUri')->will($this->returnValue(""));
        $oGen->expects($this->at(1))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/test4/test.jpg"));

        $this->assertFalse($oGen->UNITgetImageMasterPath());
        $this->assertEquals("/master/test2/test3/", $oGen->UNITgetImageMasterPath());
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImageInfo()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageUri"));
        $oGen->expects($this->at(0))->method('_getImageUri')->will($this->returnValue(""));
        $oGen->expects($this->at(1))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/test4/test.jpg"));
        $oGen->expects($this->at(2))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.jpg"));

        $this->assertEquals(array(), $oGen->UNITgetImageInfo());
        $this->assertEquals(array("test4"), $oGen->UNITgetImageInfo());
        $this->assertEquals(array("12", "12", "12"), $oGen->UNITgetImageInfo());
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImageTarget()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageUri"));
        $oGen->expects($this->at(0))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.jpg"));

        $this->assertEquals(getShopBasePath() . "/test1/test2/test3/12_12_12/test.jpg", $oGen->UNITgetImageTarget());
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetNopicImageTarget()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageUri", "_getImageName"));
        $oGen->expects($this->at(0))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.jpg"));
        $oGen->expects($this->at(1))->method('_getImageName')->will($this->returnValue("test.jpg"));

        $this->assertEquals(getShopBasePath() . "/test1/test2/test3/12_12_12/nopic.jpg", $oGen->UNITgetNopicImageTarget());
    }

    /**
     * Testing path checker
     *
     * @return null
     */
    public function testIsTargetPathValid()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_isValidPath", "_createFolders"));
        $oGen->expects($this->once())->method('_isValidPath')->with($this->equalTo("/test1/test2/test3/12_12_12"))->will($this->returnValue(false));
        $oGen->expects($this->never())->method('_createFolders');

        // invalid path
        $this->assertFalse($oGen->UNITisTargetPathValid("/test1/test2/test3/12_12_12/nopic.jpg"));

        $oGen = $this->getMock("oxDynImgGenerator", array("_isValidPath", "_createFolders"));
        $oGen->expects($this->once())->method('_isValidPath')->with($this->equalTo("/test1/test2/test3/12_12_12"))->will($this->returnValue(true));
        $oGen->expects($this->once())->method('_createFolders')->with($this->equalTo("/test1/test2/test3/12_12_12"))->will($this->returnValue(true));

        // invalid path
        $this->assertTrue($oGen->UNITisTargetPathValid("/test1/test2/test3/12_12_12/nopic.jpg"));
    }

    /**
     * Testing path validator
     *
     * @return null
     */
    public function testIsValidPath()
    {
        $i = 0;
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageInfo"));
        $oGen->expects($this->at($i++))->method('_getImageInfo')->will($this->returnValue(false));
        $oGen->expects($this->at($i++))->method('_getImageInfo')->will($this->returnValue(array(1, 2, 3)));
        $oGen->expects($this->at($i++))->method('_getImageInfo')->will($this->returnValue(array(4, 5, 6)));
        $oGen->expects($this->at($i++))->method('_getImageInfo')->will($this->returnValue(array(7, 8, 75)));
        $oGen->expects($this->at($i++))->method('_getImageInfo')->will($this->returnValue(array(87, 87, 75)));

        // missing image info
        $this->assertFalse($oGen->UNITisValidPath("any/path"));

        // wrong path
        $this->assertFalse($oGen->UNITisValidPath("wrong/path"));

        // wrong quality param
        $this->assertFalse($oGen->UNITisValidPath("/wrong/quality/param/generated/product/icon/4_5_6"));

        // wrogn size param
        $this->assertFalse($oGen->UNITisValidPath("/wrong/size/param/generated/product/icon/7_8_75"));

        // all parameters are fine
        $this->assertTrue($oGen->UNITisValidPath("/all/params/fine/generated/product/icon/87_87_75"));
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImageType()
    {
        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageName"));
        $oGen->expects($this->at(0))->method('_getImageName')->will($this->returnValue("test.jpg"));
        $oGen->expects($this->at(1))->method('_getImageName')->will($this->returnValue("test.jpeg"));
        $oGen->expects($this->at(2))->method('_getImageName')->will($this->returnValue("test.png"));
        $oGen->expects($this->at(3))->method('_getImageName')->will($this->returnValue("test.gif"));
        $oGen->expects($this->at(4))->method('_getImageName')->will($this->returnValue("test"));
        $oGen->expects($this->at(5))->method('_getImageName')->will($this->returnValue("test.php"));
        $oGen->expects($this->at(6))->method('_getImageName')->will($this->returnValue("test.exe"));

        $this->assertEquals("jpeg", $oGen->UNITgetImageType());
        $this->assertEquals("jpeg", $oGen->UNITgetImageType());
        $this->assertEquals("png", $oGen->UNITgetImageType());
        $this->assertEquals("gif", $oGen->UNITgetImageType());
        $this->assertFalse($oGen->UNITgetImageType());
        $this->assertFalse($oGen->UNITgetImageType());
        $this->assertFalse($oGen->UNITgetImageType());
    }

    /**
     * @dataProvider dataProviderTestGenerateImagePickGenerationMethodFromFileExtension
     *
     * @param $sourceFilePath
     * @param $targetFilePath
     * @param $expectedGenerationMethod
     * @param $message
     */
    public function testGenerateImagePickGenerationMethodFromFileExtension($sourceFilePath, $targetFilePath, $expectedGenerationMethod, $message)
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, [
            '_getImageInfo',
            'validateGdVersion',
            'validateFileExist',
            '_isTargetPathValid',
            'validateImageFileExtension',
            '_generateJpg',
            '_generatePng',
            '_generateGif',
        ]);
        $oGen->expects($this->any())->method('_getImageInfo')->will($this->returnValue(array(100,100,75)));
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateImageFileExtension')->will($this->returnValue(true));
        $oGen->expects($this->once())->method($expectedGenerationMethod)->will($this->returnValue($targetFilePath));

        $oGen->UNITgenerateImage($sourceFilePath, $targetFilePath);
    }

    public function testGenerateImageThrowsException()
    {
        $sourceFilePath = 'source.jpg';
        $targetFilePath = 'target.jpg';
        $expectedException = StandardException::class;
        $expectedExceptionMessage = 'imageTarget path and generatedImage path differ';

        $this->setExpectedException($expectedException, $expectedExceptionMessage);

        $oGen = $this->getMock(
            DynamicImageGenerator::class,
            [
             '_getImageInfo',
            'validateGdVersion',
            'validateFileExist',
            '_isTargetPathValid',
            'validateImageFileExtension',
            '_generateJpg',
            '_generatePng',
            '_generateGif',
        ]);
        $oGen->expects($this->any())->method('_getImageInfo')->will($this->returnValue(array(100,100,75)));
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateImageFileExtension')->will($this->returnValue(true));
        $oGen->expects($this->once())->method('_generateJpg')->will($this->returnValue('NOT_' . $targetFilePath));

        $oGen->UNITgenerateImage($sourceFilePath, $targetFilePath);
    }

    public function dataProviderTestGenerateImagePickGenerationMethodFromFileExtension() {
        return [
            ['sourceFile.jpeg', 'targetFile.jpeg', '_generateJpg', 'For files with the extension jpeg the _generateJpg method is called'],
            ['sourceFile.jpg', 'targetFile.jpg', '_generateJpg', 'For files with the extension jpg _generateJpg method is called'],
            ['sourceFile.png', 'targetFile.png', '_generatePng', 'For files with the extension png _generatePng method is called'],
            ['sourceFile.gif', 'targetFile.gif', '_generateGif', 'For files with the extension gif _generateGif method is called'],
            // Test for case insensitivity
            ['sourceFile.JPEG', 'targetFile.jpeg', '_generateJpg', 'For files with the extension JPEG the _generateJpg method is called'],
            ['sourceFile.JPG', 'targetFile.jpg', '_generateJpg', 'For files with the extension JPG _generateJpg method is called'],
            ['sourceFile.PNG', 'targetFile.png', '_generatePng', 'For files with the extension PNG _generatePng method is called'],
            ['sourceFile.GIF', 'targetFile.gif', '_generateGif', 'For files with the extension GIF _generateGif method is called'],
        ];
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function __testGenerateImageFromSource()
    {
        $oGen = $this->getMock("oxDynImgGenerator", [
            '_getImageType',
            '_generatePng',
            '_generateJpg',
            '_generateGif',
            '_getImageUri',
            'validateGdVersion',
            'validateFileExist',
            '_isTargetPathValid',
            'validateImageFileExtension'
        ]);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateImageFileExtension')->with('jpg')->will($this->returnValue(true));

        $oGen->expects($this->at(0))->method('_getImageType')->will($this->returnValue("png"));
        $oGen->expects($this->at(1))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.png"));
        $oGen->expects($this->at(2))->method('_generatePng')->with($this->equalTo("source"), $this->equalTo("target"), $this->equalTo("12"), $this->equalTo("12"))->will($this->returnValue("test.png"));

        $oGen->expects($this->at(3))->method('_getImageType')->will($this->returnValue("jpeg"));
        $oGen->expects($this->at(4))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.jpg"));
        $oGen->expects($this->at(5))->method('_generateJpg')->with($this->equalTo("source"), $this->equalTo("target"), $this->equalTo("12"), $this->equalTo("12"))->will($this->returnValue("test.jpg"));

        $oGen->expects($this->at(6))->method('_getImageType')->will($this->returnValue("jpeg"));
        $oGen->expects($this->at(7))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.jpeg"));
        $oGen->expects($this->at(8))->method('_generateJpg')->with($this->equalTo("source"), $this->equalTo("target"), $this->equalTo("12"), $this->equalTo("12"))->will($this->returnValue("test.jpg"));

        $oGen->expects($this->at(9))->method('_getImageType')->will($this->returnValue("gif"));
        $oGen->expects($this->at(10))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/test.gif"));
        $oGen->expects($this->at(11))->method('_generateGif')->with($this->equalTo("source"), $this->equalTo("target"), $this->equalTo("12"), $this->equalTo("12"))->will($this->returnValue("test.gif"));

        $oGen->expects($this->at(12))->method('_getImageType')->will($this->returnValue("unknown"));
        $oGen->expects($this->at(13))->method('_getImageUri')->will($this->returnValue("/test1/test2/test3/12_12_12/unknown"));

        $this->assertEquals("test.png", $oGen->UNITgenerateImage("source.jpg", "target.jpg"));
        $this->assertEquals("test.jpg", $oGen->UNITgenerateImage("source.jpg", "target.jpg"));
        $this->assertEquals("test.jpg", $oGen->UNITgenerateImage("source.jpg", "target.jpg"));
        $this->assertEquals("test.gif", $oGen->UNITgenerateImage("source.jpg", "target.jpg"));
        $this->assertFalse($oGen->UNITgenerateImage("source.jpg", "target.jpg"));
    }

    public function testGenerateImageGdVersionValidation() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(false));

        $this->assertFalse($oGen->UNITgenerateImage('source.jpg', 'target.jpg'));
    }

    public function testGenerateImageSourceFileExistValidation() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(false));

        $this->assertFalse($oGen->UNITgenerateImage('source.jpg', 'target.jpg'));
    }

    public function testGenerateImageTargetPathValidation() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', '_isTargetPathValid']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(false));

        $this->assertFalse($oGen->UNITgenerateImage('source.jpg', 'target.jpg'));
    }

    public function testGenerateImageFileExtensionValidationSource() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', '_isTargetPathValid', 'validateImageFileExtension']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateImageFileExtension')->with('sourcejpg')->will($this->returnValue(false));

        $this->assertFalse($oGen->UNITgenerateImage('source.sourcejpg', 'target.jpg'));
    }

    public function testGenerateImageFileExtensionValidationTarget() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', '_isTargetPathValid', 'validateImageFileExtension']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        // Validate the source image
        $oGen->expects($this->at(0))->method('validateImageFileExtension')->will($this->returnValue(true));
        // Validate the target image
        $oGen->expects($this->at(1))->method('validateImageFileExtension')->will($this->returnValue(false));

        $this->assertFalse($oGen->UNITgenerateImage('source.sourcejpg', 'target.targetjpg'));
    }

    public function testGenerateImageFileSourceAndTargetExtensionEqualityValidation() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', '_isTargetPathValid', 'validateImageFileExtension']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        $oGen->expects($this->at(0))->method('validateImageFileExtension')->will($this->returnValue(true));
        $oGen->expects($this->at(1))->method('validateImageFileExtension')->will($this->returnValue(true));

        $this->assertFalse($oGen->UNITgenerateImage('source.jpg', 'target.png'));
    }

    public function testGenerateImageTargetFileExistsValidation() {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', '_isTargetPathValid', 'validateImageFileExtension', 'getImageDimensions', '_getImageInfo', '_generateJpg']);
        $oGen->expects($this->any())->method('validateGdVersion')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateFileExist')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('_isTargetPathValid')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('validateImageFileExtension')->will($this->returnValue(true));
        $oGen->expects($this->any())->method('getImageDimensions')->will($this->returnValue(array(100,100)));
        $oGen->expects($this->any())->method('_getImageInfo')->will($this->returnValue(array(100,100,75)));

        /** If an image file with the same dimensions already exist do regenerate it. I.e. never call _generateJpg' */
        $oGen->expects($this->never())->method('_generateJpg');
        $this->assertSame("target.jpg", $oGen->UNITgenerateImage('source.jpg', 'target.jpg'));
    }

    /**
     * Testing image uri getter
     *
     * @return null
     */
    public function testGetImagePath()
    {
        $sDir = basename($this->getConfig()->getPictureDir(false));
        $i = 0;

        $oGen = $this->getMock("oxDynImgGenerator", array("_getImageMasterPath", "_getImageName", "_getImageTarget", "_getNopicImageTarget", "_generateImage", "_getImageType", "_setHeader", "_getHeaders"));

        $oGen->expects($this->at($i++))->method('_getImageMasterPath')->will($this->returnValue("/test/"));
        $oGen->expects($this->at($i++))->method('_getImageName')->will($this->returnValue("test.jpg"));
        $oGen->expects($this->at($i++))->method('_getNopicImageTarget')->will($this->returnValue("nopicimagetarget"));
        $oGen->expects($this->at($i++))->method('_setHeader')->with($this->equalTo("HTTP/1.1 404 Not Found"));
        $oGen->expects($this->at($i++))->method('_setHeader')->with($this->equalTo("HTTP/1.1 404 Not Found"));

        $oGen->expects($this->at($i++))->method('_getImageMasterPath')->will($this->returnValue("out/" . $sDir . "/master/"));
        $oGen->expects($this->at($i++))->method('_getImageName')->will($this->returnValue("nopic.jpg"));
        $oGen->expects($this->at($i++))->method('_getImageTarget')->will($this->returnValue("best.jpg"));
        $oGen->expects($this->at($i++))->method('_generateImage')->will($this->returnValue("best.jpg"));
        $oGen->expects($this->at($i++))->method('_getImageType')->will($this->returnValue("jpg"));

        $oGen->expects($this->at($i++))->method('_getImageMasterPath')->will($this->returnValue("out/" . $sDir . "/master/product/1/"));
        $oGen->expects($this->at($i++))->method('_getImageName')->will($this->returnValue("best.jpg"));
        $oGen->expects($this->at($i++))->method('_getNopicImageTarget')->will($this->returnValue("nopic.jpg"));
        $oGen->expects($this->at($i++))->method('_setHeader')->with($this->equalTo("HTTP/1.1 404 Not Found"));
        $oGen->expects($this->at($i++))->method('_generateImage')->will($this->returnValue("best.jpg"));
        $oGen->expects($this->at($i++))->method('_getImageType')->will($this->returnValue("jpg"));

        $this->assertFalse($oGen->getImagePath());
        $this->assertEquals("best.jpg", $oGen->getImagePath());
        $this->assertEquals("best.jpg", $oGen->getImagePath());
    }
}
