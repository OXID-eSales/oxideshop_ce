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

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use oxUtilsObject;
use stdClass;
use OxidEsales\Eshop\Application\Model\Article;

/**
 * Admin article main manager.
 * Collects and updates (on user submit) article base parameters data ( such as
 * title, article No., short Description and etc.).
 * Admin Menu: Manage Products -> Articles -> Main.
 */
class ArticleMain extends \oxAdminDetails
{

    /**
     * Loads article parameters and passes them to Smarty engine, returns
     * name of template file "article_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->getConfig()->setConfigParam('bl_perfLoadPrice', true);

        $oArticle = oxNew('oxArticle');
        $oArticle->enablePriceLoad();

        $this->_aViewData['edit'] = $oArticle;

        $sOxId = $this->getEditObjectId();
        $sVoxId = $this->getConfig()->getRequestParameter("voxid");
        $sOxParentId = $this->getConfig()->getRequestParameter("oxparentid");

        // new variant ?
        if (isset($sVoxId) && $sVoxId == "-1" && isset($sOxParentId) && $sOxParentId && $sOxParentId != "-1") {
            $oParentArticle = oxNew("oxArticle");
            $oParentArticle->load($sOxParentId);
            $this->_aViewData["parentarticle"] = $oParentArticle;
            $this->_aViewData["oxparentid"] = $sOxParentId;

            $this->_aViewData["oxid"] = $sOxId = "-1";
        }

        if ($sOxId && $sOxId != "-1") {
            // load object
            $oArticle = $this->updateArticle($oArticle, $sOxId);

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang(key($oOtherLang), $sOxId);
            }

            // variant handling
            if ($oArticle->oxarticles__oxparentid->value) {
                $oParentArticle = oxNew("oxArticle");
                $oParentArticle->load($oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] = $oParentArticle;
                $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
                $this->_aViewData["issubvariant"] = 1;
            }

            // #381A
            $this->_formJumpList($oArticle, $oParentArticle);

            //hook for modules
            $oArticle = $this->customizeArticleInformation($oArticle);

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
        }

        $this->_aViewData["editor"] = $this->_generateTextEditor(
            "100%",
            300,
            $oArticle,
            "oxarticles__oxlongdesc",
            "details.tpl.css"
        );
        $this->_aViewData["blUseTimeCheck"] = $this->getConfig()->getConfigParam('blUseTimeCheck');

        return "article_main.tpl";
    }

    /**
     * Returns string which must be edited by editor
     *
     * @param oxbase $oObject object whifh field will be used for editing
     * @param string $sField  name of editable field
     *
     * @return string
     */
    protected function _getEditValue($oObject, $sField)
    {
        $sEditObjectValue = '';
        if ($oObject) {
            $oDescField = $oObject->getLongDescription();
            $sEditObjectValue = $this->_processEditValue($oDescField->getRawValue());
            $oDescField = new oxField($sEditObjectValue, oxField::T_RAW);
        }

        return $sEditObjectValue;
    }

