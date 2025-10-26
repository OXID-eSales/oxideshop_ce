<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

class MimeBaseTypeMismatchException extends MediaValidationException
{
    public function __construct(private readonly string $guessedMime, private readonly string $requiredBasePrefix)
    {
        parent::__construct('MIME type ' . $guessedMime . ' does not match required base ' . $requiredBasePrefix);
    }

    public function getGuessedMime(): string
    {
        return $this->guessedMime;
    }

    public function getRequiredBasePrefix(): string
    {
        return $this->requiredBasePrefix;
    }
}
