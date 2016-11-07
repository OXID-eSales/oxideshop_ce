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
namespace Unit\Core\Smarty;

use \stdClass;
use \oxException;
use \oxRegistry;
use \oxTestModules;

$filePath = oxRegistry::getConfig()->getConfigParam( 'sShopDir' ).'Core/Smarty/Plugin/prefilter.oxblock.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/prefilter.oxblock.php';
}

class PluginSmartyOxBlockTest extends \OxidTestCase
{
    /**
     * test case for smarty 2 with no dbg output
     *
     * @return null
     */
    public function testSmarty2NoDebug()
    {
        $oSmartyCompiler = new stdClass();
        $oSmartyCompiler->_current_file = 'testfile.tpl';

        $this->getConfig()->setConfigParam('blDebugTemplateBlocks', false);

        $oUtilsView = $this->getMock('oxUtilsView', array('getTemplateBlocks'));
        $oUtilsView->expects($this->once())->method('getTemplateBlocks')
                ->with($this->equalTo('testfile.tpl'))
                ->will(
                    $this->returnValue(
                        array (
                            'test1' => array('block content 1 [{block name="test1_2"}]orig test1_2[{/block}]'),
                            'test1_2' => array('<<[{$smarty.block.parent}]>>'),
                            'test2' => array('[[[{$smarty.block.parent}]]]'),
                            'test3' => array('<prep>[{$smarty.block.parent}]', '[{$smarty.block.parent}]<app>'),
                        )
                    )
                );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $this->assertEquals(
            'blocknotset [[<b2>block content 1 <<orig test1_2>></b2>]]<prep><b3/><app>',
            smarty_prefilter_oxblock(
                '[{block name="blocknotset"}]blocknotset [{/block}][{block name="test2"}]<b2>[{block name="test1"}]<b1/>[{/block}]</b2>[{/block}][{block name="test3"}]<b3/>[{/block}]',
                $oSmartyCompiler
            )
        );
    }

    /**
     * test case for smarty 2 with no dbg output: catch endless loop and error it
     *
     * @return null
     */
    public function testSmarty2NoDebugErrorHandling()
    {
        $oSmartyCompiler = $this->getMock('stdclass', array('_syntax_error'));
        $oSmartyCompiler->expects($this->once())->method('_syntax_error')
                ->with(
                        $this->equalTo('block tags mismatch (or there are more than 500 blocks in one file).'),
                        $this->equalTo(E_USER_ERROR),
                        $this->equalTo(realpath($this->getProfilterPluginPath())),
                        $this->greaterThan(75)
                )
                ->will($this->throwException(new oxException('ok')));

        $oSmartyCompiler->_current_file = 'testfile.tpl';

        $this->getConfig()->setConfigParam('blDebugTemplateBlocks', false);

        $oUtilsView = $this->getMock('oxUtilsView', array('getTemplateBlocks'));
        $oUtilsView->expects($this->once())->method('getTemplateBlocks')
                ->with($this->equalTo('testfile.tpl'))
                ->will(
                    $this->returnValue(
                        array (
                            'test1' => array('[{block name="test1"}]looping[{/block}]'),
                        )
                    )
                );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        try {
            smarty_prefilter_oxblock(
                '[{block name="test1"}]<b1/>[{/block}]',
                $oSmartyCompiler
            );
        } catch (\OxidEsales\EshopCommunity\Core\Exception\StandardException $e) {
            $this->assertEquals('ok', $e->getMessage());
        }
    }

