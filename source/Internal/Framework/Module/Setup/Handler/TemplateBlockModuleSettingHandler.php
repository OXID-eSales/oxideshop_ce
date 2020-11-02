<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;

class TemplateBlockModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var TemplateBlockExtensionDaoInterface
     */
    private $templateBlockExtensionDao;

    public function __construct(TemplateBlockExtensionDaoInterface $templateBlockExtensionDao)
    {
        $this->templateBlockExtensionDao = $templateBlockExtensionDao;
    }

    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
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

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasTemplateBlocks()) {
            $this->templateBlockExtensionDao->deleteExtensions($configuration->getId(), $shopId);
        }
    }

    private function mapDataToObject(TemplateBlock $templateBlock): TemplateBlockExtension
    {
        $templateBlockExtension = new TemplateBlockExtension();
        $templateBlockExtension
            ->setName($templateBlock->getBlockName())
            ->setFilePath($templateBlock->getModuleTemplatePath())
            ->setExtendedBlockTemplatePath($templateBlock->getShopTemplatePath());

        if (0 !== $templateBlock->getPosition()) {
            $templateBlockExtension->setPosition(
                $templateBlock->getPosition()
            );
        }

        if ('' !== $templateBlock->getTheme()) {
            $templateBlockExtension->setThemeId(
                $templateBlock->getTheme()
            );
        }

        return $templateBlockExtension;
    }
}
