<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator;

use Symfony\Component\Mime\MimeTypesInterface;

class FileValidator implements FileValidatorInterface
{
    public function __construct(private MimeTypesInterface $mimeTypesService)
    {
    }

    public function validateImage(string $filePath): bool
    {
        try {
            if (
                !empty($filePath)
                && !str_starts_with(strtoupper($this->mimeTypesService->guessMimeType($filePath)), 'IMAGE/')
            ) {
                return false;
            }
        } catch (\Exception $e) {
            throw new ImageValidationException("Unable to get MimeType of file");
        }

        return true;
    }
}
