<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;

/**
 * @internal
 */
class ModuleConfigurationDataMapper implements ModuleConfigurationDataMapperInterface
{
    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [
            'id'          => $configuration->getId(),
            'path'        => $configuration->getPath(),
            'version'     => $configuration->getVersion(),
            'autoActive'  => $configuration->isAutoActive(),
            'title'       => $configuration->getTitle(),
            'description' => $configuration->getDescription(),
            'lang'        => $configuration->getLang(),
            'thumbnail'   => $configuration->getThumbnail(),
            'author'      => $configuration->getAuthor(),
            'url'         => $configuration->getUrl(),
            'email'       => $configuration->getEmail(),
            'settings'    => $this->getSettingsData($configuration)
        ];

        return $data;
    }

    /**
     * @param array $data
     *
     * @return ModuleConfiguration
     */
    public function fromData(array $data): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($data['id'])
            ->setPath($data['path'])
            ->setVersion($data['version'])
            ->setAutoActive($data['autoActive'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLang($data['lang'])
            ->setThumbnail($data['thumbnail'])
            ->setAuthor($data['author'])
            ->setUrl($data['url'])
            ->setEmail($data['email'])
        ;

        if (isset($data['settings'])) {
            $this->setSettings($moduleConfiguration, $data['settings']);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $settingsData
     */
    private function setSettings(ModuleConfiguration $moduleConfiguration, array $settingsData)
    {
        $settings = $this->getMappedSettings($settingsData);

        foreach ($settings as $setting) {
            $moduleConfiguration->addSetting(
                $setting
            );
        }
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function getSettingsData(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getSettings() as $setting) {
            $data[$setting->getName()] = $setting->getValue();
        }

        return $data;
    }

    /**
     * @param array $settingsData
     *
     * @return array
     */
    private function getMappedSettings(array $settingsData): array
    {
        $settings = [];
        foreach ($settingsData as $settingName => $settingValue) {
            $settings[] = new ModuleSetting($settingName, $settingValue);
        }

        return $settings;
    }
}
