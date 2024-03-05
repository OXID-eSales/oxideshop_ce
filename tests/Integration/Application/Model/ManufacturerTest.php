<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Application\Model;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use Generator;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

#[Group('manufacturer')]
final class ManufacturerTest extends IntegrationTestCase
{
    private string $oxid = 'id1';
    private Manufacturer $manufacturer;
    private Filesystem $filesystem;

    public function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
    }

    #[DataProvider('provideImageTypeData')]
    public function testItShouldReturnTheCorrectImageType(?string $expected, string $fieldName): void
    {
        $manufacturer = oxNew(Manufacturer::class);

        $this->assertSame($expected, $manufacturer->getImageType($fieldName));
    }

    public static function provideImageTypeData(): Generator
    {
        yield 'MICO' => ['MICO', 'oxicon'];
        yield 'MICO for alt' => ['MICO', 'oxicon_alt'];
        yield 'MPIC' => ['MPIC', 'oxpicture'];
        yield 'MTHU' => ['MTHU', 'oxthumbnail'];
        yield 'Null' => [null, 'none_field'];
    }

    #[DataProvider('provideImageFileData')]
    public function testDeleteShouldRemoveTheGeneratedImage(string $imageFieldName, string $imageFileName, string $imageType): void
    {
        $imagePath = $this->createImage(
            Path::join($this->getImagePath($imageType, true), 'x_y_z'),
            $imageFileName
        );
        $this->setupManufacturer($imageFieldName, $imageFileName);

        $this->manufacturer->delete($this->oxid);

        $this->assertFileDoesNotExist($imagePath);
    }

    #[DataProvider('provideImageFileData')]
    public function testDeleteShouldRemoveTheMasterImage(string $imageFieldName, string $imageFileName, string $imageType): void
    {
        $imagePath = $this->createImage(
            $this->getImagePath($imageType),
            $imageFileName
        );
        $this->setupManufacturer($imageFieldName, $imageFileName);

        $this->manufacturer->delete($this->oxid);

        $this->assertFileDoesNotExist($imagePath);
    }

    public static function provideImageFileData(): Generator
    {
        yield 'Icon should be deleted from filesystem' => ['oxicon', 'test-icon.jpg', 'MICO',];
        yield 'Icon Alt should be deleted from filesystem' => ['oxicon_alt', 'test-icon-alt.jpg', 'MICO',];
        yield 'Picture should be deleted from filesystem' => ['oxpicture', 'test-picture.jpg', 'MPIC',];
        yield 'Thumbnail should be deleted from filesystem' => ['oxthumbnail', 'test-thumbnail.jpg', 'MTHU',];
        yield 'Promotion Icon should be deleted from filesystem' => ['oxpromotion_icon', 'test-promotion-icon.jpg', 'MPICO',];
    }

    public function testIconUrlShouldBeEndedWithTheGeneratedImgPath(): void
    {
        $this->setupManufacturer('oxicon', 'test-icon.png');
        $this->overwriteConfig('sManufacturerIconsize', '80*90');
        $sizeDirectory = '80_90_75';
        [$masterImage, $generatedImage] = $this->createImages('MICO', 'test-icon.png', $sizeDirectory);

        $this->assertStringEndsWith(
            $this->getImagePathFromSource('MICO', $sizeDirectory, 'test-icon.png'),
            $this->manufacturer->getIconUrl()
        );

        $this->filesystem->remove($masterImage);
        $this->filesystem->remove($generatedImage);
    }

    public function testAltIconUrlShouldBeEndedWithTheGeneratedImgPath(): void
    {
        $this->setupManufacturer('oxicon_alt', 'test-icon-alt.png');
        $this->overwriteConfig('sManufacturerIconsize', '80*90');
        $sizeDirectory = '80_90_75';
        [$masterImage, $generatedImage] = $this->createImages('MICO', 'test-icon-alt.png', $sizeDirectory);

        $this->assertStringEndsWith(
            $this->getImagePathFromSource('MICO', $sizeDirectory, 'test-icon-alt.png'),
            $this->manufacturer->getIconAltUrl()
        );

        $this->filesystem->remove($masterImage);
        $this->filesystem->remove($generatedImage);
    }

    public function testPictureUrlShouldBeEndedWithTheGeneratedImgPath(): void
    {
        $this->setupManufacturer('oxpicture', 'test-picture.png');
        $this->overwriteConfig('sManufacturerPicturesize', '80*90');
        $sizeDirectory = '80_90_75';
        [$masterImage, $generatedImage] = $this->createImages('MPIC', 'test-picture.png', $sizeDirectory);

        $this->assertStringEndsWith(
            $this->getImagePathFromSource('MPIC', $sizeDirectory, 'test-picture.png'),
            $this->manufacturer->getPictureUrl()
        );

        $this->filesystem->remove($masterImage);
        $this->filesystem->remove($generatedImage);
    }

    public function testThumbnailUrlShouldBeEndedWithTheGeneratedImgPath(): void
    {
        $this->setupManufacturer('oxthumbnail', 'test-thumbnail.png');
        $this->overwriteConfig('sManufacturerThumbnailsize', '80*90');
        $sizeDirectory = '80_90_75';
        [$masterImage, $generatedImage] = $this->createImages('MTHU', 'test-thumbnail.png', $sizeDirectory);

        $this->assertStringEndsWith(
            $this->getImagePathFromSource('MTHU', $sizeDirectory, 'test-thumbnail.png'),
            $this->manufacturer->getThumbnailUrl()
        );

        $this->filesystem->remove($masterImage);
        $this->filesystem->remove($generatedImage);
    }

    public function testPromotionIconUrlShouldBeEndedWithTheGeneratedImgPath(): void
    {
        $this->setupManufacturer('oxpromotion_icon', 'test-promotion-icon.png');
        $this->overwriteConfig('sManufacturerPromotionsize', '80*90');
        $sizeDirectory = '80_90_75';
        [$masterImage, $generatedImage] = $this->createImages('MPICO', 'test-promotion-icon.png', $sizeDirectory);

        $this->assertStringEndsWith(
            $this->getImagePathFromSource('MPICO', $sizeDirectory, 'test-promotion-icon.png'),
            $this->manufacturer->getPromotionIconUrl()
        );

        $this->filesystem->remove($masterImage);
        $this->filesystem->remove($generatedImage);
    }

    private function setupManufacturer(string $imageFieldName, string $imageFileName): void
    {
        $propertyName = 'oxmanufacturers__' . $imageFieldName;
        $this->manufacturer = oxNew(Manufacturer::class);
        $this->manufacturer->setId($this->oxid);
        $this->manufacturer->$propertyName = new Field($imageFileName, Field::T_RAW);
        $this->manufacturer->save();
    }

    private function createImage(string $path, string $imageFileName): string
    {
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path);
        }

        $imagePath = Path::join($path, $imageFileName);

        $this->filesystem->touch($imagePath);

        return $imagePath;
    }

    private function getImagePath(string $imageType, bool $isGenerated = false): string
    {
        return Path::join(Registry::getConfig()->getPictureDir(false), Registry::getUtilsFile()->getImageDirByType($imageType, $isGenerated));
    }

    private function getImagePathFromSource(string $imageType, string $sizeDirectory, string $imageFileName): string
    {
        return Path::join(
            Registry::getUtilsFile()->getImageDirByType($imageType, true),
            $sizeDirectory,
            $imageFileName
        );
    }

    private function createImages(string $imageType, string $imageFileName, string $sizeDirectory): array
    {
        $masterImage = $this->createImage($this->getImagePath($imageType), $imageFileName);
        $generatedImage = $this->createImage(
            Path::join($this->getImagePath($imageType, true), $sizeDirectory),
            $imageFileName
        );

        return [$masterImage, $generatedImage];
    }

    private function overwriteConfig(string $configParam, string $iconSizeValue): void
    {
        Registry::getConfig()->setConfigParam($configParam, false);
        Registry::getConfig()->setConfigParam('sIconsize', $iconSizeValue);
    }
}
