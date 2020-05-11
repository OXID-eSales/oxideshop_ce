<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Htaccess;

use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessDaoFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdateService;
use OxidEsales\EshopCommunity\Internal\Utility\Url\UrlParserInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class HtaccessUpdateServiceTest extends TestCase
{
    use ProphecyTrait;

    public function testUpdateRewriteBaseDirectiveWithUrlPathWillCallDaoWithExpected(): void
    {
        $url = 'http://some-url.com/some-path';
        $urlPath = '/some-path';
        $htaccessDaoFactory = $this->prophesize(HtaccessDaoFactoryInterface::class);
        $htaccessDao = $this->prophesize(HtaccessDaoInterface::class);
        $htaccessDaoFactory->createRootHtaccessDao()->willReturn($htaccessDao);
        $urlParser = $this->prophesize(UrlParserInterface::class);
        $urlParser->getPathWithoutTrailingSlash($url)->willReturn($urlPath);

        (new HtaccessUpdateService(
            $htaccessDaoFactory->reveal(),
            $urlParser->reveal()
        ))->updateRewriteBaseDirective($url);

        $htaccessDao->setRewriteBase($urlPath)->shouldHaveBeenCalledOnce();
    }

    public function testUpdateRewriteBaseDirectiveWithEmptyUrlPathWillCallDaoWithExpected(): void
    {
        $url = 'http://some-url.com/';
        $rewriteBaseForEmptyPath = '/';
        $htaccessDaoFactory = $this->prophesize(HtaccessDaoFactoryInterface::class);
        $htaccessDao = $this->prophesize(HtaccessDaoInterface::class);
        $htaccessDaoFactory->createRootHtaccessDao()->willReturn($htaccessDao);
        $urlParser = $this->prophesize(UrlParserInterface::class);
        $urlParser->getPathWithoutTrailingSlash($url)->willReturn('');

        (new HtaccessUpdateService(
            $htaccessDaoFactory->reveal(),
            $urlParser->reveal()
        ))->updateRewriteBaseDirective($url);

        $htaccessDao->setRewriteBase($rewriteBaseForEmptyPath)->shouldHaveBeenCalledOnce();
    }
}
