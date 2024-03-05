<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\SystemRequirements;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SystemRequirementsTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testGetPermissionIssuesList(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_build_directory', 'some wrong directory');
        $this->container->compile();
        $this->attachContainerToContainerFactory();

        $checkResults = (new SystemRequirements())->getPermissionIssuesList();

        $this->assertNotEmpty($checkResults['missing']);
        $this->assertNotEmpty($checkResults['not_writable']);
    }
}
