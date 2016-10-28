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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;

/**
 * Admin selectlist list manager.
 *
 * @internal This class should not be directly extended, instead of it oxAdminDetails class should be used.
 */
class AdminDetailsController extends \oxAdminView
{
    /**
     * Global editor object.
     *
     * @var object
     */
    protected $_oEditor = null;

    /**
     * Calls parent::render, sets admin help url.
     *
     * @return string
     */
    public function render()
    {
        $sReturn = parent::render();

        // generate help link
        $myConfig = $this->getConfig();
        $sDir = $myConfig->getConfigParam('sShopDir') . '/documentation/admin';
        if (is_dir($sDir)) {
            $sDir = $myConfig->getConfigParam('sShopURL') . 'documentation/admin';
        } else {
            $languageId = $this->getDocumentationLanguageId();
            $oShop = $this->_getEditShop(oxRegistry::getSession()->getVariable('actshop'));
            $sDir = "http://docu.oxid-esales.com/PE/{$oShop->oxshops__oxversion->value}/" . $languageId . '/admin';
        }

        $this->_aViewData['sHelpURL'] = $sDir;

        return $sReturn;
    }

    /**
     * Get language id for documentation by current language id.
     */
    protected function getDocumentationLanguageId()
    {
        $language = oxRegistry::getLang();
        $languageAbbr = $language->getLanguageAbbr($language->getTplLanguage());

        return $languageAbbr === "de" ? 0 : 1;
    }

    /**
     * Returns string which must be edited by editor.
     *
     * @param oxbase $oObject object whifh field will be used for editing
     * @param string $sField  name of editable field
     *
     * @return string
     */
    protected function _getEditValue($oObject, $sField)
    {
        $sEditObjectValue = '';
        if ($oObject && $sField && isset($oObject->$sField)) {
            if ($oObject->$sField instanceof \OxidEsales\EshopCommunity\Core\Field) {
                $sEditObjectValue = $oObject->$sField->getRawValue();
            } else {
                $sEditObjectValue = $oObject->$sField->value;
            }

            $sEditObjectValue = $this->_processEditValue($sEditObjectValue);
            $oObject->$sField = new oxField($sEditObjectValue, oxField::T_RAW);
        }

        return $sEditObjectValue;
    }

    /**
     * Processes edit value.
     *
     * @param string $sValue string to process
     *
     * @return string
     */
    protected function _processEditValue($sValue)
    {
        // A. replace ONLY if long description is not processed by smarty, or users will not be able to
        // store smarty tags ([{$shop->currenthomedir}]/[{$oViewConf->getCurrentHomeDir()}]) in long
        // descriptions, which are filled dynamically
        if (!$this->getConfig()->getConfigParam('bl_perfParseLongDescinSmarty')) {
            $aReplace = array('[{$shop->currenthomedir}]', '[{$oViewConf->getCurrentHomeDir()}]');
            $sValue = str_replace($aReplace, $this->getConfig()->getCurrentShopURL(false), $sValue);
        }

        return $sValue;
    }

    /**
     * Returns textarea filled with text to edit.
     *
     * @param int    $iWidth  editor width
     * @param int    $iHeight editor height
     * @param object $oObject object passed to editor
     * @param string $sField  object field which content is passed to editor
     *
     * @return string
     */
    protected function _getPlainEditor($iWidth, $iHeight, $oObject, $sField)
    {
        $sEditObjectValue = $this->_getEditValue($oObject, $sField);

        if (strpos($iWidth, '%') === false) {
            $iWidth .= 'px';
        }
        if (strpos($iHeight, '%') === false) {
            $iHeight .= 'px';
        }

        return "<textarea id='editor_{$sField}' style='width:{$iWidth}; height:{$iHeight};'>{$sEditObjectValue}</textarea>";
    }

    /**
     * Generates Text editor html code.
     *
     * @param int    $iWidth      editor width
     * @param int    $iHeight     editor height
     * @param object $oObject     object passed to editor
     * @param string $sField      object field which content is passed to editor
     * @param string $sStylesheet stylesheet to use in editor
     *
     * @return string Editor output
     */
    protected function _generateTextEditor($iWidth, $iHeight, $oObject, $sField, $sStylesheet = null)
    {
        $sEditorHtml = $this->_getPlainEditor($iWidth, $iHeight, $oObject, $sField);

        return $sEditorHtml;
    }

    /**
     * Resets number of articles in current shop categories.
     */
    public function resetNrOfCatArticles()
    {
        // resetting categories article count cache
        $this->resetContentCache();
    }

    /**
     * Resets number of articles in current shop vendors.
     */
    public function resetNrOfVendorArticles()
    {
        // resetting vendors cache
        $this->resetContentCache();
    }

