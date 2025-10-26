<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

class FileExtensionMismatchException extends MediaValidationException
{
    public function __construct(private readonly string $clientExtension, private readonly array $validExtensions)
    {
        parent::__construct('Extension ' . $clientExtension . ' not valid for detected MIME type');
    }

    public function getClientExtension(): string
    {
        return $this->clientExtension;
    }

    /**
     * @return array<string>
     */
    public function getValidExtensions(): array
    {
        return $this->validExtensions;
    }
}
