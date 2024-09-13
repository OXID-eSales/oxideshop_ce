<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

class ShopBaseUrl
{
    public function __construct(
        private readonly string $url,
    ) {
        $this->validateUrl();
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    private function validateUrl(): void
    {
        if (!parse_url($this->url, PHP_URL_SCHEME) || !parse_url($this->url, PHP_URL_HOST)) {
            throw new InvalidShopUrlException(
                "'$this->url' is not a valid URL!"
            );
        }
    }
}
