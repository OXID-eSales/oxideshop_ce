<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxDb;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Module\ModuleTemplateBlockRepository;

/**
 * @group module
 * @package Integration\Modules
 */
class ModuleTemplateBlockRepositoryTest extends UnitTestCase
{
    private $shopTemplateName = 'filename.tpl';
    private $activeShopId = '15';
    private $activeModuleId = 'module1';

    /**
     * setup test data
     */
    public function setUp()
    {
        parent::setUp();

        $oxid = "__test_1";
        $themeId = "";
        $blockName = "blockname1";
        $moduleBlockFileName = "contentfile1";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_2";
        $themeId = "";
        $blockName = "blockname2";
        $moduleBlockFileName = "contentfile_2";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_1_theme_1";
        $themeId = "theme_1";
        $blockName = "blockname1";
        $moduleBlockFileName = "contentfile_1_theme_1";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_2_theme_1";
        $themeId = "theme_1";
        $blockName = "blockname2";
        $moduleBlockFileName = "contentfile_2_theme_1";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);

        $oxid = "__test_1_theme_2";
        $themeId = "theme_2";
        $blockName = "blockname1";
        $moduleBlockFileName = "contentfile_1_theme_2";
        $this->insertBlock($oxid, $themeId, $blockName, $moduleBlockFileName);
    }

    /**
     * remove test data
     */
    public function tearDown()
    {
        oxDb::getDb()->Execute("delete from oxtplblocks where oxid like '__test_%'");

        parent::tearDown();
    }

    public function providerBlocksCount()
    {
        return [
            [$this->activeModuleId, $this->activeShopId, '5'],
            ['no_existing_module', $this->activeShopId, '0'],
            [$this->activeModuleId, 'not_active_shop_id', '0'],
        ];
    }

    /**
     * @param $moduleId
     * @param $shopId
     * @param $expectedBlocksCount
     *
     * @dataProvider providerBlocksCount
     */
    public function testBlocksCount($moduleId, $shopId, $expectedBlocksCount)
    {
        $templateBlockRepository = oxNew(ModuleTemplateBlockRepository::class);
        $actualBlocksCount = $templateBlockRepository->getBlocksCount([$moduleId], $shopId);

        $this->assertSame($expectedBlocksCount, $actualBlocksCount);
    }

    public function providerGetModuleInformationForDefaultTheme()
    {
        return [
            [null],
            ['not_existing_theme_id'],
        ];
    }

    /**
     * @param $themeId
     *
     * @dataProvider providerGetModuleInformationForDefaultTheme
     */
    public function testGetModuleInformationForDefaultTheme($themeId)
    {
        $templateFileName = $this->shopTemplateName;
        $activeModulesId = [$this->activeModuleId];
        $shopId = $this->activeShopId;
        $themesId = [$themeId];

        $templateBlockRepository = oxNew(ModuleTemplateBlockRepository::class);
        $actualBlockInformation = $templateBlockRepository->getBlocks($templateFileName, $activeModulesId, $shopId, $themesId);

        $expectedBlocksInformation = [
            0 => $this->formModuleBlockInformation(
                '__test_1',
                '',
                'blockname1',
                'contentfile1'
            ),
            1 => $this->formModuleBlockInformation(
                '__test_2',
                '',
                'blockname2',
                'contentfile_2'
            ),
        ];

        $this->assertEquals($expectedBlocksInformation, $actualBlockInformation);
    }

    public function testGetModuleInformationForSeveralThemes()
    {
        $templateFileName = $this->shopTemplateName;
        $activeModulesId = [$this->activeModuleId];
        $shopId = $this->activeShopId;
        $activeThemesId = ['theme_1', 'theme_2'];

        $templateBlockRepository = oxNew(ModuleTemplateBlockRepository::class);
        $actualBlockInformation = $templateBlockRepository->getBlocks($templateFileName, $activeModulesId, $shopId, $activeThemesId);

        $expectedBlocksInformation = [
            0 => $this->formModuleBlockInformation(
                '__test_1',
                '',
                'blockname1',
                'contentfile1'
            ),
            1 => $this->formModuleBlockInformation(
                '__test_2',
                '',
                'blockname2',
                'contentfile_2'
            ),

            2 => $this->formModuleBlockInformation(
                '__test_1_theme_1',
                'theme_1',
                'blockname1',
                'contentfile_1_theme_1'
            ),
            3 => $this->formModuleBlockInformation(
                '__test_2_theme_1',
                'theme_1',
                'blockname2',
                'contentfile_2_theme_1'
            ),
            4 => $this->formModuleBlockInformation(
                '__test_1_theme_2',
                'theme_2',
                'blockname1',
                'contentfile_1_theme_2'
            ),
        ];

        $this->assertEquals($expectedBlocksInformation, $actualBlockInformation);
    }

    public function providerGetModuleInformationWhenSomeParameterIsWrong()
    {
        $existingTemplateFileName = $this->shopTemplateName;
        $existingActiveModulesId = [$this->activeModuleId];
        $existingShopId = $this->activeShopId;

        $notExistingTemplateFileName = 'not_existing_filename.tpl';
        $notExistingActiveModulesId = ['not_active_module1'];
        $notExistingShopId = '150';

        return [
            [$notExistingTemplateFileName, $existingActiveModulesId, $existingShopId],
            [$existingTemplateFileName, $notExistingActiveModulesId, $existingShopId],
            [$existingTemplateFileName, $existingActiveModulesId, $notExistingShopId],
        ];
    }

    /**
     * @dataProvider providerGetModuleInformationWhenSomeParameterIsWrong
     */
    public function testGetModuleInformationWhenSomeParameterIsWrong($templateFileName, $activeModulesId, $shopId)
    {
        $templateBlockRepository = oxNew(ModuleTemplateBlockRepository::class);
        $actualBlockInformation = $templateBlockRepository->getBlocks($templateFileName, $activeModulesId, $shopId);

        $this->assertEquals([], $actualBlockInformation);
    }

    /**
     * @param $oxid
     * @param $themeId
     * @param $blockName
     * @param $moduleBlockFileName
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

    /**
     * @param $oxid
     * @param $themeId
     * @param $blockName
     * @param $moduleBlockFileName
     *
     * @return array
     */
    private function formModuleBlockInformation($oxid, $themeId, $blockName, $moduleBlockFileName)
    {
        return [
            'OXID' => $oxid,
            'OXACTIVE' => '1',
            'OXSHOPID' => $this->activeShopId,
            'OXTHEME' => $themeId,
            'OXTEMPLATE' => $this->shopTemplateName,
            'OXBLOCKNAME' => $blockName,
            'OXPOS' => '1',
            'OXFILE' => $moduleBlockFileName,
            'OXMODULE' => $this->activeModuleId,
            'OXTIMESTAMP' => '2016-04-14 08:09:00'
        ];
    }
}
