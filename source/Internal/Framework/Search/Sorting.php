<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Search;

use InvalidArgumentException;

readonly class Sorting
{
    public function __construct(
        private string $field,
        private SortDirection $direction = SortDirection::Asc,
    ) {
    }

    public static function fromString(string $field, string $direction): self
    {
        $normalized = strtoupper(trim($direction));

        return new self(
            $field,
            match ($normalized) {
                SortDirection::Asc->value => SortDirection::Asc,
                SortDirection::Desc->value => SortDirection::Desc,
                default => throw new InvalidArgumentException(
                    sprintf(
                        'Invalid sort direction "%s", expected "%s" or "%s"',
                        $direction,
                        SortDirection::Asc->value,
                        SortDirection::Desc->value
                    )
                ),
            }
        );
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getDirection(): SortDirection
    {
        return $this->direction;
    }
}
