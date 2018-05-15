<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Logger\Mapper;

use OxidEsales\EshopCommunity\Internal\Logger\DataObject\PsrLoggerConfigurationInterface;

/**
 * @internal
 */
interface LogLevelMapperInterface
{
    /**
     * @param PsrLoggerConfigurationInterface $configuration
     *
     * @return string
     */
    public function getLoggerLogLevel(PsrLoggerConfigurationInterface $configuration);
}
