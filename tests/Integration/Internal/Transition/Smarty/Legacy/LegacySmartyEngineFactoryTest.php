<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty\Legacy;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy\LegacySmartyEngine;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class LegacySmartyEngineFactoryTest extends IntegrationTestCase
{
    public function testGetTemplateEngine()
    {
        $factory = $this->get('smarty.smarty_engine_factory');

        $this->assertInstanceOf(LegacySmartyEngine::class, $factory->getTemplateEngine());
    }
}
