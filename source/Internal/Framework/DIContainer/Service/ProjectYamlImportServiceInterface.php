<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     */
    public function addImport(string $serviceDir);

    /**
     * @param string $serviceDir
     */
    public function removeImport(string $serviceDir);

    /**
     * Checks if the import files exist and if not removes them
     *
     * @return void
     */
    public function removeNonExistingImports();
}
