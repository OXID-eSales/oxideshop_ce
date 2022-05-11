<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Prepare application servers information for export.
 *
 * @internal Do not make a module extension for this class.
 */
interface ApplicationServerExporterInterface
{
    /**
     * Return active server nodes.
     *
     * @return array
     */
    public function exportAppServerList();
}
