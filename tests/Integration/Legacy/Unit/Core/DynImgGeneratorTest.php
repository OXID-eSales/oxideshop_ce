<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDynImgGenerator;
use OxidEsales\EshopCommunity\Core\DynamicImageGenerator;
use OxidEsales\EshopCommunity\Core\Exception\StandardException;

final class DynImgGeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function testGetInstance(): void
    {
        $this->assertInstanceOf('oxDynImgGenerator', \OxidEsales\EshopCommunity\Core\DynamicImageGenerator::getInstance());
    }

    public function testGetImageUri(): void
    {
        $oGen = oxNew('oxDynImgGenerator');
        $this->assertEquals($_SERVER["REQUEST_URI"] ?? "", $oGen->getImageUri());
    }

    public function testGetImageUriWithDoubleSlash(): void
    {
        $sRequestedImageUri = "/out/pictures//generated/path/to/test.jpg";
        $sExpectedUri = "out/pictures/generated/path/to/test.jpg";

        $sRequestUri = $_SERVER['REQUEST_URI'] ?? null;
        $_SERVER['REQUEST_URI'] = $sRequestedImageUri;

        $oGen = new oxDynImgGenerator();
        $this->assertSame($sExpectedUri, $oGen->getImageUri());

        $_SERVER['REQUEST_URI'] = $sRequestUri;
    }

    public function testGetImageName(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageUri"]);
        $oGen
            ->method('getImageUri')
            ->willReturnOnConsecutiveCalls('/test1/test2/test3/test4/test.jpg', '');

        $this->assertSame("test.jpg", $oGen->getImageName());
        $this->assertSame("", $oGen->getImageName());
    }

    public function testGetImageMasterPath(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageUri"]);
        $oGen
            ->method('getImageUri')
            ->willReturnOnConsecutiveCalls('', '/test1/test2/test3/test4/test.jpg');

        $this->assertFalse($oGen->getImageMasterPath());
        $this->assertSame("/master/test2/test3/", $oGen->getImageMasterPath());
    }

    public function testGetImageInfo(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageUri"]);
        $oGen
            ->method('getImageUri')
            ->willReturnOnConsecutiveCalls(
                '',
                '/test1/test2/test3/test4/test.jpg',
                '/test1/test2/test3/12_12_12/test.jpg'
            );

        $this->assertSame([0 ,0, 0], $oGen->getImageInfo());
        $this->assertSame(["test4"], $oGen->getImageInfo());
        $this->assertSame(["12", "12", "12"], $oGen->getImageInfo());
    }

    public function testGetImageTarget(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageUri"]);
        $oGen->method('getImageUri')->willReturn("/test1/test2/test3/12_12_12/test.jpg");

        $this->assertSame(getShopBasePath() . "/test1/test2/test3/12_12_12/test.jpg", $oGen->getImageTarget());
    }

    /**
     * @dataProvider getNopicImageTargetDataProvider
     */
    public function testGetNopicImageTarget(string $filename, bool $convertToWebP): void
    {
        $this->setConfigParam('blConvertImagesToWebP', $convertToWebP);

        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageUri", "getImageName"]);
        $oGen->method('getImageUri')->willReturn("/test1/test2/test3/12_12_12/test.jpg");
        $oGen->method('getImageName')->willReturn("test.jpg");

        $this->assertSame(getShopBasePath() . ('/test1/test2/test3/12_12_12/' . $filename), $oGen->getNopicImageTarget());
    }

    public function getNopicImageTargetDataProvider(): \Iterator
    {
        yield ['nopic.jpg', false];
        yield ['nopic.webp', true];
    }

    public function testIsTargetPathValid(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["isValidPath", "createFolders"]);
        $oGen->expects($this->once())->method('isValidPath')->with("/test1/test2/test3/12_12_12")->willReturn(false);
        $oGen->expects($this->never())->method('createFolders');

        // invalid path
        $this->assertFalse($oGen->isTargetPathValid("/test1/test2/test3/12_12_12/nopic.jpg"));

        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["isValidPath", "createFolders"]);
        $oGen->expects($this->once())->method('isValidPath')->with("/test1/test2/test3/12_12_12")->willReturn(true);
        $oGen->expects($this->once())->method('createFolders')->with("/test1/test2/test3/12_12_12")->willReturn(true);

        // invalid path
        $this->assertTrue($oGen->isTargetPathValid("/test1/test2/test3/12_12_12/nopic.jpg"));
    }

    public function testIsValidPath(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageInfo"]);
        $oGen
            ->method('getImageInfo')
            ->willReturnOnConsecutiveCalls(
                false,
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 75],
                [56, 42, 75]
            );

        // missing image info
        $this->assertFalse($oGen->isValidPath("any/path"));

        // wrong path
        $this->assertFalse($oGen->isValidPath("wrong/path"));

        // wrong quality param
        $this->assertFalse($oGen->isValidPath("/wrong/quality/param/generated/product/icon/4_5_6"));

        // wrogn size param
        $this->assertFalse($oGen->isValidPath("/wrong/size/param/generated/product/icon/7_8_75"));

        // all parameters are fine
        $this->assertTrue($oGen->isValidPath("/all/params/fine/generated/product/icon/56_42_75"));
    }

    public function testGetImageType(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageName"]);
        $oGen
            ->method('getImageName')
            ->willReturnOnConsecutiveCalls(
                'test.jpg',
                'test.jpeg',
                'test.png',
                'test.gif',
                'test',
                'test.php',
                'test.exe'
            );

        $this->assertSame("jpeg", $oGen->getImageType());
        $this->assertSame("jpeg", $oGen->getImageType());
        $this->assertSame("png", $oGen->getImageType());
        $this->assertSame("gif", $oGen->getImageType());
        $this->assertFalse($oGen->getImageType());
        $this->assertFalse($oGen->getImageType());
        $this->assertFalse($oGen->getImageType());
    }

    /**
     * @dataProvider dataProviderTestGenerateImagePickGenerationMethodFromFileExtension
     *
     * @param $sourceFilePath
     * @param $targetFilePath
     * @param $expectedGenerationMethod
     */
    public function testGenerateImagePickGenerationMethodFromFileExtension(
        $sourceFilePath,
        $targetFilePath,
        $expectedGenerationMethod
    ) {
        $oGen = $this->getMock(DynamicImageGenerator::class, [
            'getImageInfo',
            'validateGdVersion',
            'validateFileExist',
            'isTargetPathValid',
            'validateImageFileExtension',
            'generateJpg',
            'generatePng',
            'generateGif',
        ]);
        $oGen->method('getImageInfo')->willReturn([100, 100, 75]);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(true);
        $oGen->method('validateImageFileExtension')->willReturn(true);
        $oGen->expects($this->once())->method($expectedGenerationMethod)->willReturn($targetFilePath);

        $oGen->generateImage($sourceFilePath, $targetFilePath);
    }

    public function testGenerateImageThrowsException(): void
    {
        $sourceFilePath = 'source.jpg';
        $targetFilePath = 'target.jpg';
        $expectedException = StandardException::class;
        $expectedExceptionMessage = 'imageTarget path and generatedImage path differ';

        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $oGen = $this->getMock(
            DynamicImageGenerator::class,
            [
             'getImageInfo',
            'validateGdVersion',
            'validateFileExist',
            'isTargetPathValid',
            'validateImageFileExtension',
            'generateJpg',
            'generatePng',
            'generateGif',
            ]
        );
        $oGen->method('getImageInfo')->willReturn([100, 100, 75]);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(true);
        $oGen->method('validateImageFileExtension')->willReturn(true);
        $oGen->expects($this->once())->method('generateJpg')->willReturn('NOT_' . $targetFilePath);

        $oGen->generateImage($sourceFilePath, $targetFilePath);
    }

    public function dataProviderTestGenerateImagePickGenerationMethodFromFileExtension(): \Iterator
    {
        yield ['sourceFile.jpeg', 'targetFile.jpeg', 'generateJpg'];
        yield ['sourceFile.jpg', 'targetFile.jpg', 'generateJpg'];
        yield ['sourceFile.png', 'targetFile.png', 'generatePng'];
        yield ['sourceFile.gif', 'targetFile.gif', 'generateGif'];
        // Test for case insensitivity
        yield ['sourceFile.JPEG', 'targetFile.jpeg', 'generateJpg'];
        yield ['sourceFile.JPG', 'targetFile.jpg', 'generateJpg'];
        yield ['sourceFile.PNG', 'targetFile.png', 'generatePng'];
        yield ['sourceFile.GIF', 'targetFile.gif', 'generateGif'];
    }

    public function testGenerateImageGdVersionValidation(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion']);
        $oGen->method('validateGdVersion')->willReturn(false);

        $this->assertFalse($oGen->generateImage('source.jpg', 'target.jpg'));
    }

    public function testGenerateImageSourceFileExistValidation(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist']);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(false);

        $this->assertFalse($oGen->generateImage('source.jpg', 'target.jpg'));
    }

    public function testGenerateImageTargetPathValidation(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', 'isTargetPathValid']);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(false);

        $this->assertFalse($oGen->generateImage('source.jpg', 'target.jpg'));
    }

    public function testGenerateImageFileExtensionValidationSource(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', 'isTargetPathValid', 'validateImageFileExtension']);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(true);
        $oGen->method('validateImageFileExtension')->with('sourcejpg')->willReturn(false);

        $this->assertFalse($oGen->generateImage('source.sourcejpg', 'target.jpg'));
    }

    public function testGenerateImageFileExtensionValidationTarget(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', 'isTargetPathValid', 'validateImageFileExtension']);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(true);
        $oGen
            ->method('validateImageFileExtension')
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $this->assertFalse($oGen->generateImage('source.sourcejpg', 'target.targetjpg'));
    }

    public function testGenerateImageFileSourceAndTargetExtensionEqualityValidation(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', 'isTargetPathValid', 'validateImageFileExtension']);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(true);
        $oGen
            ->method('validateImageFileExtension')
            ->willReturnOnConsecutiveCalls(
                true,
                true
            );

        $this->assertFalse($oGen->generateImage('source.jpg', 'target.png'));
    }

    public function testGenerateImageTargetFileExistsValidation(): void
    {
        $oGen = $this->getMock(DynamicImageGenerator::class, ['validateGdVersion', 'validateFileExist', 'isTargetPathValid', 'validateImageFileExtension', 'getImageDimensions', 'getImageInfo', 'generateJpg']);
        $oGen->method('validateGdVersion')->willReturn(true);
        $oGen->method('validateFileExist')->willReturn(true);
        $oGen->method('isTargetPathValid')->willReturn(true);
        $oGen->method('validateImageFileExtension')->willReturn(true);
        $oGen->method('getImageDimensions')->willReturn([100, 100]);
        $oGen->method('getImageInfo')->willReturn([100, 100, 75]);

        /** If an image file with the same dimensions already exist do regenerate it. I.e. never call _generateJpg' */
        $oGen->expects($this->never())->method('generateJpg');
        $this->assertSame("target.jpg", $oGen->generateImage('source.jpg', 'target.jpg'));
    }

    public function testGetImagePathNopicImageTarget(): void
    {
        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageMasterPath", "getImageName", "getImageTarget", "getNopicImageTarget", "generateImage", "getImageType", "setHeader", "getHeaders"]);

        $oGen->method('getImageMasterPath')->willReturn("/test/");
        $oGen->method('getImageName')->willReturn("test.jpg");
        $oGen->method('getNopicImageTarget')->willReturn("nopicimagetarget");
        $oGen->method('setHeader')->with("HTTP/1.1 404 Not Found");

        $this->assertFalse($oGen->getImagePath());
    }

    public function testGetImagePath(): void
    {
        $sDir = basename((string) $this->getConfig()->getPictureDir(false));

        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageMasterPath", "getImageName", "getImageTarget", "getNopicImageTarget", "generateImage", "getImageType", "setHeader", "getHeaders"]);

        $oGen->method('getImageMasterPath')->willReturn("out/" . $sDir . "/master/");
        $oGen->method('getImageName')->willReturn("nopic.jpg");
        $oGen->method('getImageTarget')->willReturn("best.jpg");
        $oGen->method('generateImage')->willReturn("best.jpg");
        $oGen->method('getImageType')->willReturn("jpg");

        $this->assertSame("best.jpg", $oGen->getImagePath());
    }

    public function testGetImagePathWith404Header(): void
    {
        $sDir = basename((string) $this->getConfig()->getPictureDir(false));

        $oGen = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ["getImageMasterPath", "getImageName", "getImageTarget", "getNopicImageTarget", "generateImage", "getImageType", "setHeader", "getHeaders"]);

        $oGen->method('getImageMasterPath')->willReturn("out/" . $sDir . "/master/product/1/");
        $oGen->method('getImageName')->willReturn("best.jpg");
        $oGen->method('getNopicImageTarget')->willReturn("nopic.jpg");
        $oGen->method('setHeader')->with("HTTP/1.1 404 Not Found");
        $oGen->method('generateImage')->willReturn("best.jpg");
        $oGen->method('getImageType')->willReturn("jpg");

        $this->assertSame("best.jpg", $oGen->getImagePath());
    }
}
