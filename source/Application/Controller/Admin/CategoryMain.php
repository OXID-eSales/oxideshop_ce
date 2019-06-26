<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;
use stdClass;
use oxCategory;
use oxUtilsPic;
use oxUtilsFile;
use oxExceptionToDisplay;
use oxDbMetaDataHandler;
use oxUtilsView;
use category_main_ajax;

/**
 * Admin article main categories manager.
 * There is possibility to change categories description, sorting, range of price
 * and etc.
 * Admin Menu: Manage Products -> Categories -> Main.
 */
class CategoryMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    const NEW_CATEGORY_ID = "-1";

    /**
     * Loads article category data,
     * returns the name of the template file.
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        /** @var \OxidEsales\Eshop\Application\Model\Category $oCategory */
        $oCategory = $this->createCategory();

        $categoryId = $this->getEditObjectId();

        $this->_aViewData["edit"] = $oCategory;
        $this->_aViewData["oxid"] = $categoryId;

        if (isset($categoryId) && $categoryId != self::NEW_CATEGORY_ID) {
            // generating category tree for select list
            $this->_createCategoryTree("artcattree", $categoryId);

            // load object
            $oCategory->loadInLang($this->_iEditLang, $categoryId);

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                $this->_aViewData['readonly_fields'] = true;
            }

            $oOtherLang = $oCategory->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oCategory->loadInLang(key($oOtherLang), $categoryId);
            }

            // remove already created languages
            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            if ($oCategory->oxcategories__oxparentid->value == 'oxrootid') {
                $oCategory->oxcategories__oxparentid->setValue('');
            }

            $this->_getCategoryTree("cattree", $oCategory->oxcategories__oxparentid->value, $oCategory->oxcategories__oxid->value, true, $oCategory->oxcategories__oxshopid->value);

            $this->_aViewData["defsort"] = $oCategory->oxcategories__oxdefsort->value;
        } else {
            $this->_createCategoryTree("cattree", "", true, $myConfig->getShopId());
        }

        $this->_aViewData["sortableFields"] = $this->getSortableFields();

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            /** @var \OxidEsales\Eshop\Application\Controller\Admin\CategoryMainAjax $oCategoryMainAjax */
            $oCategoryMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\CategoryMainAjax::class);
            $this->_aViewData['oxajax'] = $oCategoryMainAjax->getColumns();

            return "popups/category_main.tpl";
        }

        return "category_main.tpl";
    }

    /**
     * Returns an array of article object DB fields, without multi language and unsortible fields.
     *
     * @return array
     */
    public function getSortableFields()
    {
        $aSkipFields = ["OXID", "OXSHOPID", "OXMAPID", "OXPARENTID", "OXACTIVE", "OXACTIVEFROM"
        , "OXACTIVETO", "OXSHORTDESC"
        , "OXUNITNAME", "OXUNITQUANTITY", "OXEXTURL", "OXURLDESC", "OXURLIMG", "OXVAT"
        , "OXTHUMB", "OXPIC1", "OXPIC2", "OXPIC3", "OXPIC4", "OXPIC5"
        , "OXPIC6", "OXPIC7", "OXPIC8", "OXPIC9", "OXPIC10", "OXPIC11", "OXPIC12", "OXSTOCKFLAG"
        , "OXSTOCKTEXT", "OXNOSTOCKTEXT", "OXDELIVERY", "OXFILE", "OXSEARCHKEYS", "OXTEMPLATE"
        , "OXQUESTIONEMAIL", "OXISSEARCH", "OXISCONFIGURABLE", "OXBUNDLEID", "OXFOLDER", "OXSUBCLASS"
        , "OXREMINDACTIVE", "OXREMINDAMOUNT", "OXVENDORID", "OXMANUFACTURERID", "OXSKIPDISCOUNTS"
        , "OXBLFIXEDPRICE", "OXICON", "OXVARSELECT", "OXAMITEMID", "OXAMTASKID", "OXPIXIEXPORT", "OXPIXIEXPORTED", "OXSORT"
        , "OXUPDATEPRICE", "OXUPDATEPRICEA", "OXUPDATEPRICEB", "OXUPDATEPRICEC", "OXUPDATEPRICETIME", "OXISDOWNLOADABLE"
        , "OXVARMAXPRICE", "OXSHOWCUSTOMAGREEMENT"
        ];
        /** @var \OxidEsales\Eshop\Core\DbMetaDataHandler $oDbHandler */
        $oDbHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $aFields = array_merge($oDbHandler->getMultilangFields('oxarticles'), array_keys($oDbHandler->getSinglelangFields('oxarticles', 0)));
        $aFields = array_diff($aFields, $aSkipFields);
        $aFields = array_unique($aFields);

        return $aFields;
    }

    /**
     * Saves article category data.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $myConfig = $this->getConfig();

        $soxId = $this->getEditObjectId();

        $aParams = $this->_parseRequestParametersForSave(
            $myConfig->getRequestParameter("editval")
        );

        /** @var \OxidEsales\Eshop\Application\Model\Category $oCategory */
        $oCategory = $this->createCategory();

        if ($soxId != self::NEW_CATEGORY_ID) {
            $this->resetCounter("catArticle", $soxId);
            $this->resetCategoryPictures($oCategory, $aParams, $soxId);
        }

        //Disable editing for derived items
        if ($oCategory->isDerived()) {
            return;
        }

        $oCategory = $this->updateCategoryOnSave($oCategory, $aParams);

        $oCategory->save();

        $this->setEditObjectId($oCategory->getId());
    }

    /**
     * Fixes html broken by html editor
     *
     * @param string $sValue value to fix
     *
     * @return string
     */
    protected function _processLongDesc($sValue)
    {
        // workaround for firefox showing &lang= as &9001;= entity, mantis#0001272
        return str_replace('&lang=', '&amp;lang=', $sValue);
    }

    /**
     * Saves article category data to different language (eg. english).
     */
    public function saveinnlang()
    {
        $this->save();
    }

    /**
     * Deletes selected master picture.
     *
     * @return null
     */
    public function deletePicture()
    {
        $myConfig = $this->getConfig();

        if ($myConfig->isDemoShop()) {
            // disabling uploading pictures if this is demo shop
            $oEx = new \OxidEsales\Eshop\Core\Exception\ExceptionToDisplay();
            $oEx->setMessage('CATEGORY_PICTURES_UPLOADISDISABLED');

            /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
            $oUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();

            $oUtilsView->addErrorToDisplay($oEx, false);

            return;
        }

        $sOxId = $this->getEditObjectId();
        $sField = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('masterPicField');
        if (empty($sField)) {
            return;
        }

        /** @var \OxidEsales\Eshop\Application\Model\Category $oItem */
        $oItem = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $oItem->load($sOxId);
        $this->_deleteCatPicture($oItem, $sField);
    }

    /**
     * Delete category picture, specified in $sField parameter
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $item  active category object
     * @param string                                       $field picture field name
     *
     * @return null
     */
    protected function _deleteCatPicture($item, $field)
    {
        if ($item->isDerived()) {
            return;
        }

        $myConfig = $this->getConfig();
        $sItemKey = 'oxcategories__' . $field;

        switch ($field) {
            case 'oxthumb':
                $sImgType = 'TC';
                break;

            case 'oxicon':
                $sImgType = 'CICO';
                break;

            case 'oxpromoicon':
                $sImgType = 'PICO';
                break;

            default:
                $sImgType = false;
        }

        if ($sImgType !== false) {
            /** @var \OxidEsales\Eshop\Core\UtilsPic $myUtilsPic */
            $myUtilsPic = \OxidEsales\Eshop\Core\Registry::getUtilsPic();
            /** @var \OxidEsales\Eshop\Core\UtilsFile $oUtilsFile */
            $oUtilsFile = \OxidEsales\Eshop\Core\Registry::getUtilsFile();

            $sDir = $myConfig->getPictureDir(false);
            $myUtilsPic->safePictureDelete($item->$sItemKey->value, $sDir . $oUtilsFile->getImageDirByType($sImgType), 'oxcategories', $field);

            $item->$sItemKey = new \OxidEsales\Eshop\Core\Field();
            $item->save();
        }
    }

    /**
     * Parse parameters prior to saving category.
     *
     * @param array $aReqParams Request parameters.
     *
     * @return array
     */
    protected function _parseRequestParametersForSave($aReqParams)
    {
        // checkbox handling
        if (!isset($aReqParams['oxcategories__oxactive'])) {
            $aReqParams['oxcategories__oxactive'] = 0;
        }
        if (!isset($aReqParams['oxcategories__oxhidden'])) {
            $aReqParams['oxcategories__oxhidden'] = 0;
        }
        if (!isset($aReqParams['oxcategories__oxdefsortmode'])) {
            $aReqParams['oxcategories__oxdefsortmode'] = 0;
        }

        // null values
        if ($aReqParams['oxcategories__oxvat'] === '') {
            $aReqParams['oxcategories__oxvat'] = null;
        }

        if ($this->getEditObjectId() == self::NEW_CATEGORY_ID) {
            //#550A - if new category is made then is must be default activ
            //#4051: Impossible to create inactive category
            //$aReqParams['oxcategories__oxactive'] = 1;
            $aReqParams['oxcategories__oxid'] = null;
        }

        if (isset($aReqParams["oxcategories__oxlongdesc"])) {
            $aReqParams["oxcategories__oxlongdesc"] = $this->_processLongDesc($aReqParams["oxcategories__oxlongdesc"]);
        }

        if (empty($aReqParams['oxcategories__oxpricefrom'])) {
            $aReqParams['oxcategories__oxpricefrom'] = 0;
        }
        if (empty($aReqParams['oxcategories__oxpriceto'])) {
            $aReqParams['oxcategories__oxpriceto'] = 0;
        }

        return $aReqParams;
    }

    /**
     * Set parameters, language and files to category object.
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $category
     * @param array                                        $params
     * @param string                                       $categoryId
     */
    protected function resetCategoryPictures($category, $params, $categoryId)
    {
        $config = $this->getConfig();
        $category->load($categoryId);
        $category->loadInLang($this->_iEditLang, $categoryId);

        /** @var \OxidEsales\Eshop\Core\UtilsPic $utilsPic */
        $utilsPic = \OxidEsales\Eshop\Core\Registry::getUtilsPic();

        // #1173M - not all pic are deleted, after article is removed
        $utilsPic->overwritePic($category, 'oxcategories', 'oxthumb', 'TC', '0', $params, $config->getPictureDir(false));
        $utilsPic->overwritePic($category, 'oxcategories', 'oxicon', 'CICO', 'icon', $params, $config->getPictureDir(false));
        $utilsPic->overwritePic($category, 'oxcategories', 'oxpromoicon', 'PICO', 'icon', $params, $config->getPictureDir(false));
    }

    /**
     * Set parameters, language and files to category object.
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $category
     * @param array                                        $params
     *
     * @return \OxidEsales\Eshop\Application\Model\Category
     */
    protected function updateCategoryOnSave($category, $params)
    {
        $category->assign($params);
        $category->setLanguage($this->_iEditLang);

        $utilsFile = \OxidEsales\Eshop\Core\Registry::getUtilsFile();

        return $utilsFile->processFiles($category);
    }

    /**
     * @return \OxidEsales\Eshop\Application\Model\Category
     */
    protected function createCategory()
    {
        $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        return $category;
    }
}
