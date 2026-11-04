<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi;

interface AdminUiPathProviderInterface
{
    public function getAbsolutePath(): string;
}
