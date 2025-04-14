<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesizeConstraintValidator implements MediaConstraintValidatorInterface
{
    private int $minSizeBytes;
    private int $maxSizeBytes;

    public function __construct(
        private readonly string $minSize,
        private readonly string $maxSize
    ) {
        $this->minSizeBytes = (int)$minSize * 1024;
        $this->maxSizeBytes = (int)$maxSize * 1024;
    }

    public function validate(UploadedFile $uploadedFile): void
    {
        $filesize = $uploadedFile->getSize();
        if ($filesize < $this->minSizeBytes) {
            throw new InvalidMediaException(
                'File size %d bytes is smaller than the minimum allowed %d KB.',
                $filesize,
                (int)$this->minSize
            );
        }

        if ($filesize > $this->maxSizeBytes) {
            throw new InvalidMediaException(
                'File size %d bytes exceeds the maximum allowed %d KB.',
                $filesize,
                (int)$this->maxSize
            );
        }
    }
}
