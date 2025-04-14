<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

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
        $clientMimeType = $uploadedFile->getClientMimeType();
        $clientExtension = $uploadedFile->getClientOriginalExtension();

        $validExtensions = $this->mimeTypeGuesser->getExtensions($clientMimeType);

        if (!$validExtensions || !\in_array($clientExtension, $validExtensions, true)) {
            throw new InvalidMediaException(
                'File extension "%s" does not match the client-provided MIME type "%s". Valid extensions: %s.',
                $clientExtension,
                $clientMimeType,
                implode(', ', $validExtensions)
            );
        }
    }
}
