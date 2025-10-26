<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

class MimeGuessMismatchException extends MediaValidationException
{
    public function __construct(private readonly string $guessedMime, private readonly string $clientMime)
    {
        parent::__construct('Guessed MIME ' . $guessedMime . ' does not match client MIME ' . $clientMime);
    }

    public function getGuessedMime(): string
    {
        return $this->guessedMime;
    }

    public function getClientMime(): string
    {
        return $this->clientMime;
    }
}
