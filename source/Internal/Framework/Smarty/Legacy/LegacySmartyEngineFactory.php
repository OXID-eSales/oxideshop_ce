<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge\SmartyEngineBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

/**
 * Class LegacySmartyEngineFactory.
 *
 * @internal
 */
class LegacySmartyEngineFactory implements TemplateEngineFactoryInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @var SmartyEngineBridgeInterface
     */
    private $smartyBridge;

    /**
     * LegacySmartyEngineFactory constructor.
     */
    public function __construct(ShopAdapterInterface $shopAdapter, SmartyEngineBridgeInterface $smartyBridge)
    {
        $this->shopAdapter = $shopAdapter;
        $this->smartyBridge = $smartyBridge;
    }

    public function getTemplateEngine(): TemplateEngineInterface
    {
        $smarty = $this->shopAdapter->getSmartyInstance();

        //TODO Event for smarty object configuration

        return new LegacySmartyEngine($smarty, $this->smartyBridge);
    }
}
