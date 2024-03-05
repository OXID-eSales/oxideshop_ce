<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

/**
 * @internal
 */
class QueryFilter implements QueryFilterInterface
{
    private array $logThese = [
        'insert into',
        'update ',
        'delete ',
    ];
    private array $skipThese = [
        'oxsession',
        'oxcache',
    ];

    public function shouldLogQuery(string $query, array $skipLogTags): bool
    {
        return (bool)preg_match($this->getSearchPattern($skipLogTags), $query);
    }

    private function getSearchPattern(array $additionalTagsToSkip): string
    {
        $pattern = sprintf(
            "/(.?)(%s)(?!.*%s)",
            implode('|', $this->logThese),
            implode(')(?!.*', $this->skipThese)
        );
        if (!empty($additionalTagsToSkip)) {
            $pattern .= sprintf(
                "(?!.*%s)",
                implode(')(?!.*', $additionalTagsToSkip)
            );
        }

        return "$pattern/i";
    }
}
