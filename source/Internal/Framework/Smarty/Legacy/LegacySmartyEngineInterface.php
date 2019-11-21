<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy;

/**
 * Class LegacySmartyEngineInterface
 * @internal
 */
interface LegacySmartyEngineInterface
{
    /**
     * @return \Smarty
     */
    public function getSmarty(): \Smarty;

    /**
     * @param \Smarty $smarty
     */
    public function setSmarty(\Smarty $smarty);
}
