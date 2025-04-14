<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;
use Symfony\Component\Filesystem\Path;

use function sprintf;

#[Group('admin', 'product', 'pictures')]
final class ProductPicturesCest
{
    private string $productNumber = '1000';
    private string $image1 = 'media/product/image_1.webp';
    private string $image2 = 'media/product/image_2.webp';
    private string $image3 = 'media/product/image_3.webp';
    private string $imageInvalid = 'media/product/image_invalid.jpg';

    public function _after(AcceptanceTester $I): void
    {
        $I->restoreProjectConfigurations();
    }

    public function uploadSingleImage(AcceptanceTester $I): void
    {
        $I->wantToTest('uploading a single valid image to a product');
        $I->amGoingTo('set the minimum image size to 1 KB to allow uploading small images');
        $I->updateProjectConfigurations(['oxid_esales.product.media.file.min_size_kb' => 1], []);
        $I
            ->loginAdmin()
            ->openProducts()
            ->findByProductNumber($this->productNumber)
            ->openPicturesTab()
            ->uploadFile($this->image1)
            ->seeUploadedImage(1)
            ->seeUploadedImageIsActive(1)
            ->seeUploadedImageIsNotThumbnail(1)
            ->seeUploadedImageIsNotIcon(1)
            ->canSeeUploadedImageInLightbox(1)
            ->setUploadedImageAsThumb(1)
            ->seeUploadedImageIsThumbnail(1)
            ->canSeeThumbnailInLightbox()
            ->setUploadedImageAsIcon(1)
            ->seeUploadedImageIsIcon(1)
            ->canSeeIconInLightbox()
            ->deactivateUploadedImage(1)
            ->seeUploadedImageIsInactive(1)
            ->activateUploadedImage(1)
            ->seeUploadedImageIsActive(1)
            ->deleteUploadedImage(1)
            ->dontSeeUploadedImage(1)
            ->seeEmptyThumbnailPlaceholder()
            ->seeEmptyIconPlaceholder();
    }

    public function uploadMultipleImages(AcceptanceTester $I): void
    {
        $I->wantToTest('uploading multiple valid images to a product');
        $I->amGoingTo('set the minimum image size to 1 KB to allow uploading small images');
        $I->updateProjectConfigurations(['oxid_esales.product.media.file.min_size_kb' => 1], []);
        $I
            ->loginAdmin()
            ->openProducts()
            ->findByProductNumber($this->productNumber)
            ->openPicturesTab()
            ->uploadFile($this->image1)
            ->uploadFile($this->image2)
            ->uploadFile($this->image3)
            ->setUploadedImageAsThumb(1)
            ->setUploadedImageAsIcon(3)
            ->seeThumbnailImage()
            ->seeIconImage()
            ->seeUploadedImageIsThumbnail(1)
            ->seeUploadedImageIsIcon(3)
            ->seeUploadedImageAtPosition(basename($this->image1), 1)
            ->seeUploadedImageAtPosition(basename($this->image2), 2)
            ->seeUploadedImageAtPosition(basename($this->image3), 3);
        $I->amGoingTo(
            'skip sorting images by drag-n-drop, as the current Selenium driver does not implement it correctly'
        );
    }

    public function uploadInvalidImage(AcceptanceTester $I): void
    {
        $I->wantToTest('uploading invalid image will display an error message');
        $minSize = '1024';
        $I->amGoingTo("set the minimum image size to make previously valid image fixtures invalid");
        $I->updateProjectConfigurations(['oxid_esales.product.media.file.min_size_kb' => $minSize], []);
        $I
            ->loginAdmin()
            ->openProducts()
            ->findByProductNumber($this->productNumber)
            ->openPicturesTab()
            ->uploadFile($this->image1)
            ->seeImageUploadError(
                sprintf(
                    Translator::translate(
                        'File size %d bytes is smaller than the minimum allowed %d KB.',
                    ),
                    filesize(
                        Path::join(
                            codecept_data_dir(),
                            $this->image1
                        )
                    ),
                    $minSize
                )
            );
    }
}
