<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

use OxidEsales\EshopCommunity\Internal\Utility\Url\UrlParserInterface;

class HtaccessUpdater implements HtaccessUpdaterInterface
{
    private const REWRITE_BASE_FOR_EMPTY_PATH = '/';

    public function __construct(
        private readonly HtaccessDaoFactoryInterface $htaccessDaoFactory,
        private readonly UrlParserInterface $urlParser
    ) {
    }

    /** @inheritDoc */
    public function updateRewriteBaseDirective(ShopBaseUrl $shopBaseUrl): void
    {
        $this->htaccessDaoFactory->createRootHtaccessDao()->setRewriteBase(
            $this->getRewriteBase($shopBaseUrl->getUrl())
        );
    }

    private function getRewriteBase(string $url): string
    {
        return $this->urlParser->getPathWithoutTrailingSlash($url) ?: self::REWRITE_BASE_FOR_EMPTY_PATH;
    }
}
