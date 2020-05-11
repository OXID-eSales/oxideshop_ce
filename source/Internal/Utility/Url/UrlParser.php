<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Url;

class UrlParser implements UrlParserInterface
{
    /** @inheritDoc */
    public function getPathWithoutTrailingSlash(string $url): string
    {
        return $this->removeTrailingSlash(
            $this->getPath($url)
        );
    }

    /**
     * @param string $url
     * @return string
     */
    private function getPath(string $url): string
    {
        return (string)parse_url($url, PHP_URL_PATH);
    }

    /**
     * @param string $path
     * @return string
     */
    private function removeTrailingSlash(string $path): string
    {
        return rtrim($path, '/');
    }
}
