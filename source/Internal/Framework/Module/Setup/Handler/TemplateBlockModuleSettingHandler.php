<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

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
        if ($configuration->hasTemplateBlocks()) {
            foreach ($configuration->getTemplateBlocks() as $templateBlock) {
                $templateBlockExtension = $this->mapDataToObject($templateBlock);
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
        if ($configuration->hasTemplateBlocks()) {
            $this->templateBlockExtensionDao->deleteExtensions($configuration->getId(), $shopId);
        }
    }

    /**
     * @param TemplateBlock $templateBlock
     * @return TemplateBlockExtension
     */
    public function mapDataToObject(TemplateBlock $templateBlock): TemplateBlockExtension
    {
        $templateBlockExtension = new TemplateBlockExtension();
        $templateBlockExtension
            ->setName($templateBlock->getBlockName())
            ->setFilePath($templateBlock->getModuleTemplatePath())
            ->setExtendedBlockTemplatePath($templateBlock->getShopTemplatePath());

        if ($templateBlock->getPosition() !== 0) {
            $templateBlockExtension->setPosition(
                $templateBlock->getPosition()
            );
        }

        if ($templateBlock->getTheme() !== '') {
            $templateBlockExtension->setThemeId(
                $templateBlock->getTheme()
            );
        }

        return $templateBlockExtension;
    }
}
