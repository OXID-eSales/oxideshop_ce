<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploaderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileExtensionMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooLargeException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooSmallException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeBaseTypeMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeGuessMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\UploadInvalidException;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaUploadProcessorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use const UPLOAD_ERR_NO_FILE;

final class ProductMediaUploadProcessorTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const VALID_IMAGE = 'valid_image.jpg';
    private const INVALID_IMAGE = 'invalid_image.jpg';
    private const VALID_IMAGE_WRONG_EXTENSION = 'valid_image.png';

    private readonly Id $productId;
    private readonly string $destinationPath;
    private readonly ProductMediaUploadProcessorInterface $productMediaUploadProcessor;
    private MediaUploaderInterface $mediaUploader;

    public function testUploadWithValidImage(): void
    {
        $this->allowSmallFilesUploadInConfiguration();
        $this->replaceMediaUploaderServiceInstanceWithMock();
        $fixture = Path::join(
            __DIR__,
            'Fixtures',
            self::VALID_IMAGE
        );
        $uploadedFile = new UploadedFile(
            $fixture,
            self::VALID_IMAGE,
            'image/jpeg',
            null,
            true
        );
        $this->mediaUploader
            ->expects($this->once())
            ->method('uploadTo')
            ->with($uploadedFile, $this->isInstanceOf(MediaPath::class))
            ->willReturn(new MediaPath(
                Path::join('out/pictures/media/products/placeholder', $uploadedFile->getClientOriginalName())
            ));

        $productId = Id::generate();
        $result = $this->get(ProductMediaUploadProcessorInterface::class)
            ->process(
                $productId,
                $uploadedFile
            );

        $this->assertEquals((string) $productId, (string) $result->getProductId());
        $this->assertStringEndsWith(self::VALID_IMAGE, (string) $result->getMedia()->getMediaPath());
        $this->assertEquals($uploadedFile->getClientMimeType(), (string) $result->getMedia()->getMediaType());
    }

    public function testUploadWithFileTooSmall(): void
    {
        $this->rewriteProjectConfiguration([
            'parameters' => [
                'oxid_esales.product.media.file.min_size_kb' => '1024',
            ]
        ]);
        $this->replaceMediaUploaderServiceInstance();
        $fixture = Path::join(
            __DIR__,
            'Fixtures',
            self::VALID_IMAGE
        );
        $uploadedFile = new UploadedFile(
            $fixture,
            self::VALID_IMAGE,
            'image/jpeg',
            null,
            true
        );

        $this->expectException(FileSizeTooSmallException::class);
        $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);
    }

    public function testUploadWithFileTooBig(): void
    {
        $this->rewriteProjectConfiguration([
            'parameters' => [
                'oxid_esales.product.media.file.min_size_kb' => '0',
                'oxid_esales.product.media.file.max_size_kb' => '1',
            ]
        ]);
        $this->replaceMediaUploaderServiceInstance();
        $fixture = Path::join(
            __DIR__,
            'Fixtures',
            self::VALID_IMAGE
        );
        $uploadedFile = new UploadedFile(
            $fixture,
            self::VALID_IMAGE,
            'image/jpeg',
            null,
            true
        );

        $this->expectException(FileSizeTooLargeException::class);
        $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);
    }

    public function testUploadWithNonImageMimeTypeFile(): void
    {
        $this->allowSmallFilesUploadInConfiguration();
        $this->replaceMediaUploaderServiceInstance();
        $uploadedFile = new UploadedFile(
            Path::join(
                __DIR__,
                'Fixtures',
                self::INVALID_IMAGE
            ),
            self::INVALID_IMAGE,
            'image/jpeg',
            null,
            true
        );

        $this->expectException(MimeBaseTypeMismatchException::class);
        $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);
    }

    public function testUploadWitMimeTypeSpoofing(): void
    {
        $this->allowSmallFilesUploadInConfiguration();
        $this->replaceMediaUploaderServiceInstance();
        $uploadedFile = new UploadedFile(
            Path::join(
                __DIR__,
                'Fixtures',
                self::VALID_IMAGE
            ),
            self::VALID_IMAGE,
            'image/png',
            null,
            true
        );

        $this->expectException(MimeGuessMismatchException::class);
        $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);
    }

    public function testUploadWithImageFileHavingInvalidFileExtension(): void
    {
        $this->allowSmallFilesUploadInConfiguration();
        $this->replaceMediaUploaderServiceInstance();
        $uploadedFile = new UploadedFile(
            Path::join(
                __DIR__,
                'Fixtures',
                self::VALID_IMAGE_WRONG_EXTENSION
            ),
            self::VALID_IMAGE_WRONG_EXTENSION,
            'image/jpeg',
            null,
            true
        );
        $this->expectException(FileExtensionMismatchException::class);
        $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);
    }

    public function testUploadWithFileHavingAnErrorSetDuringUploading(): void
    {
        $this->allowSmallFilesUploadInConfiguration();
        $this->replaceMediaUploaderServiceInstance();
        $uploadedFile = new UploadedFile(
            Path::join(
                __DIR__,
                'Fixtures',
                self::VALID_IMAGE
            ),
            self::VALID_IMAGE,
            'image/jpeg',
            UPLOAD_ERR_NO_FILE,
            true
        );
        $this->expectException(UploadInvalidException::class);
        $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);
    }

    private function replaceMediaUploaderServiceInstance(): void
    {
        $this->createContainer();
        $this->mediaUploader = $this->createStub(MediaUploaderInterface::class);
        $this->container->set(
            'oxid_esales.product.media.media_uploader',
            $this->mediaUploader
        );
        $this->compileContainer();
    }

    private function replaceMediaUploaderServiceInstanceWithMock(): void
    {
        $this->createContainer();
        $this->mediaUploader = $this->createMock(MediaUploaderInterface::class);
        $this->container->set(
            'oxid_esales.product.media.media_uploader',
            $this->mediaUploader
        );
        $this->compileContainer();
    }

    public function allowSmallFilesUploadInConfiguration(): void
    {
        $this->rewriteProjectConfiguration([
            'parameters' => [
                'oxid_esales.product.media.file.min_size_kb' => '0',
            ]
        ]);
    }
}
