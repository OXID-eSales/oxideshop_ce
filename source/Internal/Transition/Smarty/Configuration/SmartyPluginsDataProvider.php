<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Transition\Smarty\SmartyContextInterface;

/**
 * Class SmartyPluginsDataProvider
 * @package OxidEsales\EshopCommunity\Internal\Smarty\Configuration
 */
class SmartyPluginsDataProvider implements SmartyPluginsDataProviderInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * SmartyPluginsDataProvider constructor.
     *
     * @param SmartyContextInterface $context
     */
    public function __construct(SmartyContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->context->getSmartyPluginDirectories();
    }
}
