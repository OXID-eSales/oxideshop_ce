<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

/**
 * @internal
 */
class QueryFilter implements QueryFilterInterface
{
    /**
     * @var array
     */
    private $logThese = [
        'insert into',
        'update ',
        'delete '
    ];

    /**
     * @var array
     */
    private $skipThese = [
        'oxsession',
        'oxcache'
    ];

    /**
     * @param string $query       Query string
     * @param array  $skipLogTags Additional tags to skip
     *
     * @return bool
     */
    public function shouldLogQuery(string $query, array $skipLogTags): bool
    {
        return (bool) preg_match($this->getSearchPattern($skipLogTags), $query);
    }

    /**
     * Assemble search pattern
     *
     * @param array  $skipLogTags Additional tags to skip
     *
     * @return string
     */
    private function getSearchPattern(array $skipLogTags): string
    {
        $pattern = '/(.?)(' . implode('|', $this->logThese) . ')(?!.*' .
                   implode(')(?!.*', $this->skipThese) . ')';

        if (!empty($skipLogTags)) {
            $pattern .= '(?!.*' . implode(')(?!.*', $skipLogTags) . ')';
        }
        $pattern .= '/i';

        return $pattern;
    }
}
