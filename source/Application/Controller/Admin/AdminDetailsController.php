<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\TextEditorHandler;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use Symfony\Component\Filesystem\Path;

class AdminDetailsController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /** @inheritdoc */
    public function render()
    {
        $sReturn = parent::render();

        // generate help link
        $myConfig = Registry::getConfig();
        $sDir = Path::join(
            ContainerFacade::getParameter('oxid_shop_source_directory'),
            'documentation',
            'admin'
        );
        if (is_dir($sDir)) {
            $sDir = ContainerFacade::getParameter('oxid_shop_url') . 'documentation/admin';
        } else {
            $languageId = $this->getDocumentationLanguageId();
            $shopVersion = oxNew(ShopVersion::class)->getVersion();
            $sDir = "http://docu.oxid-esales.com/PE/{$shopVersion}/" . $languageId . '/admin';
        }

        $this->_aViewData['sHelpURL'] = $sDir;

        return $sReturn;
    }

    /**
     * Get language id for documentation by current language id.
     *
     * @return int
     */
    protected function getDocumentationLanguageId()
    {
        $language = Registry::getLang();
        $languageAbbr = $language->getLanguageAbbr($language->getTplLanguage());

        return $languageAbbr === "de" ? 0 : 1;
    }

    /**
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $object
     * @param string $fieldName
     *
     * @return string
     * @deprecated method will be removed in v7.0
     */
    protected function getEditValue($object, $fieldName)
    {
        if (!$object || !$fieldName || !isset($object->$fieldName)) {
            return '';
        }
        if (!$object->$fieldName instanceof Field) {
            $object->$fieldName = new Field($object->$fieldName->value, Field::T_RAW);
        }

        return $object->$fieldName->getRawValue();
    }

    /**
     * Generates Text editor html code.
     *
     * @param int                                    $width      editor width
     * @param int                                    $height     editor height
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $object     object passed to editor
     * @param string                                 $field      object field which content is passed to editor
     * @param string                                 $stylesheet stylesheet to use in editor
     *
     * @return string Editor output
     */
    protected function generateTextEditor($width, $height, $object, $field, $stylesheet = null)
    {
        $objectValue = $this->getEditValue($object, $field);

        $textEditorHandler = $this->createTextEditorHandler();
        $this->configureTextEditorHandler($textEditorHandler, $object, $field, $stylesheet);

        return $textEditorHandler->renderTextEditor($width, $height, $objectValue, $field);
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
    protected function createCategoryTree($sTplVarName, $sEditCatId = '', $blForceNonCache = false, $iTreeShopId = null)
    {
        // caching category tree, to load it once, not many times
        if (!isset($this->oCatTree) || $blForceNonCache) {
            $this->oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
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
        $oRoot = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $oRoot->oxcategories__oxtitle = new Field('--');

        $oCatTree->assign(array_merge(['' => $oRoot], $oCatTree->getArray()));

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
    protected function getCategoryTree(
        $sTplVarName,
        $sSelectedCatId,
        $sEditCatId = '',
        $blForceNonCache = false,
        $iTreeShopId = null
    ) {
        $oCatTree = $this->createCategoryTree($sTplVarName, $sEditCatId, $blForceNonCache, $iTreeShopId);

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
        $sFolder = Registry::getRequest()->getRequestEscapedParameter('setfolder');
        $sFolderClass = Registry::getRequest()->getRequestEscapedParameter('folderclass');

        if ($sFolderClass == 'oxcontent' && $sFolder == 'CMSFOLDER_NONE') {
            $sFolder = '';
        }

        $oObject = oxNew($sFolderClass);
        if ($oObject->load($this->getEditObjectId())) {
            $oObject->{$oObject->getCoreTableName() . '__oxfolder'} = new Field($sFolder);
            $oObject->save();
        }
    }

    /**
     * Sets-up navigation parameters.
     *
     * @param string $sNode active view id
     */
    protected function setupNavigation($sNode)
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
    protected function resetCounts($aIds)
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

    /**
     * Create the handler for the text editor.
     *
     * Note: the parameters editedObject and field are not used here but in the enterprise edition.
     *
     * @param TextEditorHandler $textEditorHandler
     * @param mixed             $editedObject      The object we want to edit, either type of
     *                                             \OxidEsales\Eshop\Core\BaseModel if you want to persist or anything
     *                                             else
     * @param string            $field             The input field we want to edit
     * @param string            $stylesheet        The name of the CSS file
     */
    protected function configureTextEditorHandler(
        TextEditorHandler $textEditorHandler,
        $editedObject,
        $field,
        $stylesheet
    ) {
        $textEditorHandler->setStyleSheet($stylesheet);
    }

    /**
     * Create the handler for the text editor.
     *
     * @return TextEditorHandler The text editor handler
     */
    protected function createTextEditorHandler()
    {
        $textEditorHandler = oxNew(TextEditorHandler::class);

        return $textEditorHandler;
    }
}
