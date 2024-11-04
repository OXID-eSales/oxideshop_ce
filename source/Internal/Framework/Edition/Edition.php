<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Edition;

enum Edition: string
{
    case Community = 'CE';
    case Professional = 'PE';
    case Enterprise = 'EE';

    public function isCommunityEdition(): bool
    {
        return match ($this) {
            self::Community => true,
            default => false,
        };
    }

    public function getFullEditionName(): string
    {
        return match ($this) {
            self::Community => 'Community Edition',
            self::Professional => 'Professional Edition',
            self::Enterprise => 'Enterprise Edition',
        };
    }
}
