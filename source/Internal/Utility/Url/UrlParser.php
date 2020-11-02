<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Url;

class UrlParser implements UrlParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPathWithoutTrailingSlash(string $url): string
    {
        return $this->removeTrailingSlash(
            $this->getPath($url)
        );
    }

    private function getPath(string $url): string
    {
        return (string)parse_url($url, PHP_URL_PATH);
    }

    private function removeTrailingSlash(string $path): string
    {
        return rtrim($path, '/');
    }
}
