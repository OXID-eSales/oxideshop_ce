<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooLargeException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooSmallException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSizeConstraintValidator implements MediaConstraintValidatorInterface
{
    private int $minSizeBytes;
    private int $maxSizeBytes;

    public function __construct(
        private readonly int $minSizeKb,
        private readonly int $maxSizeKb
    ) {
        $this->minSizeBytes = $minSizeKb * 1024;
        $this->maxSizeBytes = $maxSizeKb * 1024;
    }

    public function validate(UploadedFile $uploadedFile): void
    {
        $filesize = $uploadedFile->getSize();

        if ($filesize < $this->minSizeBytes) {
            throw new FileSizeTooSmallException(
                $filesize,
                $this->minSizeKb
            );
        }

        if ($filesize > $this->maxSizeBytes) {
            throw new FileSizeTooLargeException(
                $filesize,
                $this->maxSizeKb
            );
        }
    }
}
