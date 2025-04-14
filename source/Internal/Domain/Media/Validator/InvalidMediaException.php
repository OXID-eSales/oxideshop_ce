<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use RuntimeException;

class InvalidMediaException extends RuntimeException
{
    private string $format;
    private array $values;

    public function __construct(string $format, ...$values)
    {
        $this->format = $format;
        $this->values = $values;
        parent::__construct($format);
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
