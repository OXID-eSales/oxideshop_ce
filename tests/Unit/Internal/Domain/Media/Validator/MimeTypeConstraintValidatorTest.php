<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeBaseTypeMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeGuessMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeTypeGuessFailedException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\MimeTypeConstraintValidator;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

final class MimeTypeConstraintValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidateDoesNotThrowWhenMimeTypeMatches(): void
    {
        $guesser = $this->createMock(MimeTypeGuesserInterface::class);
        $validator = new MimeTypeConstraintValidator('image/', $guesser);

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/valid.jpg');
        $uploadedFile->method('getClientMimeType')->willReturn('image/jpeg');
        $guesser->method('guessMimeType')->willReturn('image/jpeg');

        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenGuessedMimeBaseTypeDoesNotMatchRequiredPrefix(): void
    {
        $guesser = $this->createMock(MimeTypeGuesserInterface::class);
        $validator = new MimeTypeConstraintValidator('image/', $guesser);

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.txt');
        $uploadedFile->method('getClientMimeType')->willReturn('text/plain');
        $guesser->method('guessMimeType')->willReturn('text/plain');

        $this->expectException(MimeBaseTypeMismatchException::class);
        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenClientMimeTypeDoesNotMatchGuessedMimeType(): void
    {
        $guesser = $this->createMock(MimeTypeGuesserInterface::class);
        $validator = new MimeTypeConstraintValidator('image/', $guesser);

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.jpg');
        $uploadedFile->method('getClientMimeType')->willReturn('image/png');
        $guesser->method('guessMimeType')->willReturn('image/jpeg');

        $this->expectException(MimeGuessMismatchException::class);
        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenMimeTypeCannotBeGuessed(): void
    {
        $guesser = $this->createMock(MimeTypeGuesserInterface::class);
        $validator = new MimeTypeConstraintValidator('image/', $guesser);

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn('/tmp/file.jpg');
        $uploadedFile->method('getClientMimeType')->willReturn('image/jpeg');
        $guesser->method('guessMimeType')->willReturn(null);

        $this->expectException(MimeTypeGuessFailedException::class);
        $validator->validate($uploadedFile);
    }
}
