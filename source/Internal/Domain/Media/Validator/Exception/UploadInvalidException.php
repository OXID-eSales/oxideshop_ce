<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

class UploadInvalidException extends MediaValidationException
{
    public function __construct(private readonly int $errorCode)
    {
        parent::__construct('Upload error with PHP code ' . $errorCode);
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}
