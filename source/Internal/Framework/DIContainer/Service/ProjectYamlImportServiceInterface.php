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
    /**
     * @param string $serviceDir
     *
     * @return void
     * @deprecated will be removed in the next major version, use addImportFromFilePath() instead
     */
    public function addImport(string $serviceDir);

    /**
     * @param string $serviceDir
     *
     * @deprecated will be removed in the next major version, use removeImportFromFilePath() instead
     */
    public function removeImport(string $serviceDir);

    public function addImportFromFilePath(string $serviceFilePath): void;

    public function removeImportFromFilePath(string $serviceFilePath): void;

    /**
     * Checks if the import files exist and if not removes them
     *
     * @return void
     */
    public function removeNonExistingImports();
}
