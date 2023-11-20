<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao\AppConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\AppConfiguration;

class AppConfigurationDaoBridge implements AppConfigurationDaoBridgeInterface
{
    public function __construct(
        private readonly AppConfigurationDaoInterface $appConfigurationDao
    ) {
    }

    public function get(): AppConfiguration
    {
        return $this->appConfigurationDao->get();
    }
}
