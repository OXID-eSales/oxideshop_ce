<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge\SmartyEngineBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;

class SmartyEngineFactory implements TemplateEngineFactoryInterface
{
    /**
     * SmartyEngineFactory constructor.
     */
    public function __construct(private SmartyBuilder $smartyBuilder, private SmartyConfigurationInterface $smartyConfiguration)
    {
    }

    /**
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine(): TemplateEngineInterface
    {
        $smarty = $this->smartyBuilder
            ->setSettings($this->smartyConfiguration->getSettings())
            ->setSecuritySettings($this->smartyConfiguration->getSecuritySettings())
            ->registerPlugins($this->smartyConfiguration->getPlugins())
            ->registerPrefilters($this->smartyConfiguration->getPrefilters())
            ->registerResources($this->smartyConfiguration->getResources())
            ->getSmarty();

        // TODO Event for smarty object configuration

        return new SmartyEngine($smarty, new SmartyEngineBridge());
    }
}