    /**
     * Saves changes of article parameters.
     */
    public function save()
    {
        parent::save();

        $oConfig = $this->getConfig();
        $soxId = $this->getEditObjectId();
        $aParams = $oConfig->getRequestParameter("editval");

        // default values
        $aParams = $this->addDefaultValues($aParams);

        // null values
        if (isset($aParams['oxarticles__oxvat']) && $aParams['oxarticles__oxvat'] === '') {
            $aParams['oxarticles__oxvat'] = null;
        }

        // varianthandling
        $soxparentId = $oConfig->getRequestParameter("oxparentid");
        if (isset($soxparentId) && $soxparentId && $soxparentId != "-1") {
            $aParams['oxarticles__oxparentid'] = $soxparentId;
        } else {
            unset($aParams['oxarticles__oxparentid']);
        }

        $oArticle = oxNew("oxArticle");
        $oArticle->setLanguage($this->_iEditLang);

        if ($soxId != "-1") {
            $oArticle->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxarticles__oxid'] = null;
            $aParams['oxarticles__oxissearch'] = 1;
            $aParams['oxarticles__oxstockflag'] = 1;
            if (empty($aParams['oxarticles__oxstock'])) {
                $aParams['oxarticles__oxstock'] = 0;
            }

            if (!isset($aParams['oxarticles__oxactive'])) {
                $aParams['oxarticles__oxactive'] = 0;
            }
        }

        //article number handling, warns for artnum duplicates
        if (isset($aParams['oxarticles__oxartnum']) && strlen($aParams['oxarticles__oxartnum']) > 0 &&
            $oConfig->getConfigParam('blWarnOnSameArtNums') &&
            $oArticle->oxarticles__oxartnum->value != $aParams['oxarticles__oxartnum']
        ) {
            $sSelect = "select oxid from " . getViewName('oxarticles');
            $sSelect .= " where oxartnum = '" . $aParams['oxarticles__oxartnum'] . "'";
            $sSelect .= " and oxid != '" . $aParams['oxarticles__oxid'] . "'";
            if ($oArticle->assignRecord($sSelect)) {
                $this->_aViewData["errorsavingatricle"] = 1;
            }
        }

        $oArticle->setLanguage(0);
        //triming spaces from article title (M:876)
        if (isset($aParams['oxarticles__oxtitle'])) {
            $aParams['oxarticles__oxtitle'] = trim($aParams['oxarticles__oxtitle']);
        }

        $oArticle->assign($aParams);
        $oArticle->setArticleLongDesc($this->_processLongDesc($aParams['oxarticles__oxlongdesc']));
        $oArticle->setLanguage($this->_iEditLang);
        $oArticle = oxRegistry::get("oxUtilsFile")->processFiles($oArticle);
        $oArticle->save();

        // set oxid if inserted
        if ($soxId == "-1") {
            $sFastCat = $oConfig->getRequestParameter("art_category");
            if ($sFastCat != "-1") {
                $this->addToCategory($sFastCat, $oArticle->getId());
            }
        }

        $oArticle = $this->saveAdditionalArticleData($oArticle, $aParams);

        $this->setEditObjectId($oArticle->getId());
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
        // TODO: the code below is redundant, optimize it, assignments should go smooth without conversions
        // hack, if editor screws up text, htmledit tends to do so
        $sValue = str_replace('&amp;nbsp;', '&nbsp;', $sValue);
        $sValue = str_replace('&amp;', '&', $sValue);
        $sValue = str_replace('&quot;', '"', $sValue);
        $sValue = str_replace('&lang=', '&amp;lang=', $sValue);
        $sValue = str_replace('<p>&nbsp;</p>', '', $sValue);
        $sValue = str_replace('<p>&nbsp; </p>', '', $sValue);

        return $sValue;
    }

