<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge\SmartyEngineBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;

/**
 * @internal
 */
class LegacySmartyEngineFactory implements TemplateEngineFactoryInterface
{
    private ShopAdapterInterface $shopAdapter;
    private SmartyEngineBridgeInterface $smartyBridge;
    private TemplateFileResolverBridgeInterface $templateFileResolverBridge;

    /**
     * LegacySmartyEngineFactory constructor.
     *
     * @param ShopAdapterInterface $shopAdapter
     * @param SmartyEngineBridgeInterface $smartyBridge
     */
    public function __construct(
        ShopAdapterInterface $shopAdapter,
        SmartyEngineBridgeInterface $smartyBridge,
        TemplateFileResolverBridgeInterface $templateFileResolverBridge
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->smartyBridge = $smartyBridge;
        $this->templateFileResolverBridge = $templateFileResolverBridge;
    }

    /**
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine(): TemplateEngineInterface
    {
        $smarty = $this->shopAdapter->getSmartyInstance();
        //TODO Event for smarty object configuration
        return new LegacySmartyEngine(
            $smarty,
            $this->smartyBridge,
            $this->templateFileResolverBridge
        );
    }
}
