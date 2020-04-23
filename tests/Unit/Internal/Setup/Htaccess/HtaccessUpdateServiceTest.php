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
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class HtaccessUpdateServiceTest extends TestCase
{
    use ProphecyTrait;

    public function testUpdateRewriteBaseWillCallDaoMethod(): void
    {
        $rewriteBase = '/some-string';
        $htaccessDaoFactory = $this->prophesize(HtaccessDaoFactoryInterface::class);
        $htaccessDao = $this->prophesize(HtaccessDaoInterface::class);
        $htaccessDaoFactory->createRootHtaccessDao()->willReturn($htaccessDao);

        (new HtaccessUpdateService($htaccessDaoFactory->reveal()))->updateRewriteBaseDirective($rewriteBase);

        $htaccessDao->setRewriteBase($rewriteBase)->shouldHaveBeenCalledOnce();
    }
}
