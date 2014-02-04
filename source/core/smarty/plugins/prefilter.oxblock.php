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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */


/**
 * Check if this template is registered for block extends. If yes, then collect
 * the extended blocks and replace them into the compile result of this template.
 * Else, replace block tags to smarty comments.
 *
 * @param string $sSource          source content
 * @param object &$oSmartyCompiler smarty compiler instance
 *
 * @return string
 */
function smarty_prefilter_oxblock($sSource, &$oSmartyCompiler)
{
    $blUseSmarty3 = false;
    if (strpos($oSmartyCompiler->_version, 'Smarty3') === 0) {
        $blUseSmarty3 = true;
    }
    $blDebugTemplateBlocks = (bool)oxRegistry::getConfig()->getConfigParam('blDebugTemplateBlocks');

    $aBlocks = oxRegistry::get("oxUtilsView")->getTemplateBlocks($oSmartyCompiler->_current_file);

    $iLimit = 500;

    while (--$iLimit && preg_match('/\[\{\s*block\s+name\s*=\s*([\'"])([a-z0-9_]+)\1\s*\}\](.*?)\[\{\s*\/block\s*\}\]/is', $sSource, $m)) {
        $sBlock = $m[0];
        $sBlockName = $m[2];
        $sBlockContent = $m[3];
        if (preg_match('/^.+(\[\{\s*block\s+name\s*=\s*([\'"])([a-z0-9_]+)\2\s*\}\](.*?)\[\{\s*\/block\s*\}\])$/is', $sBlock, $m)) {
            // shift to (deepest) nested tag opening
            $sBlock = $m[1];
            $sBlockName = $m[3];
            $sBlockContent = $m[4];
        }
        $sPrepend = '';
        $sAppend  = '';
        if ($blUseSmarty3) {
            $sPrepend = '[{__smartyblock__ name="'.$sBlockName.'"}]'.$sPrepend;
            $sAppend .= '[{/__smartyblock__}]';
        }
        if ($blDebugTemplateBlocks) {
            $sTplDir = trim(oxRegistry::getConfig()->getConfigParam('_sTemplateDir'), '/\\');
            $sFile = str_replace(array('\\', '//'), '/', $oSmartyCompiler->_current_file);
            if (preg_match('@/'.preg_quote($sTplDir, '@').'/(.*)$@', $sFile, $m)) {
                $sFile = $m[1];
            }

            $sDbgName = $sFile.'-&gt;'.$sBlockName;
            $sPrepend = '[{capture name="_dbg_blocks"}]'.$sPrepend;
            $sDbgId = 'block_'.sprintf("%u", crc32($sDbgName)).'_[{$_dbg_block_idr1}][{$_dbg_block_idr2}]';
            $sAppend .= '[{/capture}][{math equation="rand()" assign="_dbg_block_idr1"}][{math equation="rand()" assign="_dbg_block_idr2"}]'
                       .'<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksStart" id="'.$sDbgId.'" title="'.$sDbgName.'">'
                       .'[{$smarty.capture._dbg_blocks}]'
                       .'<hr style="visibility:hidden;height:0;margin:0;padding:0;border:0;line-height:0;font-size:0;" class="debugBlocksEnd" title="'.$sDbgId.'">';
        }
        if (!isset($aBlocks[$sBlockName]) || !is_array($aBlocks[$sBlockName])) {
            // block is unused, just use its content
            $sSource = str_replace($sBlock, $sPrepend.$sBlockContent.$sAppend, $sSource);
        } else {
            // go through the replacement array and fill in parent values
            // specified by [{$smarty.block.parent}] tag
            $sCurrBlock = $sBlockContent;
            foreach ($aBlocks[$sBlockName] as $sOverBlock) {
                $sCurrBlock = preg_replace('/\[\{\s*\$smarty\.block\.parent\s*\}\]/i', $sCurrBlock, $sOverBlock);
            }
            $sSource = str_replace($sBlock, $sPrepend.$sCurrBlock.$sAppend, $sSource);
        }
    }
    if (!$iLimit) {
        if ($blUseSmarty3) {
            $oSmartyCompiler->trigger_error("block tags mismatch (or there are more than 500 blocks in one file).", E_USER_ERROR);
        } else {
            $oSmartyCompiler->_syntax_error("block tags mismatch (or there are more than 500 blocks in one file).", E_USER_ERROR, __FILE__, __LINE__);
        }
    }
    if ($blUseSmarty3) {
        $sSource = str_replace('__smartyblock__', 'block', $sSource);
    }
    return $sSource;
}

