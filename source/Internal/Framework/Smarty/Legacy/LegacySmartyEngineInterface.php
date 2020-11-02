<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy;

/**
 * Class LegacySmartyEngineInterface.
 *
 * @internal
 */
interface LegacySmartyEngineInterface
{
    public function getSmarty(): \Smarty;

    public function setSmarty(\Smarty $smarty);
}
