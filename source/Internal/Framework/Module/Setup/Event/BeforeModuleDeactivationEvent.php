<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeModuleDeactivationEvent extends ModuleSetupEvent
{
    const NAME = self::class;
}
