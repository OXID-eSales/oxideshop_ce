<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use oxException;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxUtilsView;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class oxUtilsViewGetTemplateBlocksTest extends UnitTestCase
{
    /**
     * setup test data
     */
    public function setup(): void
    {
        parent::setUp();

        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_1',
                                                   '1',
                                                   '15',
                                                   'filename.tpl',
                                                   'blockname1',
                                                   1,
                                                   'contentfile1',
                                                   'module1'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_2',
                                                   '1',
                                                   '15',
                                                   'filename.tpl',
                                                   'blockname2',
                                                   2,
                                                   'contentfile2',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_3',
                                                   '1',
                                                   '15',
                                                   'filename.tpl',
                                                   'blockname2',
                                                   0,
                                                   'contentfile3',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_3_2',
                                                   '1',
                                                   '15',
                                                   'not_active_theme',
                                                   'filename.tpl',
                                                   'blockname2',
                                                   1,
                                                   'contentfile2_not_active_theme',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_3_3',
                                                   '1',
                                                   '15',
                                                   'active_theme',
                                                   'filename.tpl',
                                                   'blockname2',
                                                   1,
                                                   'contentfile2_active_theme',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_ap_1',
                                                   '1',
                                                   '15',
                                                   'active_theme_parent',
                                                   'filename2.tpl',
                                                   'blockname_ap_1',
                                                   1,
                                                   'contentfile_ap_active_theme',
                                                   'module2'
                                                )"
        );

        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_apat_1',
                                                   '1',
                                                   '15',
                                                   'active_theme',
                                                   'filename3.tpl',
                                                   'blockname_ap_1',
                                                   1,
                                                   'contentfile_apat_block1',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_apat_2',
                                                   '1',
                                                   '15',
                                                   'active_theme',
                                                   'filename3.tpl',
                                                   'blockname_ap_2',
                                                   1,
                                                   'contentfile_apat_block2',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_apat_2_parent',
                                                   '1',
                                                   '15',
                                                   'active_theme_parent',
                                                   'filename3.tpl',
                                                   'blockname_ap_2',
                                                   1,
                                                   'contentfile_apat_block2_parent',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_apat_3_parent',
                                                   '1',
                                                   '15',
                                                   'active_theme_parent',
                                                   'filename3.tpl',
                                                   'blockname_ap_3',
                                                   1,
                                                   'contentfile_apat_block3_parent',
                                                   'module2'
                                                )"
        );

        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_apct_1',
                                                   '1',
                                                   '15',
                                                   '',
                                                   'filename4.tpl',
                                                   'blockname_ap_1',
                                                   1,
                                                   'contentfile_apat_block1_default',
                                                   'module2'
                                                )"
        );
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTHEME,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_apct_1_parent',
                                                   '1',
                                                   '15',
                                                   'active_theme',
                                                   'filename4.tpl',
                                                   'blockname_ap_1',
                                                   1,
                                                   'contentfile_apat_block1_custom',
                                                   'module2'
                                                )"
        );

        // one non active - to be sure it is not loaded
        oxDb::getDb()->Execute(
            "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                   'test_4',
                                                   '0',
                                                   '15',
                                                   'filename.tpl',
                                                   'blockname3',
                                                   3,
                                                   'contentfile3',
                                                   'module2'
                                                )"
        );
    }

    /**
     * remove test data
     */
    public function tearDown(): void
    {
        if (strpos($this->getName(), 'testGetTemplateBlocks') === 0) {
            oxDb::getDb()->Execute("delete from oxtplblocks where oxid like 'test_%'");
        }
        parent::tearDown();
    }

    /**
     * exception log test
     */
    public function testGetTemplateBlocksLogsExceptions()
    {
        $logger = $this->getMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        Registry::set('logger', $logger);

        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopId', 'init'));
        $config->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('15'));
        $aInfo = array('module1' => 'module1', 'module2' => 'module2');

        /** @var oxException|PHPUnit\Framework\MockObject\MockObject $exception */
        $exception = $this->getMockBuilder(\OxidEsales\Eshop\Core\Exception\StandardException::class)->getMock();

        /** @var oxUtilsView|PHPUnit\Framework\MockObject\MockObject $utilsView */
        $utilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array( '_getActiveModuleInfo', '_getTemplateBlock'));
        Registry::set(Config::class, $config);
        $utilsView->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));

        $utilsView->expects($this->any())->method('_getTemplateBlock')->will($this->returnCallback(
            function ($param1, $param2) use ($exception) {
                if ($param1 == "module2") {
                    if ($param2 == "contentfile3") {
                        throw $exception;
                    } else {
                        return "content2";
                    }
                }

                return "content1";
            }
        ));

        $this->assertEquals(
            array(
                'blockname1' => array(
                    'content1',
                ),
                'blockname2' => array(
                    'content2',
                ),
            ),
            $utilsView->getTemplateBlocks('filename.tpl')
        );
    }
}
