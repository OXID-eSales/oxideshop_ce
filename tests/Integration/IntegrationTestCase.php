<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration;

use OxidEsales\EshopCommunity\Tests\CachingTrait;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use OxidEsales\EshopCommunity\Tests\RequestTrait;
use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase
{
    use ContainerTrait;
    use CachingTrait;
    use DatabaseTrait;
    use RequestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->backupRequestData();
        $this->cleanupCaching();
        $this->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction();
        $this->cleanupCaching();
        $this->restoreRequestData();
        parent::tearDown();
    }
}
