<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileExtensionMismatchException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

readonly class FileExtensionConstraintValidator implements MediaConstraintValidatorInterface
{
    public function __construct(
        private MimeTypes $mimeTypeGuesser
    ) {
    }

    public function validate(UploadedFile $uploadedFile): void
    {
        $clientExtension = strtolower($uploadedFile->getClientOriginalExtension());
        $guessedMimeType = $this->mimeTypeGuesser->guessMimeType($uploadedFile->getPathname());
        $validExtensions = array_map(
            'strtolower',
            $this->mimeTypeGuesser->getExtensions($guessedMimeType)
        );

        if (empty($validExtensions) || !in_array($clientExtension, $validExtensions, true)) {
            throw new FileExtensionMismatchException($clientExtension, $validExtensions);
        }
    }
}
