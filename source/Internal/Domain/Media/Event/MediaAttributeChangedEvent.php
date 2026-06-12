<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use Symfony\Contracts\EventDispatcher\Event;

class MediaAttributeChangedEvent extends Event
{
    public function __construct(private readonly Id $mediaId)
    {
    }

    public function getMediaId(): Id
    {
        return $this->mediaId;
    }
}
