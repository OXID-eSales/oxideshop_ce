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
        private HtaccessDaoFactoryInterface $htaccessDaoFactory,
        private UrlParserInterface $urlParser
    ) {
    }

    /** @inheritDoc */
    public function updateRewriteBaseDirective(string $url): void
    {
        $this->htaccessDaoFactory->createRootHtaccessDao()->setRewriteBase(
            $this->getRewriteBase($url)
        );
    }

    /**
     * @param string $url
     * @return string
     */
    private function getRewriteBase(string $url): string
    {
        return $this->urlParser->getPathWithoutTrailingSlash($url) ?: self::REWRITE_BASE_FOR_EMPTY_PATH;
    }
}
