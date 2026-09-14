<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;

class InvalidThemeMetaDataException extends \InvalidArgumentException implements ThemeNotLoadableException
{
}
