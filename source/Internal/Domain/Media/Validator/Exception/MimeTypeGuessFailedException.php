<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

class MimeTypeGuessFailedException extends MediaValidationException
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;

        parent::__construct(\sprintf('Unable to guess MIME type for file "%s".', $path));
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
