<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartyConfigurationFactory implements SmartyConfigurationFactoryInterface
{
    /**
     * SmartyConfigurationFactory constructor.
     */
    public function __construct(private SmartyContextInterface $context, private SmartySettingsDataProviderInterface $settingsDataProvider, private SmartySecuritySettingsDataProviderInterface $securitySettingsDataProvider, private SmartyResourcesDataProviderInterface $resourcesDataProvider, private SmartyPrefiltersDataProviderInterface $prefiltersDataProvider, private SmartyPluginsDataProviderInterface $pluginsDataProvider)
    {
    }

    /**
     * @return SmartyConfigurationInterface
     */
    public function getConfiguration(): SmartyConfigurationInterface
    {
        $smartyConfiguration = new SmartyConfiguration();
        $smartyConfiguration->setSettings($this->settingsDataProvider->getSettings());
        if ($this->context->getTemplateSecurityMode()) {
            $smartyConfiguration->setSecuritySettings($this->securitySettingsDataProvider->getSecuritySettings());
        }
        $smartyConfiguration->setResources($this->resourcesDataProvider->getResources());
        $smartyConfiguration->setPrefilters($this->prefiltersDataProvider->getPrefilterPlugins());
        $smartyConfiguration->setPlugins($this->pluginsDataProvider->getPlugins());

        return $smartyConfiguration;
    }
}