    /**
     * Resets article categories counters
     *
     * @param string $sArticleId Article id
     */
    protected function _resetCategoriesCounter($sArticleId)
    {
        $oDb = oxDb::getDb();
        $sQ = "select oxcatnid from oxobject2category where oxobjectid = " . $oDb->quote($sArticleId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $this->resetCounter("catArticle", $oRs->fields[0]);
                $oRs->moveNext();
            }
        }
    }

    /**
     * Add article to category.
     *
     * @param string $sCatID Category id
     * @param string $sOXID  Article id
     */
    public function addToCategory($sCatID, $sOXID)
    {
        $base = oxNew("oxBase");
        $base->init("oxobject2category");
        $base->oxobject2category__oxtime = new oxField(0);
        $base->oxobject2category__oxobjectid = new oxField($sOXID);
        $base->oxobject2category__oxcatnid = new oxField($sCatID);

        $base = $this->updateBase($base);

        $base->save();
    }

    /**
     * Copies article (with all parameters) to new articles.
     *
     * @param string $sOldId    old product id (default null)
     * @param string $sNewId    new product id (default null)
     * @param string $sParentId product parent id
     */
    public function copyArticle($sOldId = null, $sNewId = null, $sParentId = null)
    {
        $myConfig = $this->getConfig();

        $sOldId = $sOldId ? $sOldId : $this->getEditObjectId();
        $sNewId = $sNewId ? $sNewId : oxUtilsObject::getInstance()->generateUID();

        $oArticle = oxNew('oxBase');
        $oArticle->init('oxarticles');
        if ($oArticle->load($sOldId)) {
            if ($myConfig->getConfigParam('blDisableDublArtOnCopy')) {
                $oArticle->oxarticles__oxactive->setValue(0);
                $oArticle->oxarticles__oxactivefrom->setValue(0);
                $oArticle->oxarticles__oxactiveto->setValue(0);
            }

            // setting parent id
            if ($sParentId) {
                $oArticle->oxarticles__oxparentid->setValue($sParentId);
            }

            // setting oxinsert/oxtimestamp
            $iNow = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
            $oArticle->oxarticles__oxinsert = new oxField($iNow);

            // mantis#0001590: OXRATING and OXRATINGCNT not set to 0 when copying article
            $oArticle->oxarticles__oxrating = new oxField(0);
            $oArticle->oxarticles__oxratingcnt = new oxField(0);

            $oArticle->setId($sNewId);
            $oArticle->save();

            //copy categories
            $this->_copyCategories($sOldId, $sNewId);

            //atributes
            $this->_copyAttributes($sOldId, $sNewId);

            //sellist
            $this->_copySelectlists($sOldId, $sNewId);

            //crossseling
            $this->_copyCrossseling($sOldId, $sNewId);

            //accessoire
            $this->_copyAccessoires($sOldId, $sNewId);

            // #983A copying staffelpreis info
            $this->_copyStaffelpreis($sOldId, $sNewId);

            //copy article extends (longdescription)
            $this->_copyArtExtends($sOldId, $sNewId);

            //files
            $this->_copyFiles($sOldId, $sNewId);

            $this->resetContentCache();

            $myUtilsObject = oxUtilsObject::getInstance();
            $oDb = oxDb::getDb();

            //copy variants
            $sQ = "select oxid from oxarticles where oxparentid = " . $oDb->quote($sOldId);
            $oRs = $oDb->select($sQ);
            if ($oRs !== false && $oRs->recordCount() > 0) {
                while (!$oRs->EOF) {
                    $this->copyArticle($oRs->fields[0], $myUtilsObject->generateUid(), $sNewId);
                    $oRs->moveNext();
                }
            }

            // only for top articles
            if (!$sParentId) {
                $this->setEditObjectId($oArticle->getId());

                //article number handling, warns for artnum duplicates
                $sFncParameter = oxRegistry::getConfig()->getRequestParameter('fnc');
                $sArtNumField = 'oxarticles__oxartnum';
                if ($myConfig->getConfigParam('blWarnOnSameArtNums') &&
                    $oArticle->$sArtNumField->value && $sFncParameter == 'copyArticle'
                ) {
                    $sSelect = "select oxid from " . $oArticle->getCoreTableName() .
                               " where oxartnum = " . $oDb->quote($oArticle->$sArtNumField->value) .
                               " and oxid != " . $oDb->quote($sNewId);

                    if ($oArticle->assignRecord($sSelect)) {
                        $this->_aViewData["errorsavingatricle"] = 1;
                    }
                }
            }
        }
    }

    /**
     * Copying category assignments
     *
     * @param string $sOldId Id from old article
     * @param string $newArticleId Id from new article
     */
    protected function _copyCategories($sOldId, $newArticleId)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDb = oxDb::getDb();

        $sO2CView = getViewName('oxobject2category');
        $sQ = "select oxcatnid, oxtime from {$sO2CView} where oxobjectid = " . $oDb->quote($sOldId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $uniqueId = $myUtilsObject->generateUid();
                $sCatId = $oRs->fields[0];
                $sTime = $oRs->fields[1];
                $sSql = $this->formQueryForCopyingToCategory($newArticleId, $uniqueId, $sCatId, $sTime);
                $oDb->execute($sSql);
                $oRs->moveNext();
            }
        }
    }

    /**
     * Copying attributes assignments
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copyAttributes($sOldId, $sNewId)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDb = oxDb::getDb();

        $sQ = "select oxid from oxobject2attribute where oxobjectid = " . $oDb->quote($sOldId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                // #1055A
                $oAttr = oxNew("oxBase");
                $oAttr->init("oxobject2attribute");
                $oAttr->load($oRs->fields[0]);
                $oAttr->setId($myUtilsObject->generateUID());
                $oAttr->oxobject2attribute__oxobjectid->setValue($sNewId);
                $oAttr->save();
                $oRs->moveNext();
            }
        }
    }

    /**
     * Copying files
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copyFiles($sOldId, $sNewId)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $sQ = "SELECT * FROM `oxfiles` WHERE `oxartid` = " . $oDb->quote($sOldId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $oFile = oxNew("oxfile");
                $oFile->setId($myUtilsObject->generateUID());
                $oFile->oxfiles__oxartid = new oxField($sNewId);
                $oFile->oxfiles__oxfilename = new oxField($oRs->fields['OXFILENAME']);
                $oFile->oxfiles__oxfilesize = new oxField($oRs->fields['OXFILESIZE']);
                $oFile->oxfiles__oxstorehash = new oxField($oRs->fields['OXSTOREHASH']);
                $oFile->oxfiles__oxpurchasedonly = new oxField($oRs->fields['OXPURCHASEDONLY']);
                $oFile->save();
                $oRs->moveNext();
            }
        }
    }

    /**
     * Copying selectlists assignments
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copySelectlists($sOldId, $sNewId)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDb = oxDb::getDb();

        $sQ = "select oxselnid from oxobject2selectlist where oxobjectid = " . $oDb->quote($sOldId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $sUid = $myUtilsObject->generateUID();
                $sId = $oRs->fields[0];
                $sSql = "insert into oxobject2selectlist (oxid, oxobjectid, oxselnid) " .
                        "VALUES (" . $oDb->quote($sUid) . ", " . $oDb->quote($sNewId) . ", " . $oDb->quote($sId) . ") ";
                $oDb->execute($sSql);
                $oRs->moveNext();
            }
        }
    }

    /**
     * Copying crossseling assignments
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copyCrossseling($sOldId, $sNewId)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDb = oxDb::getDb();

        $sQ = "select oxobjectid from oxobject2article where oxarticlenid = " . $oDb->quote($sOldId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $sUid = $myUtilsObject->generateUID();
                $sId = $oRs->fields[0];
                $sSql = "insert into oxobject2article (oxid, oxobjectid, oxarticlenid) " .
                       "VALUES (" . $oDb->quote($sUid) . ", " . $oDb->quote($sId) . ", " . $oDb->quote($sNewId) . " ) ";
                $oDb->execute($sSql);
                $oRs->moveNext();
            }
        }
    }

    /**
     * Copying accessoires assignments
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copyAccessoires($sOldId, $sNewId)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDb = oxDb::getDb();

        $sQ = "select oxobjectid from oxaccessoire2article where oxarticlenid= " . $oDb->quote($sOldId);
        $oRs = $oDb->select($sQ);
        if ($oRs !== false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $sUId = $myUtilsObject->generateUid();
                $sId = $oRs->fields[0];
                $sSql = "insert into oxaccessoire2article (oxid, oxobjectid, oxarticlenid) " .
                        "VALUES (" . $oDb->quote($sUId) . ", " . $oDb->quote($sId) . ", " . $oDb->quote($sNewId) . ") ";
                $oDb->execute($sSql);
                $oRs->moveNext();
            }
        }
    }

    /**
     * Copying staffelpreis assignments
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copyStaffelpreis($sOldId, $sNewId)
    {
        $sShopId = $this->getConfig()->getShopId();
        $oPriceList = oxNew("oxlist");
        $oPriceList->init("oxbase", "oxprice2article");
        $sQ = "select * from oxprice2article where oxartid = '{$sOldId}' and oxshopid = '{$sShopId}' " .
              "and (oxamount > 0 or oxamountto > 0) order by oxamount ";
        $oPriceList->selectString($sQ);
        if ($oPriceList->count()) {
            foreach ($oPriceList as $oItem) {
                $oItem->oxprice2article__oxid->setValue($oItem->setId());
                $oItem->oxprice2article__oxartid->setValue($sNewId);
                $oItem->save();
            }
        }
    }

    /**
     * Copying article extends
     *
     * @param string $sOldId Id from old article
     * @param string $sNewId Id from new article
     */
    protected function _copyArtExtends($sOldId, $sNewId)
    {
        $oExt = oxNew("oxBase");
        $oExt->init("oxartextends");
        $oExt->load($sOldId);
        $oExt->setId($sNewId);
        $oExt->save();
    }

    /**
     * Saves article parameters in different language.
     */
    public function saveinnlang()
    {
        $this->save();
    }

    /**
     * Sets default values for empty article (currently does nothing), returns
     * array with parameters.
     *
     * @param array $aParams Parameters, to set default values
     *
     * @return array
     */
    public function addDefaultValues($aParams)
    {
        return $aParams;
    }

    /**
     * Function forms article variants jump list.
     *
     * @param object $oArticle       article object
     * @param object $oParentArticle article parent object
     */
    protected function _formJumpList($oArticle, $oParentArticle)
    {
        $aJumpList = array();
        //fetching parent article variants
        $sOxIdField = 'oxarticles__oxid';
        if (isset($oParentArticle)) {
            $aJumpList[] = array($oParentArticle->$sOxIdField->value, $this->_getTitle($oParentArticle));
            $sEditLanguageParameter = oxRegistry::getConfig()->getRequestParameter("editlanguage");
            $oParentVariants = $oParentArticle->getAdminVariants($sEditLanguageParameter);
            if ($oParentVariants->count()) {
                foreach ($oParentVariants as $oVar) {
                    $aJumpList[] = array($oVar->$sOxIdField->value, " - " . $this->_getTitle($oVar));
                    if ($oVar->$sOxIdField->value == $oArticle->$sOxIdField->value) {
                        $oVariants = $oArticle->getAdminVariants($sEditLanguageParameter);
                        if ($oVariants->count()) {
                            foreach ($oVariants as $oVVar) {
                                $aJumpList[] = array($oVVar->$sOxIdField->value, " -- " . $this->_getTitle($oVVar));
                            }
                        }
                    }
                }
            }
        } else {
            $aJumpList[] = array($oArticle->$sOxIdField->value, $this->_getTitle($oArticle));
            //fetching this article variants data
            $oVariants = $oArticle->getAdminVariants(oxRegistry::getConfig()->getRequestParameter("editlanguage"));
            if ($oVariants && $oVariants->count()) {
                foreach ($oVariants as $oVar) {
                    $aJumpList[] = array($oVar->$sOxIdField->value, " - " . $this->_getTitle($oVar));
                }
            }
        }
        if (count($aJumpList) > 1) {
            $this->_aViewData["thisvariantlist"] = $aJumpList;
        }
    }

    /**
     * Returns formed variant title
     *
     * @param object $oObj product object
     *
     * @return string
     */
    protected function _getTitle($oObj)
    {
        $sTitle = $oObj->oxarticles__oxtitle->value;
        if (!strlen($sTitle)) {
            $sTitle = $oObj->oxarticles__oxvarselect->value;
        }

        return $sTitle;
    }

    /**
     * Returns shop manufacturers list
     *
     * @return oxmanufacturerlist
     */
    public function getCategoryList()
    {
        $oCatTree = oxNew("oxCategoryList");
        $oCatTree->loadList();

        return $oCatTree;
    }

    /**
     * Returns shop manufacturers list
     *
     * @return oxmanufacturerlist
     */
    public function getVendorList()
    {
        $oVendorlist = oxNew("oxvendorlist");
        $oVendorlist->loadVendorList();

        return $oVendorlist;
    }

    /**
     * Returns shop manufacturers list
     *
     * @return oxmanufacturerlist
     */
    public function getManufacturerList()
    {
        $oManufacturerList = oxNew("oxmanufacturerlist");
        $oManufacturerList->loadManufacturerList();

        return $oManufacturerList;
    }

    /**
     * Loads language for article.
     *
     * @param oxArticle $oArticle
     * @param string    $sOxId
     *
     * @return oxArticle
     */
    protected function updateArticle($oArticle, $sOxId)
    {
        $oArticle->loadInLang($this->_iEditLang, $sOxId);

        return $oArticle;
    }

    /**
     * Forms query which is used for adding article to category.
     *
     * @param string $newArticleId
     * @param string $sUid
     * @param string $sCatId
     * @param string $sTime
     *
     * @return string
     */
    protected function formQueryForCopyingToCategory($newArticleId, $sUid, $sCatId, $sTime)
    {
        $oDb = oxDb::getDb();
        return "insert into oxobject2category (oxid, oxobjectid, oxcatnid, oxtime) " .
            "VALUES (" . $oDb->quote($sUid) . ", " . $oDb->quote($newArticleId) . ", " .
            $oDb->quote($sCatId) . ", " . $oDb->quote($sTime) . ") ";
    }

    /**
     * @param oxBase $base
     */
    protected function updateBase($base)
    {
        return $base;
    }

    /**
     * Customize article data for rendering.
     * Intended to be used by modules.
     *
     * @param Article $article
     *
     * @return Article
     */
    protected function customizeArticleInformation($article)
    {
        return $article;
    }

    /**
     * Save non standard article information if needed.
     * Intended to be used by modules.
     *
     * @param Article $article
     * @param array   $parameters
     *
     * @return Article
     */
    protected function saveAdditionalArticleData($article, $parameters)
    {
        return $article;
    }
}
