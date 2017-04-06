<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use oxException;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxUtilsView;
use PHPUnit_Framework_MockObject_MockObject;

class oxUtilsViewGetTemplateBlocksTest extends UnitTestCase
{
    /**
     * setup test data
     */
    public function setUp()
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
    public function  tearDown()
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
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopId', 'init'));
        $config->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('15'));
        $aInfo = array('module1' => 'module1', 'module2' => 'module2');

        /** @var oxException|PHPUnit_Framework_MockObject_MockObject $exception */
        $exception = $this->getMock(\OxidEsales\Eshop\Core\Exception\StandardException::class, array('debugOut'));
        $exception->expects($this->once())->method('debugOut');

        /** @var oxUtilsView|PHPUnit_Framework_MockObject_MockObject $utilsView */
        $utilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('getConfig', '_getActiveModuleInfo', '_getTemplateBlock'));
        $utilsView->expects($this->any())->method('getConfig')->will($this->returnValue($config));
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