    /**
     * test case for smarty 2 with dbg output
     *
     * @return null
     */
    public function testSmarty2Debug()
    {
        $oSmartyCompiler = new stdClass();
        $oSmartyCompiler->_current_file = 'testfile.tpl';

        $this->getConfig()->setConfigParam('blDebugTemplateBlocks', true);

        $oUtilsView = $this->getMock('oxUtilsView', array('getTemplateBlocks'));
        $oUtilsView->expects($this->once())->method('getTemplateBlocks')
                ->with($this->equalTo('testfile.tpl'))
                ->will(
                    $this->returnValue(
                        array (
                            'test1' => array('block content 1 [{block name="test1_2"}]orig test1_2[{/block}]'),
                            'test1_2' => array('<<[{$smarty.block.parent}]>>'),
                            'test2' => array('[[[{$smarty.block.parent}]]]'),
                            'test3' => array('<prep>[{$smarty.block.parent}]', '[{$smarty.block.parent}]<app>'),
                        )
                    )
                );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $this->assertEquals(
            '[{capture name="_dbg_blocks"}]'.
                'blocknotset '.
            '[{/capture}]'.
            '[{math equation="rand()" assign="_dbg_block_idr1"}]'.
            '[{math equation="rand()" assign="_dbg_block_idr2"}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_1838478057_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;blocknotset">'.
                '[{$smarty.capture._dbg_blocks}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_1838478057_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.

            '[{capture name="_dbg_blocks"}]'.
                '[[<b2>'.
                '[{capture name="_dbg_blocks"}]'.
                    'block content 1 '.
                    '[{capture name="_dbg_blocks"}]'.
                       '<<orig test1_2>>'.
                    '[{/capture}]'.
                    '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
                    '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_3673087697_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test1_2">'.
                    '[{$smarty.capture._dbg_blocks}]'.''.
                    '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_3673087697_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.
                '[{/capture}]'.
                '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
                '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_3171795629_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test1">'.
                '[{$smarty.capture._dbg_blocks}]'.
                '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_3171795629_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.

                '</b2>]]'.
            '[{/capture}]'.
            '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_604279575_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test2">'.
            '[{$smarty.capture._dbg_blocks}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_604279575_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.

            '[{capture name="_dbg_blocks"}]'.
                '<prep><b3/><app>'.
            '[{/capture}]'.
            '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_1392747393_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test3">'.
            '[{$smarty.capture._dbg_blocks}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_1392747393_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">',
            smarty_prefilter_oxblock(
                '[{block name="blocknotset"}]blocknotset [{/block}][{block name="test2"}]<b2>[{block name="test1"}]<b1/>[{/block}]</b2>[{/block}][{block name="test3"}]<b3/>[{/block}]',
                $oSmartyCompiler
            )
        );
    }




    /**
     * test case for smarty 3 with no dbg output
     *
     * @return null
     */
    public function testSmarty3NoDebug()
    {
        $oSmartyCompiler = new stdClass();
        $oSmartyCompiler->_current_file = 'testfile.tpl';
        $oSmartyCompiler->_version = 'Smarty3z';

        $this->getConfig()->setConfigParam('blDebugTemplateBlocks', false);

        $oUtilsView = $this->getMock('oxUtilsView', array('getTemplateBlocks'));
        $oUtilsView->expects($this->once())->method('getTemplateBlocks')
                ->with($this->equalTo('testfile.tpl'))
                ->will(
                    $this->returnValue(
                        array (
                            'test1' => array('block content 1 [{block name="test1_2"}]orig test1_2[{/block}]'),
                            'test1_2' => array('<<[{$smarty.block.parent}]>>'),
                            'test2' => array('[[[{$smarty.block.parent}]]]'),
                            'test3' => array('<prep>[{$smarty.block.parent}]', '[{$smarty.block.parent}]<app>'),
                        )
                    )
                );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $this->assertEquals(
            '[{block name="blocknotset"}]'.
                'blocknotset '.
            '[{/block}]'.
            '[{block name="test2"}]'.
                '[[<b2>'.
                '[{block name="test1"}]'.
                    'block content 1 '.
                    '[{block name="test1_2"}]'.
                        '<<orig test1_2>>'.
                    '[{/block}]'.
                '[{/block}]'.
                '</b2>]]'.
            '[{/block}]'.
            '[{block name="test3"}]'.
                '<prep><b3/><app>'.
            '[{/block}]',
            smarty_prefilter_oxblock(
                '[{block name="blocknotset"}]blocknotset [{/block}][{block name="test2"}]<b2>[{block name="test1"}]<b1/>[{/block}]</b2>[{/block}][{block name="test3"}]<b3/>[{/block}]',
                $oSmartyCompiler
            )
        );
    }

