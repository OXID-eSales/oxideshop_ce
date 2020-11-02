<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartyPluginsDataProvider implements SmartyPluginsDataProviderInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * SmartyPluginsDataProvider constructor.
     */
    public function __construct(SmartyContextInterface $context)
    {
        $this->context = $context;
    }

    public function getPlugins(): array
    {
        return $this->context->getSmartyPluginDirectories();
    }
}
