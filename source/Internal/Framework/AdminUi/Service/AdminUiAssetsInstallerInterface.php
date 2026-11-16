<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service;

interface AdminUiAssetsInstallerInterface
{
    public function install(): void;

    public function uninstall(): void;

    public function isInstalled(): bool;
}
