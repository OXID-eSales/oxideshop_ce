<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxDb;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\Facts\Facts;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxUtilsView;
use Webmozart\PathUtil\Path;

/**
 * @group module
 * @package Integration\Modules
 */
class ModuleTemplateBlocksTest extends UnitTestCase
{
    private $shopTemplateName = 'filename.tpl';
    private $activeShopId = '1';
    private $activeModuleId = 'module1';

    /**
     * remove test data
     */
    public function tearDown(): void
    {
        oxDb::getDb()->Execute("delete from oxtplblocks where oxid like '__test_%'");

        parent::tearDown();
    }

    public function providerGetTemplateBlocksForAllActiveModules()
    {
        $blocksForDefaultTheme = [
            'blockname1' => [
                'block template content'
            ],
            'blockname2' => [
                'block template content 2'
            ],
            'blockname3' => [
                'block template content 3'
            ],
        ];

        $blocksForTheme_1 = [
            'blockname1' => [
                'block template content 1 theme 1'
            ],
            'blockname2' => [
                'block template content 2 theme 1'
            ],
            'blockname3' => [
                'block template content 3'
            ],
        ];

        $blocksForTheme_2 = [
            'blockname1' => [
                'block template content 1 theme 2'
            ],
            'blockname2' => [
                'block template content 2'
            ],
            'blockname3' => [
                'block template content 3'
            ],
        ];

        $blocksForTheme_1_2 = [
            'blockname1' => [
                'block template content 1 theme 2'
            ],
            'blockname2' => [
                'block template content 2 theme 1'
            ],
            'blockname3' => [
                'block template content 3'
            ],
        ];

        $shopInAdminMode = true;
        $shopInFrontendMode = !$shopInAdminMode;

        return [
            ['theme_without_blocks', null, $blocksForDefaultTheme, $shopInFrontendMode],
            ['theme_1', null, $blocksForTheme_1, $shopInFrontendMode],
            ['theme_2', null, $blocksForTheme_2, $shopInFrontendMode],
            ['theme_1', 'theme_2', $blocksForTheme_1_2, $shopInFrontendMode],
            ['theme_1', 'theme_2', $blocksForDefaultTheme, $shopInAdminMode],
        ];
    }

    /**
     * @param string $themeId
     * @param string $customThemeId
     * @param string $expectedBlocksWithContent
     * @param bool   $isAdminMode
     *
     * @dataProvider providerGetTemplateBlocksForAllActiveModules
     */
    public function testGetTemplateBlocksForAllActiveModulesDependentlyFromActiveTheme($themeId, $customThemeId, $expectedBlocksWithContent, $isAdminMode)
    {
        $this->setAdminMode($isAdminMode);

        $this->prepareTestModuleConfiguration();
        $this->insertTemplateBlocks();

        $blocksGetter = $this->getUtilsView($themeId, $customThemeId);
        $actualBlocksWithContent = $blocksGetter->getTemplateBlocks($this->shopTemplateName);

        $this->assertEquals($expectedBlocksWithContent, $actualBlocksWithContent);
    }

    /**
     * @param string $themeId
     * @param string $customThemeId
     * @param string $expectedBlocksWithContent
     * @param bool   $isAdminMode
     *
     * @dataProvider providerGetTemplateBlocksForAllActiveModules
     */
    public function testGetTemplateBlocksWhenNoBlocksExist($themeId, $customThemeId, $expectedBlocksWithContent, $isAdminMode)
    {
        $this->setAdminMode($isAdminMode);

        $blocksGetter = $this->getUtilsView($themeId, $customThemeId);
        $actualBlocksWithContent = $blocksGetter->getTemplateBlocks($this->shopTemplateName);

        $this->assertEquals([], $actualBlocksWithContent);
    }

    /**
     * Return testable object.
     *
     * @param string $themeId
     * @param string $customThemeId
     *
     * @return oxUtilsView
     */
    private function getUtilsView($themeId, $customThemeId)
    {
        $shopPath = implode(DIRECTORY_SEPARATOR, [__DIR__, 'TestData', 'shop']) . DIRECTORY_SEPARATOR;

        $this->setShopId(1);
        $this->setConfigParam('sShopDir', $shopPath);
        $this->setConfigParam('sTheme', $themeId);
        $this->setConfigParam('sCustomTheme', $customThemeId);
        $this->setConfigParam('aModulePaths', [$this->activeModuleId => 'oe/testTemplateBlockModuleId']);

        return oxNew('oxUtilsView');
    }

    /**
     * Prepare database: insert needed module template blocks.
     */
    private function insertTemplateBlocks()
    {
        $oxid = "__test_1";
        $themeId = "";
        $blockName = "blockname1";
        $moduleBlockFileName = "blocks/blocktemplate.tpl";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_2";
        $themeId = "";
        $blockName = "blockname2";
        $moduleBlockFileName = "blocks/blocktemplate2.tpl";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_3";
        $themeId = "";
        $blockName = "blockname3";
        $moduleBlockFileName = "blocks/blocktemplate3.tpl";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_1_theme_1";
        $themeId = "theme_1";
        $blockName = "blockname1";
        $moduleBlockFileName = "blocks/blocktemplate1_theme1.tpl";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_2_theme_1";
        $themeId = "theme_1";
        $blockName = "blockname2";
        $moduleBlockFileName = "blocks/blocktemplate2_theme1.tpl";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_1_theme_2";
        $themeId = "theme_2";
        $blockName = "blockname1";
        $moduleBlockFileName = "blocks/blocktemplate1_theme2.tpl";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);
    }

    /**
     * Insert module template block to database.
     *
     * @param string $oxid
     * @param string $themeId
     * @param string $blockName
     * @param string $moduleBlockFileName
     */
    private function insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName)
    {
        oxDb::getDb()->Execute("
            INSERT INTO oxtplblocks SET
            OXID = '{$oxid}',
            OXACTIVE = '1',
            OXSHOPID = '{$this->activeShopId}',
            OXTEMPLATE = '{$this->shopTemplateName}',
            OXTHEME = '{$themeId}',
            OXBLOCKNAME = '{$blockName}',
            OXPOS = 1,
            OXFILE = '{$moduleBlockFileName}',
            OXMODULE = '{$this->activeModuleId}',
            OXTIMESTAMP = '2016-04-14 08:09:00'
        ");
    }

    private function prepareTestModuleConfiguration(): void
    {
        $fullModulePath = __DIR__ . '/TestData/shop/modules/oe/testTemplateBlockModuleId/';

        $relativeModulePath = Path::makeRelative(
            $fullModulePath,
            (new Facts())->getShopRootPath()
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($this->activeModuleId)
            ->setPath('somePath')
            ->setModuleSource($relativeModulePath);

        ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleConfigurationDaoBridgeInterface::class)
            ->save($moduleConfiguration);
    }
}
