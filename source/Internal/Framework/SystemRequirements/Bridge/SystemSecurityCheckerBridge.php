<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\Bridge;


use OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface;

class SystemSecurityCheckerBridge implements SystemSecurityCheckerBridgeInterface
{
    private SystemSecurityCheckerInterface $systemSecurityChecker;

    public function __construct(
        SystemSecurityCheckerInterface $systemSecurityChecker
    )
    {
        $this->systemSecurityChecker = $systemSecurityChecker;
    }

    /** @inheritDoc */
    public function isCryptographicallySecure(): bool
    {
        return $this->systemSecurityChecker->isCryptographicallySecure();
    }
}
