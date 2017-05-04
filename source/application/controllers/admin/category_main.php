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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Admin article main categories manager.
 * There is possibility to change categories description, sorting, range of price
 * and etc.
 * Admin Menu: Manage Products -> Categories -> Main.
 */
class Category_Main extends oxAdminDetails
{

    /**
     * Loads article category data, passes it to Smarty engine, returns
     * name of template file "category_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        /** @var oxcategory $oCategory */
        $oCategory = oxNew("oxcategory");

        $soxId = $this->getEditObjectId();

        $this->_aViewData["edit"] = $oCategory;
        $this->_aViewData["oxid"] = $soxId;

        if ($soxId != "-1" && isset($soxId)) {

            // generating category tree for select list
            $this->_createCategoryTree("artcattree", $soxId);

            // load object
            $oCategory->loadInLang($this->_iEditLang, $soxId);


            $oOtherLang = $oCategory->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oCategory->loadInLang(key($oOtherLang), $soxId);
            }

            // remove already created languages
            $aLang = array_diff(oxRegistry::getLang()->getLanguageNames(), $oOtherLang);
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

        if (oxRegistry::getConfig()->getRequestParameter("aoc")) {
            /** @var category_main_ajax $oCategoryMainAjax */
            $oCategoryMainAjax = oxNew('category_main_ajax');
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
        $aSkipFields = array("OXID", "OXSHOPID", "OXMAPID", "OXPARENTID", "OXACTIVE", "OXACTIVEFROM"
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
        );
        /** @var oxDbMetaDataHandler $oDbHandler */
        $oDbHandler = oxNew("oxDbMetaDataHandler");
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

        /** @var oxCategory $oCategory */
        $oCategory = oxNew("oxcategory");

        if ($soxId != "-1") {
            $this->resetCounter("catArticle", $soxId);
            $oCategory->load($soxId);
            $oCategory->loadInLang($this->_iEditLang, $soxId);

            /** @var oxUtilsPic $myUtilsPic */
            $myUtilsPic = oxRegistry::get("oxUtilsPic");

            // #1173M - not all pic are deleted, after article is removed
            $myUtilsPic->overwritePic($oCategory, 'oxcategories', 'oxthumb', 'TC', '0', $aParams, $myConfig->getPictureDir(false));
            $myUtilsPic->overwritePic($oCategory, 'oxcategories', 'oxicon', 'CICO', 'icon', $aParams, $myConfig->getPictureDir(false));
            $myUtilsPic->overwritePic($oCategory, 'oxcategories', 'oxpromoicon', 'PICO', 'icon', $aParams, $myConfig->getPictureDir(false));
        }


        $oCategory->setLanguage(0);


        $oCategory->assign($aParams);
        $oCategory->setLanguage($this->_iEditLang);

        /** @var oxUtilsFile $oUtilsFile */
        $oUtilsFile = oxRegistry::get("oxUtilsFile");

        $oCategory = $oUtilsFile->processFiles($oCategory);
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
            $oEx = new oxExceptionToDisplay();
            $oEx->setMessage('CATEGORY_PICTURES_UPLOADISDISABLED');

            /** @var oxUtilsView $oUtilsView */
            $oUtilsView = oxRegistry::get("oxUtilsView");

            $oUtilsView->addErrorToDisplay($oEx, false);

            return;
        }

        $sOxId = $this->getEditObjectId();
        $sField = oxRegistry::getConfig()->getRequestParameter('masterPicField');
        if (empty($sField)) {
            return;
        }

        /** @var oxCategory $oItem */
        $oItem = oxNew('oxCategory');
        $oItem->load($sOxId);
        $this->_deleteCatPicture($oItem, $sField);
    }

    /**
     * Delete category picture, specified in $sField parameter
     *
     * @param oxCategory $oItem  active category object
     * @param string     $sField picture field name
     *
     * @return null
     */
    protected function _deleteCatPicture(oxCategory $oItem, $sField)
    {
        $myConfig = $this->getConfig();
        $sItemKey = 'oxcategories__' . $sField;


        switch ($sField) {
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
            /** @var oxUtilsPic $myUtilsPic */
            $myUtilsPic = oxRegistry::get("oxUtilsPic");
            /** @var oxUtilsFile $oUtilsFile */
            $oUtilsFile = oxRegistry::get("oxUtilsFile");

            $sDir = $myConfig->getPictureDir(false);
            $myUtilsPic->safePictureDelete($oItem->$sItemKey->value, $sDir . $oUtilsFile->getImageDirByType($sImgType), 'oxcategories', $sField);

            $oItem->$sItemKey = new oxField();
            $oItem->save();
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

        // shopId
        $aReqParams['oxcategories__oxshopid'] = oxRegistry::getSession()->getVariable("actshop");

        if ($this->getEditObjectId() == "-1") {
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
}
