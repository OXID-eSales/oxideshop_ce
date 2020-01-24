<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker;

interface DatabaseCheckerInterface
{
    /** * @return bool */
    public function isDatabaseCompatible(): bool;

    /** * @return string[] - Array of untranslated notice strings */
    public function getCompatibilityNotices(): array;
}