    /**
     * test case for smarty 3 with no dbg output: catch endless loop and error it
     *
     * @return null
     */
    public function testSmarty3NoDebugErrorHandling()
    {
        $oSmartyCompiler = $this->getMock('stdclass', array('trigger_error'));
        $oSmartyCompiler->expects($this->once())->method('trigger_error')
                ->with(
                        $this->equalTo('block tags mismatch (or there are more than 500 blocks in one file).'),
                        $this->equalTo(E_USER_ERROR)
                )
                ->will($this->throwException(new oxException('ok')));

        $oSmartyCompiler->_current_file = 'testfile.tpl';
        $oSmartyCompiler->_version = 'Smarty3z';

        $this->getConfig()->setConfigParam('blDebugTemplateBlocks', false);

        $oUtilsView = $this->getMock('oxUtilsView', array('getTemplateBlocks'));
        $oUtilsView->expects($this->once())->method('getTemplateBlocks')
                ->with($this->equalTo('testfile.tpl'))
                ->will(
                    $this->returnValue(
                        array (
                            'test1' => array('[{block name="test1"}]looping[{/block}]'),
                        )
                    )
                );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        try {
            smarty_prefilter_oxblock(
                '[{block name="test1"}]<b1/>[{/block}]',
                $oSmartyCompiler
            );
        } catch (\OxidEsales\EshopCommunity\Core\Exception\StandardException $e) {
            $this->assertEquals('ok', $e->getMessage());
        }
    }



    /**
     * test case for smarty 3 with dbg output
     *
     * @return null
     */
    public function testSmarty3Debug()
    {
        $oSmartyCompiler = new stdClass();
        $oSmartyCompiler->_current_file = 'testfile.tpl';
        $oSmartyCompiler->_version = 'Smarty3z';

        $this->getConfig()->setConfigParam('blDebugTemplateBlocks', true);

        $oUtilsView = $this->getMock('oxUtilsView', array('getTemplateBlocks'));
        $oUtilsView->expects($this->once())->method('getTemplateBlocks')
                ->with($this->equalTo('testfile.tpl'))
                ->will(
                    $this->returnValue(
                        array (
                            'test1' => array('block content 1 [{block name="test1_2"}]orig test1_2[{/block}]'),
                            'test1_2' => array('<<[{$smarty.block.parent}]>>'),
                            'test2' => array('[[[{$smarty.block.parent}]]]'),
                            'test3' => array('<prep>[{$smarty.block.parent}]', '[{$smarty.block.parent}]<app>'),
                        )
                    )
                );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $this->assertEquals(
            '[{capture name="_dbg_blocks"}]'.
                '[{block name="blocknotset"}]'.
                   'blocknotset '.
                '[{/block}]'.
            '[{/capture}]'.
            '[{math equation="rand()" assign="_dbg_block_idr1"}]'.
            '[{math equation="rand()" assign="_dbg_block_idr2"}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_1838478057_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;blocknotset">'.
                '[{$smarty.capture._dbg_blocks}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_1838478057_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.

            '[{capture name="_dbg_blocks"}]'.
                '[{block name="test2"}]'.
                    '[[<b2>'.
                    '[{capture name="_dbg_blocks"}]'.
                        '[{block name="test1"}]'.
                            'block content 1 '.
                            '[{capture name="_dbg_blocks"}]'.
                               '[{block name="test1_2"}]'.
                                    '<<orig test1_2>>'.
                                '[{/block}]'.
                            '[{/capture}]'.
                            '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
                            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_3673087697_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test1_2">'.
                            '[{$smarty.capture._dbg_blocks}]'.''.
                            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_3673087697_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.
                        '[{/block}]'.
                    '[{/capture}]'.
                    '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
                    '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_3171795629_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test1">'.
                    '[{$smarty.capture._dbg_blocks}]'.
                    '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_3171795629_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.

                    '</b2>]]'.
                '[{/block}]'.
            '[{/capture}]'.
            '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_604279575_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test2">'.
            '[{$smarty.capture._dbg_blocks}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_604279575_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">'.

            '[{capture name="_dbg_blocks"}]'.
                '[{block name="test3"}]'.
                    '<prep><b3/><app>'.
                '[{/block}]'.
            '[{/capture}]'.
            '[{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="block_1392747393_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]" title="testfile.tpl-&gt;test3">'.
            '[{$smarty.capture._dbg_blocks}]'.
            '<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="block_1392747393_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]">',
            smarty_prefilter_oxblock(
                '[{block name="blocknotset"}]blocknotset [{/block}][{block name="test2"}]<b2>[{block name="test1"}]<b1/>[{/block}]</b2>[{/block}][{block name="test3"}]<b3/>[{/block}]',
                $oSmartyCompiler
            )
        );
    }

    /**
     * @return string
     */
    protected function getProfilterPluginPath()
    {
        $filePath = $this->getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/prefilter.oxblock.php';
        if (!file_exists($filePath)) {
            $filePath = dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/prefilter.oxblock.php';
            return $filePath;
        }
        return $filePath;
    }

}
