<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

/**
 * @internal
 */
interface ProjectYamlImportServiceInterface
{
    public function addImport(string $serviceDir): void;

    public function removeImport(string $serviceDir);

    /**
     * Checks if the import files exist and if not removes them.
     */
    public function removeNonExistingImports(): void;
}
