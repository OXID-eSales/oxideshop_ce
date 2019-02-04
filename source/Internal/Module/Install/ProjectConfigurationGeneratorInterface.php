<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install;

/**
 * @internal
 */
interface ProjectConfigurationGeneratorInterface
{
    /**
     * Generates default project configuration.
     */
    public function generate();
}
