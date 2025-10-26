<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeBaseTypeMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeGuessMismatchException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

readonly class MimeTypeConstraintValidator implements MediaConstraintValidatorInterface
{
    public function __construct(
        private string $baseTypePrefix,
        private MimeTypes $mimeTypeGuesser
    ) {
    }

    public function validate(UploadedFile $uploadedFile): void
    {
        $path = $uploadedFile->getPathname();

        $guessedMimeType = $this->mimeTypeGuesser->guessMimeType($path);
        $clientMimeType = $uploadedFile->getClientMimeType();

        if (!str_starts_with($guessedMimeType, $this->baseTypePrefix)) {
            throw new MimeBaseTypeMismatchException(
                $guessedMimeType,
                $this->baseTypePrefix
            );
        }

        if ($guessedMimeType !== $clientMimeType) {
            throw new MimeGuessMismatchException(
                $guessedMimeType,
                $clientMimeType
            );
        }
    }
}
