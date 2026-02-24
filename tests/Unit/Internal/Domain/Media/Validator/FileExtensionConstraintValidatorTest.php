<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileExtensionMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeTypeGuessFailedException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\FileExtensionConstraintValidator;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypesInterface;

final class FileExtensionConstraintValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidateDoesNotThrowWhenFileExtensionMatchesDetectedMimeType(): void
    {
        $mimeTypes = $this->createStub(MimeTypesInterface::class);
        $validator = new FileExtensionConstraintValidator($mimeTypes);

        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.jpg');
        $uploadedFile->method('getClientOriginalExtension')->willReturn('JPEG');
        $mimeTypes->method('guessMimeType')->willReturn('image/jpeg');
        $mimeTypes->method('getExtensions')->willReturn(['jpg', 'jpeg']);

        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenFileExtensionIsNotAllowedForMimeType(): void
    {
        $mimeTypes = $this->createStub(MimeTypesInterface::class);
        $validator = new FileExtensionConstraintValidator($mimeTypes);

        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.png');
        $uploadedFile->method('getClientOriginalExtension')->willReturn('png');
        $mimeTypes->method('guessMimeType')->willReturn('image/jpeg');
        $mimeTypes->method('getExtensions')->willReturn(['JPG']);

        $this->expectException(FileExtensionMismatchException::class);
        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenNoValidExtensionsCanBeResolved(): void
    {
        $mimeTypes = $this->createStub(MimeTypesInterface::class);
        $validator = new FileExtensionConstraintValidator($mimeTypes);

        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.jpg');
        $uploadedFile->method('getClientOriginalExtension')->willReturn('jpg');
        $mimeTypes->method('guessMimeType')->willReturn('image/jpeg');
        $mimeTypes->method('getExtensions')->willReturn([]);

        $this->expectException(FileExtensionMismatchException::class);
        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenMimeTypeCannotBeGuessed(): void
    {
        $mimeTypes = $this->createStub(MimeTypesInterface::class);
        $validator = new FileExtensionConstraintValidator($mimeTypes);

        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.jpg');
        $uploadedFile->method('getClientOriginalExtension')->willReturn('jpg');
        $mimeTypes->method('guessMimeType')->willReturn(null);

        $this->expectException(MimeTypeGuessFailedException::class);
        $validator->validate($uploadedFile);
    }
}
