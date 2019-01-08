<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;

/**
 * @internal
 */
class TemplateBlockModuleSettingHandler implements ModuleSettingHandlerInterface
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
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongModuleSettingException
     */
    public function handleOnModuleActivation(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canHandle($moduleSetting)) {
            throw new WrongModuleSettingException($moduleSetting, self::class);
        }

        $templateBlocksData = $moduleSetting->getValue();

        foreach ($templateBlocksData as $templateBlockData) {
            $templateBlockExtension = $this->mapDataToObject($templateBlockData);
            $templateBlockExtension->setShopId($shopId);
            $templateBlockExtension->setModuleId($moduleId);

            $this->templateBlockExtensionDao->add($templateBlockExtension);
        }
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongModuleSettingException
     */
    public function handleOnModuleDeactivation(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canHandle($moduleSetting)) {
            throw new WrongModuleSettingException($moduleSetting, self::class);
        }

        $this->templateBlockExtensionDao->deleteExtensions($moduleId, $shopId);
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canHandle(ModuleSetting $moduleSetting): bool
    {
        return $moduleSetting->getName() === ModuleSetting::TEMPLATE_BLOCKS;
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
