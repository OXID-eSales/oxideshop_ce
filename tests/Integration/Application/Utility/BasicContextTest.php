<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Utility;

use OxidEsales\EshopCommunity\Internal\Application\BootstrapContainer\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class BasicContextTest extends TestCase
{
    use ContainerTrait;
    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    public function setUp()
    {
        $this->basicContext = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);

        parent::setUp();
    }

    public function testIsShopSetUp()
    {
        $this->assertTrue($this->basicContext->isShopLaunched());
    }

    public function testIsShopSetUpReturnsFalseIfConfigFileDoesNotExist()
    {
        $context = new BasicContext('nonExistentFile');
        $this->assertFalse($context->isShopLaunched());
    }

    public function testGetDefaultShopId()
    {
        $this->assertSame(1, $this->basicContext->getDefaultShopId());
    }
}
