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
    /**
     * @var HtaccessDaoFactoryInterface
     */
    private $htaccessDaoFactory;
    /**
     * @var UrlParserInterface
     */
    private $urlParser;

    public function __construct(
        HtaccessDaoFactoryInterface $htaccessDaoFactory,
        UrlParserInterface $urlParser
    ) {
        $this->htaccessDaoFactory = $htaccessDaoFactory;
        $this->urlParser = $urlParser;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRewriteBaseDirective(string $url): void
    {
        $this->htaccessDaoFactory->createRootHtaccessDao()->setRewriteBase(
            $this->getRewriteBase($url)
        );
    }

    private function getRewriteBase(string $url): string
    {
        return $this->urlParser->getPathWithoutTrailingSlash($url) ?: self::REWRITE_BASE_FOR_EMPTY_PATH;
    }
}