    /**
     * Resets number of articles in current shop manufacturers.
     */
    public function resetNrOfManufacturerArticles()
    {
        // resetting manufacturers cache
        $this->resetContentCache();
    }

    /**
     * Function creates category tree for select list used in "Category main", "Article extend" etc.
     *
     * @param string $sTplVarName     name of template variable where is stored category tree
     * @param string $sEditCatId      ID of category witch we are editing
     * @param bool   $blForceNonCache Set to true to disable caching
     * @param int    $iTreeShopId     tree shop id
     *
     * @return string
     */
    protected function _createCategoryTree($sTplVarName, $sEditCatId = '', $blForceNonCache = false, $iTreeShopId = null)
    {
        // caching category tree, to load it once, not many times
        if (!isset($this->oCatTree) || $blForceNonCache) {
            $this->oCatTree = oxNew('oxCategoryList');
            $this->oCatTree->setShopID($iTreeShopId);

            // setting language
            $oBase = $this->oCatTree->getBaseObject();
            $oBase->setLanguage($this->_iEditLang);

            $this->oCatTree->loadList();
        }

        // copying tree
        $oCatTree = $this->oCatTree;
        //removing current category
        if ($sEditCatId && isset($oCatTree[$sEditCatId])) {
            unset($oCatTree[$sEditCatId]);
        }

        // add first fake category for not assigned articles
        $oRoot = oxNew('oxCategory');
        $oRoot->oxcategories__oxtitle = new oxField('--');

        $oCatTree->assign(array_merge(array('' => $oRoot), $oCatTree->getArray()));

        // passing to view
        $this->_aViewData[$sTplVarName] = $oCatTree;

        return $oCatTree;
    }

    /**
     * Function creates category tree for select list used in "Category main", "Article extend" etc.
     * Returns ID of selected category if available.
     *
     * @param string $sTplVarName     name of template variable where is stored category tree
     * @param string $sSelectedCatId  ID of category witch was selected in select list
     * @param string $sEditCatId      ID of category witch we are editing
     * @param bool   $blForceNonCache Set to true to disable caching
     * @param int    $iTreeShopId     tree shop id
     *
     * @return string
     */
    protected function _getCategoryTree($sTplVarName, $sSelectedCatId, $sEditCatId = '', $blForceNonCache = false, $iTreeShopId = null)
    {
        $oCatTree = $this->_createCategoryTree($sTplVarName, $sEditCatId, $blForceNonCache, $iTreeShopId);

        // mark selected
        if ($sSelectedCatId) {
            // fixed parent category in select list
            foreach ($oCatTree as $oCategory) {
                if (strcmp($oCategory->getId(), $sSelectedCatId) == 0) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        } else {
            // no category selected - opening first available
            $oCatTree->rewind();
            if ($oCat = $oCatTree->current()) {
                $oCat->selected = 1;
                $sSelectedCatId = $oCat->getId();
            }
        }

        // passing to view
        $this->_aViewData[$sTplVarName] = $oCatTree;

        return $sSelectedCatId;
    }

    /**
     * Updates object folder parameters.
     */
    public function changeFolder()
    {
        $sFolder = oxRegistry::getConfig()->getRequestParameter('setfolder');
        $sFolderClass = oxRegistry::getConfig()->getRequestParameter('folderclass');

        if ($sFolderClass == 'oxcontent' && $sFolder == 'CMSFOLDER_NONE') {
            $sFolder = '';
        }

        $oObject = oxNew($sFolderClass);
        if ($oObject->load($this->getEditObjectId())) {
            $oObject->{$oObject->getCoreTableName() . '__oxfolder'} = new oxField($sFolder);
            $oObject->save();
        }
    }

    /**
     * Sets-up navigation parameters.
     *
     * @param string $sNode active view id
     */
    protected function _setupNavigation($sNode)
    {
        // navigation according to class
        if ($sNode) {
            $myAdminNavig = $this->getNavigation();

            // default tab
            $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab($sNode, $this->_iDefEdit);

            // buttons
            $this->_aViewData['bottom_buttons'] = $myAdminNavig->getBtn($sNode);
        }
    }

    /**
     * Resets count of vendor/manufacturer category items.
     *
     * @param array $aIds to reset type => id
     */
    protected function _resetCounts($aIds)
    {
        foreach ($aIds as $sType => $aResetInfo) {
            foreach ($aResetInfo as $sResetId => $iPos) {
                switch ($sType) {
                    case 'vendor':
                        $this->resetCounter("vendorArticle", $sResetId);
                        break;
                    case 'manufacturer':
                        $this->resetCounter("manufacturerArticle", $sResetId);
                        break;
                }
            }
        }
    }
}
