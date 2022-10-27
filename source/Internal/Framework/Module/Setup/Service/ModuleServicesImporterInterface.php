<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

/**
 * @internal
 */
interface ModuleServicesImporterInterface
{
    public function addImport(string $serviceDir, int $shopId): void;
    public function removeImport(string $serviceDir, int $shopId): void;
}
