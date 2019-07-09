<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class TemplateBlockModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var TemplateBlockExtensionDaoInterface
     */
    private $templateBlockExtensionDao;

    /**
     * TemplateBlockModuleSettingHandler constructor.
     * @param TemplateBlockExtensionDaoInterface $templateBlockExtensionDao
     */
    public function __construct(TemplateBlockExtensionDaoInterface $templateBlockExtensionDao)
    {
        $this->templateBlockExtensionDao = $templateBlockExtensionDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($this->canHandle($configuration)) {
            $setting = $configuration->getSetting(ModuleSetting::TEMPLATE_BLOCKS);
            $templateBlocksData = $setting->getValue();

            foreach ($templateBlocksData as $templateBlockData) {
                $templateBlockExtension = $this->mapDataToObject($templateBlockData);
                $templateBlockExtension->setShopId($shopId);
                $templateBlockExtension->setModuleId($configuration->getId());

                $this->templateBlockExtensionDao->add($templateBlockExtension);
            }
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($this->canHandle($configuration)) {
            $this->templateBlockExtensionDao->deleteExtensions($configuration->getId(), $shopId);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return bool
     */
    private function canHandle(ModuleConfiguration $configuration): bool
    {
        return $configuration->hasSetting(ModuleSetting::TEMPLATE_BLOCKS);
    }

    /**
     * @param array $templateBlockData
     * @return TemplateBlockExtension
     */
    public function mapDataToObject(array $templateBlockData): TemplateBlockExtension
    {
        $templateBlockExtension = new TemplateBlockExtension();
        $templateBlockExtension
            ->setName($templateBlockData['block'])
            ->setFilePath($templateBlockData['file'])
            ->setExtendedBlockTemplatePath($templateBlockData['template']);

        if (isset($templateBlockData['position'])) {
            $templateBlockExtension->setPosition(
                (int) $templateBlockData['position']
            );
        }

        if (isset($templateBlockData['theme'])) {
            $templateBlockExtension->setThemeId(
                $templateBlockData['theme']
            );
        }

        return $templateBlockExtension;
    }
}
