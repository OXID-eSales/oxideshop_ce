<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploaderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\InvalidMediaException;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaPathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaUploadProcessorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use const UPLOAD_ERR_NO_FILE;

final class ProductMediaUploadProcessorTest extends IntegrationTestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    private const VALID_IMAGE = 'valid_image.jpg';
    private const INVALID_IMAGE = 'invalid_image.jpg';
    private const VALID_IMAGE_WRONG_EXTENSION = 'valid_image.png';

    private readonly Id $productId;
    private readonly string $destinationPath;
    private readonly ProductMediaUploadProcessorInterface $productMediaUploadProcessor;
    private readonly MediaUploaderInterface|ObjectProphecy $mediaUploader;
    private readonly InvalidMediaException $exception;

    public function testUploadWithValidImage(): void
    {
        $this->allowSmallFilesUploadInConfiguration();
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->get(ProductMediaUploadProcessorInterface::class)
            ->process(
                Id::generate(),
                $uploadedFile
            );

        $this->mediaUploader
            ->upload($uploadedFile)
            ->shouldHaveBeenCalledOnce();
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->processAndExpectException($uploadedFile);

        $this->assertEquals(
            'File size %d bytes is smaller than the minimum allowed %d KB.',
            $this->exception->getFormat()
        );
        $this->assertEquals(filesize($fixture), $this->exception->getValues()[0]);
        $this->assertEquals(1024, $this->exception->getValues()[1]);
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->processAndExpectException($uploadedFile);

        $this->assertEquals(
            'File size %d bytes exceeds the maximum allowed %d KB.',
            $this->exception->getFormat()
        );
        $this->assertEquals(filesize($fixture), $this->exception->getValues()[0]);
        $this->assertEquals(1, $this->exception->getValues()[1]);
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->processAndExpectException($uploadedFile);

        $this->assertEquals(
            'MIME type "%s" does not match required base type "%s".',
            $this->exception->getFormat()
        );
        $this->assertEquals('text/plain', $this->exception->getValues()[0]);
        $this->assertEquals('image/', $this->exception->getValues()[1]);
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->processAndExpectException($uploadedFile);

        $this->assertEquals(
            'Guessed MIME type "%s" does not match client-provided MIME type "%s".',
            $this->exception->getFormat()
        );
        $this->assertEquals('image/jpeg', $this->exception->getValues()[0]);
        $this->assertEquals($uploadedFile->getClientMimeType(), $this->exception->getValues()[1]);
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->processAndExpectException($uploadedFile);

        $this->assertEquals(
            'File extension "%s" does not match the client-provided MIME type "%s". Valid extensions: %s.',
            $this->exception->getFormat()
        );
        $this->assertEquals('png', $this->exception->getValues()[0]);
        $this->assertEquals($uploadedFile->getClientMimeType(), $this->exception->getValues()[1]);
        $this->assertNotEmpty($this->exception->getValues()[2]);
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
        $this->stubMediaUploaderService($uploadedFile);

        $this->processAndExpectException($uploadedFile);

        $this->assertEquals(
            'Media file upload error: %s',
            $this->exception->getFormat()
        );
        $this->assertEquals($uploadedFile->getErrorMessage(), $this->exception->getValues()[0]);
    }

    private function replaceMediaUploaderServiceInstance(): void
    {
        $this->createContainer();
        $this->mediaUploader = $this->prophesize(MediaUploaderInterface::class);
        $this->container->set(
            'oxid_esales.product.media.media_uploader',
            $this->mediaUploader->reveal()
        );
        $this->compileContainer();
    }

    private function stubMediaUploaderService(UploadedFile $uploadedFile): void
    {
        $this->mediaUploader
            ->upload($uploadedFile)
            ->willReturn(new MediaPath(
                Path::join(
                    $this->getMediaPathResolved(),
                    $uploadedFile->getClientOriginalName()
                )
            ));
    }

    private function getMediaPathResolved(): string
    {
        return $this->get(ProductMediaPathResolverInterface::class)->getRelativePath('product_media');
    }

    public function allowSmallFilesUploadInConfiguration(): void
    {
        $this->rewriteProjectConfiguration([
            'parameters' => [
                'oxid_esales.product.media.file.min_size_kb' => '0',
            ]
        ]);
    }

    private function processAndExpectException(UploadedFile $uploadedFile): void
    {
        try {
            $this->get(ProductMediaUploadProcessorInterface::class)->process(Id::generate(), $uploadedFile);

            $this->fail('Expected InvalidMediaException was not thrown.');
        } catch (InvalidMediaException $e) {
            $this->exception = $e;
        }
    }
}
