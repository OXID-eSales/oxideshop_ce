<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Setup\Database\DatabaseNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\ShopSetupCommand;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ShopSetupCommandTest extends TestCase
{
    use ContainerTrait;

    public function testShopSetupCommandFailsWhenDatabaseIsNotEmpty(): void
    {
        $this->expectException(DatabaseNotEmptyException::class);

        (new CommandTester(
            $this->get(ShopSetupCommand::class)
        ))->execute([]);
    }
}
