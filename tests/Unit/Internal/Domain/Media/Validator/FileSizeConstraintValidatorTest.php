<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooLargeException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooSmallException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\FileSizeConstraintValidator;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileSizeConstraintValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidateDoesNotThrowAtMinAndMaxBoundaries(): void
    {
        $validator = new FileSizeConstraintValidator(minSizeKb: 1, maxSizeKb: 2);

        $minFile = $this->createStub(UploadedFile::class);
        $minFile->method('getSize')->willReturn(1024);
        $validator->validate($minFile);

        $maxFile = $this->createStub(UploadedFile::class);
        $maxFile->method('getSize')->willReturn(2048);
        $validator->validate($maxFile);
    }

    public function testValidateThrowsWhenFileTooSmall(): void
    {
        $validator = new FileSizeConstraintValidator(minSizeKb: 1, maxSizeKb: 10);
        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('getSize')->willReturn(1023);

        $this->expectException(FileSizeTooSmallException::class);
        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenFileTooLarge(): void
    {
        $validator = new FileSizeConstraintValidator(minSizeKb: 0, maxSizeKb: 2);
        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('getSize')->willReturn(2049);

        $this->expectException(FileSizeTooLargeException::class);
        $validator->validate($uploadedFile);
    }
}
