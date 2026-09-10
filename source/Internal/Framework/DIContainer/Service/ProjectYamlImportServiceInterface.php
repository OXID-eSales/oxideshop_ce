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
    public function addImportFromFilePath(string $serviceFilePath): void;

    public function removeImportFromFilePath(string $serviceFilePath): void;

    /**
     * Checks if the import files exist and if not removes them
     *
     * @return void
     */
    public function removeNonExistingImports();
}
