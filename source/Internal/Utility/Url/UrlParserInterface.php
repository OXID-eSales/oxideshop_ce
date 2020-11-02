<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility\Url;

interface UrlParserInterface
{
    public function getPathWithoutTrailingSlash(string $url): string;
}
