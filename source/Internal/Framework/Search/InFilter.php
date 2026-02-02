<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Search;

use InvalidArgumentException;

readonly class InFilter implements FilterInterface
{
    /** @var list<string> */
    private array $values;

    /** @param list<string> $values */
    public function __construct(
        private string $field,
        array $values,
    ) {
        if (empty($values)) {
            throw new InvalidArgumentException('InFilter requires at least one value');
        }
        $this->values = array_values($values);
    }

    public function getField(): string
    {
        return $this->field;
    }

    /** @return list<string> */
    public function getValues(): array
    {
        return $this->values;
    }
}
