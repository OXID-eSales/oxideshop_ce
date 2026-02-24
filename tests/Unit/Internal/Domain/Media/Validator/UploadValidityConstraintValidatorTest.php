<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\UploadInvalidException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\UploadValidityConstraintValidator;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadValidityConstraintValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidateDoesNotThrowWhenUploadIsValid(): void
    {
        $validator = new UploadValidityConstraintValidator();
        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('isValid')->willReturn(true);

        $validator->validate($uploadedFile);
    }

    public function testValidateThrowsWhenUploadHasError(): void
    {
        $validator = new UploadValidityConstraintValidator();
        $uploadedFile = $this->createStub(UploadedFile::class);
        $uploadedFile->method('isValid')->willReturn(false);
        $uploadedFile->method('getError')->willReturn(\UPLOAD_ERR_NO_FILE);

        $this->expectException(UploadInvalidException::class);
        $validator->validate($uploadedFile);
    }
}
