<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @internal
 */
class ProjectYamlChangedEvent extends Event
{
    const NAME = self::class;
}
