<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator;

interface FileValidatorInterface
{
    /**
     * @throws ImageValidationException
     */
    public function validateImage(string $filePath): bool;
}
