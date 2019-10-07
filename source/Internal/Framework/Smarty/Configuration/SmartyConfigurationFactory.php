<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartyConfigurationFactory implements SmartyConfigurationFactoryInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * @var SmartySettingsDataProviderInterface
     */
    private $settingsDataProvider;

    /**
     * @var SmartySecuritySettingsDataProviderInterface
     */
    private $securitySettingsDataProvider;

    /**
     * @var SmartyResourcesDataProviderInterface
     */
    private $resourcesDataProvider;

    /**
     * @var SmartyPluginsDataProviderInterface
     */
    private $pluginsDataProvider;

    /**
     * @var SmartyPrefiltersDataProviderInterface
     */
    private $prefiltersDataProvider;

    /**
     * SmartyConfigurationFactory constructor.
     *
     * @param SmartyContextInterface                      $context
     * @param SmartySettingsDataProviderInterface         $settingsDataProvider
     * @param SmartySecuritySettingsDataProviderInterface $securitySettingsDataProvider
     * @param SmartyResourcesDataProviderInterface        $resourcesDataProvider
     * @param SmartyPrefiltersDataProviderInterface       $prefiltersDataProvider
     * @param SmartyPluginsDataProviderInterface          $pluginsDataProvider
     */
    public function __construct(
        SmartyContextInterface $context,
        SmartySettingsDataProviderInterface $settingsDataProvider,
        SmartySecuritySettingsDataProviderInterface $securitySettingsDataProvider,
        SmartyResourcesDataProviderInterface $resourcesDataProvider,
        SmartyPrefiltersDataProviderInterface $prefiltersDataProvider,
        SmartyPluginsDataProviderInterface $pluginsDataProvider
    ) {
        $this->context = $context;
        $this->settingsDataProvider = $settingsDataProvider;
        $this->securitySettingsDataProvider = $securitySettingsDataProvider;
        $this->resourcesDataProvider = $resourcesDataProvider;
        $this->prefiltersDataProvider = $prefiltersDataProvider;
        $this->pluginsDataProvider = $pluginsDataProvider;
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
