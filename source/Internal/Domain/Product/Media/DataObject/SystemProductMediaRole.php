<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

enum SystemProductMediaRole: string
{
    case Icon = 'icon';
    case Thumb = 'thumb';
    case Detail = 'detail';
    case Listing = 'list';
    case Zoom = 'zoom';
}
