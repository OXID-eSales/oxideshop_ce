<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileExtensionMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeTypeGuessFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypesInterface;

readonly class FileExtensionConstraintValidator implements MediaConstraintValidatorInterface
{
    public function __construct(
        private MimeTypesInterface $mimeTypeGuesser
    ) {
    }

    public function validate(UploadedFile $uploadedFile): void
    {
        $clientExtension = strtolower($uploadedFile->getClientOriginalExtension());
        $path = $uploadedFile->getPathname();

        $guessedMimeType = $this->mimeTypeGuesser->guessMimeType($path)
            ?? throw new MimeTypeGuessFailedException($path);

        $validExtensions = array_map(
            'strtolower',
            $this->mimeTypeGuesser->getExtensions($guessedMimeType)
        );

        if (empty($validExtensions) || !in_array($clientExtension, $validExtensions, true)) {
            throw new FileExtensionMismatchException($clientExtension, $validExtensions);
        }
    }
}
