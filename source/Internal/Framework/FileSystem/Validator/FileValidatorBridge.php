<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator;

class FileValidatorBridge implements FileValidatorBridgeInterface
{
    public function __construct(private FileValidatorInterface $fileValidator)
    {
    }

    public function validateImage(string $filePath): bool
    {
        return $this->fileValidator->validateImage($filePath);
    }
}
