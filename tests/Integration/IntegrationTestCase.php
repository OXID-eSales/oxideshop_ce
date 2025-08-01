<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use OxidEsales\EshopCommunity\Tests\FilesystemTrait;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;
    use FilesystemTrait;

    public function setUp(): void
    {
        parent::setUp();
        TestContainerFactory::resetContainer();
        $this->backupVarDirectory();
        $this->beginTransaction();
        $this->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction();
        $this->restoreVarDirectory();

        parent::tearDown();
    }
}
