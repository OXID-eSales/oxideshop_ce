<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\TemplateBlockModuleSettingHandler;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class TemplateBlockModuleSettingHandlerTest extends TestCase
{
    public function testHandlingOnModuleActivation()
    {
        $moduleSetting = new ModuleSetting(
            ModuleSetting::TEMPLATE_BLOCKS,
            [
                [
                    'block'     => 'testBlock',
                    'position'  => '3',
                    'theme'     => 'flow_theme',
                    'template'  => 'extendedTemplatePath',
                    'file'      => 'filePath',
                ],
            ]
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModule')
            ->addSetting($moduleSetting);

        $templateBlockExtension = new TemplateBlockExtension();
        $templateBlockExtension
            ->setShopId(1)
            ->setModuleId('testModule')
            ->setName('testBlock')
            ->setThemeId('flow_theme')
            ->setPosition(3)
            ->setExtendedBlockTemplatePath('extendedTemplatePath')
            ->setFilePath('filePath');

        $templateBlockDao = $this->getTemplateBlockDaoMock();
        $templateBlockDao
            ->expects($this->once())
            ->method('add')
            ->with($templateBlockExtension);

        $settingHandler = new TemplateBlockModuleSettingHandler($templateBlockDao);
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration, 1
        );
    }

    public function testHandlingOnModuleDeactivation()
    {
        $moduleSetting = new ModuleSetting(ModuleSetting::TEMPLATE_BLOCKS, []);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModule')
            ->addSetting($moduleSetting);

        $templateBlockDao = $this->getTemplateBlockDaoMock();
        $templateBlockDao
            ->expects($this->once())
            ->method('deleteExtensions')
            ->with('testModule', 1);

        $settingHandler = new TemplateBlockModuleSettingHandler($templateBlockDao);
        $settingHandler->handleOnModuleDeactivation(
            $moduleConfiguration, 1
        );
    }

    private function getTemplateBlockDaoMock(): TemplateBlockExtensionDaoInterface
    {
        return $this
            ->getMockBuilder(TemplateBlockExtensionDaoInterface::class)
            ->getMock();
    }
}
