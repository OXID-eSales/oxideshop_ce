<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Htaccess;

use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessAccessException;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessDao;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessDaoFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class HtaccessDaoFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateRootHtaccessDaoWithExistingPathWillReturnExpected(): void
    {
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getSourcePath()->willReturn(__DIR__ . '/testData');

        $testContext = new HtaccessDao(__DIR__ . '/testData/.htaccess');

        $accessDao = (new HtaccessDaoFactory($basicContext->reveal()))->createRootHtaccessDao();

        $this->assertEquals($accessDao, $testContext);

        $basicContext->getSourcePath()->shouldBeCalledOnce();
    }

    public function testCreateRootHtaccessDaoWithNonExistingPathWillThrow(): void
    {
        $path = 'some-non-existing-path';
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getSourcePath()->willReturn($path);

        $this->expectException(HtaccessAccessException::class);

        (new HtaccessDaoFactory($basicContext->reveal()))->createRootHtaccessDao();
    }
}
