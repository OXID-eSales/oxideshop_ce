<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker\DatabaseCheckerInterface;

class DatabaseCheckerBridge implements DatabaseCheckerBridgeInterface
{
    /** * @var DatabaseCheckerInterface */
    private $databaseCheckerService;

    /**
     * DatabaseCheckerBridge constructor.
     * @param DatabaseCheckerInterface $databaseCheckerService
     */
    public function __construct(DatabaseCheckerInterface $databaseCheckerService)
    {
        $this->databaseCheckerService = $databaseCheckerService;
    }

    /** * @return bool */
    public function isDatabaseCompatible(): bool
    {
        return $this->databaseCheckerService->isDatabaseCompatible();
    }

    /** * @return string[] - Array of untranslated notice strings */
    public function getCompatibilityNotices(): array
    {
        return $this->databaseCheckerService->getCompatibilityNotices();
    }
}
