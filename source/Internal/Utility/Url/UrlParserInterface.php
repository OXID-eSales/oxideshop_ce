<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility\Url;

interface UrlParserInterface
{
    /**
     * @param string $url
     * @return string
     */
    public function getPathWithoutTrailingSlash(string $url): string;
}
