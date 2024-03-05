<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain\ClassExtensionsChainDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ClassExtensionsChainDaoTest extends TestCase
{
    use ContainerTrait;

    public function testSaving(): void
    {
        $chain = new ClassExtensionsChain(['first', 'second']);

        $dao = $this->get(ClassExtensionsChainDaoInterface::class);
        $dao->saveChain(1, $chain);

        $this->assertEquals($chain, $dao->getChain(1));
    }
}
