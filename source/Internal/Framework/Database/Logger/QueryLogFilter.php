<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use function implode;
use function sprintf;

readonly class QueryLogFilter implements QueryLogFilterInterface
{
    private const SHOULD_LOG_IF_CONTAINS_PATTERN = '(.?)(insert into|update |delete )';
    private const SHOULD_NOT_LOG_IF_CONTAINS_PATTERN = '(?!.*oxsession)(?!.*oxcache)';

    public function __construct(private array $skipLogTags)
    {
    }

    public function shouldLogQuery(string $query): bool
    {
        $additionalPatternToSkipLogging = !empty($this->skipLogTags)
            ? sprintf(
                '(?!.*%s)',
                implode(')(?!.*', $this->skipLogTags)
            )
            : '';

        return (bool)preg_match(
            sprintf(
                '/%s%s%s/i',
                self::SHOULD_LOG_IF_CONTAINS_PATTERN,
                self::SHOULD_NOT_LOG_IF_CONTAINS_PATTERN,
                $additionalPatternToSkipLogging
            ),
            $query
        );
    }
}
