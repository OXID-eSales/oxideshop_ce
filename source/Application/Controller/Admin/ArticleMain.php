<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use oxRegistry;
use oxDb;
use oxField;
use stdClass;
use OxidEsales\Eshop\Application\Model\Article;

/**
 * Admin article main manager.
 * Collects and updates (on user submit) article base parameters data ( such as
 * title, article No., short Description and etc.).
 * Admin Menu: Manage Products -> Articles -> Main.
 */
class ArticleMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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

        $oArticle = $this->createArticle();
        $oArticle->enablePriceLoad();

        $this->_aViewData['edit'] = $oArticle;

        $sOxId = $this->getEditObjectId();
        $sVoxId = $this->getConfig()->getRequestParameter("voxid");
        $sOxParentId = $this->getConfig()->getRequestParameter("oxparentid");

        // new variant ?
        if (isset($sVoxId) && $sVoxId == "-1" && isset($sOxParentId) && $sOxParentId && $sOxParentId != "-1") {
            $oParentArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
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
                $oParentArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                $oParentArticle->load($oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] = $oParentArticle;
                $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
                $this->_aViewData["issubvariant"] = 1;
            }

            // #381A
            $this->_formJumpList($oArticle, $oParentArticle);

            //hook for modules
            $oArticle = $this->customizeArticleInformation($oArticle);

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
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $oObject object with field will be used for editing
     * @param string                                 $sField  name of editable field
     *
     * @return string
     */
    protected function _getEditValue($oObject, $sField)
    {
        $sEditObjectValue = '';
        if ($oObject) {
            $oDescField = $oObject->getLongDescription();
            $sEditObjectValue = $this->_processEditValue($oDescField->getRawValue());
        }

        return $sEditObjectValue;
    }

    /**
     * Saves changes of article parameters.
     */
    public function save()
    {
        parent::save();

        $oDb = DatabaseProvider::getDb();
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

        $oArticle = $this->createArticle();
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
            $sSelect .= " where oxartnum = " . $oDb->quote($aParams['oxarticles__oxartnum']) . "";
            $sSelect .= " and oxid != " . $oDb->quote($aParams['oxarticles__oxid']) . "";
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
        $oArticle = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oArticle);
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
        $oDb = DatabaseProvider::getDb();
        $sQ = "select oxcatnid from oxobject2category where oxobjectid = :oxobjectid";
        $oRs = $oDb->select($sQ, [
            ':oxobjectid' => $sArticleId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $this->resetCounter("catArticle", $oRs->fields[0]);
                $oRs->fetchRow();
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
        $base = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $base->init("oxobject2category");
        $base->oxobject2category__oxtime = new \OxidEsales\Eshop\Core\Field(0);
        $base->oxobject2category__oxobjectid = new \OxidEsales\Eshop\Core\Field($sOXID);
        $base->oxobject2category__oxcatnid = new \OxidEsales\Eshop\Core\Field($sCatID);

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
        $sNewId = $sNewId ? $sNewId : \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID();

        $oArticle = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
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
            $iNow = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
            $oArticle->oxarticles__oxinsert = new \OxidEsales\Eshop\Core\Field($iNow);

            // mantis#0001590: OXRATING and OXRATINGCNT not set to 0 when copying article
            $oArticle->oxarticles__oxrating = new \OxidEsales\Eshop\Core\Field(0);
            $oArticle->oxarticles__oxratingcnt = new \OxidEsales\Eshop\Core\Field(0);

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

            $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
            $oDb = DatabaseProvider::getDb();

            //copy variants
            $sQ = "select oxid from oxarticles where oxparentid = :oxparentid";
            $oRs = $oDb->select($sQ, [
                ':oxparentid' => $sOldId
            ]);
            if ($oRs !== false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    $this->copyArticle($oRs->fields[0], $myUtilsObject->generateUid(), $sNewId);
                    $oRs->fetchRow();
                }
            }

            // only for top articles
            if (!$sParentId) {
                $this->setEditObjectId($oArticle->getId());

                //article number handling, warns for artnum duplicates
                $sFncParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('fnc');
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
     * @param string $sOldId       Id from old article
     * @param string $newArticleId Id from new article
     */
    protected function _copyCategories($sOldId, $newArticleId)
    {
        $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $oDb = DatabaseProvider::getDb();

        $sO2CView = getViewName('oxobject2category');
        $sQ = "select oxcatnid, oxtime from {$sO2CView} where oxobjectid = :oxobjectid";
        $oRs = $oDb->select($sQ, [
            ':oxobjectid' => $sOldId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $uniqueId = $myUtilsObject->generateUid();
                $sCatId = $oRs->fields[0];
                $sTime = $oRs->fields[1];
                $sSql = $this->formQueryForCopyingToCategory($newArticleId, $uniqueId, $sCatId, $sTime);
                $oDb->execute($sSql);
                $oRs->fetchRow();
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
        $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $oDb = DatabaseProvider::getDb();

        $sQ = "select oxid from oxobject2attribute where oxobjectid = :oxobjectid";
        $oRs = $oDb->select($sQ, [
            ':oxobjectid' => $sOldId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                // #1055A
                $oAttr = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oAttr->init("oxobject2attribute");
                $oAttr->load($oRs->fields[0]);
                $oAttr->setId($myUtilsObject->generateUID());
                $oAttr->oxobject2attribute__oxobjectid->setValue($sNewId);
                $oAttr->save();
                $oRs->fetchRow();
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
        $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

        $sQ = "SELECT * FROM `oxfiles` WHERE `oxartid` = :oxartid";
        $oRs = $oDb->select($sQ, [
            ':oxartid' => $sOldId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $oFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
                $oFile->setId($myUtilsObject->generateUID());
                $oFile->oxfiles__oxartid = new \OxidEsales\Eshop\Core\Field($sNewId);
                $oFile->oxfiles__oxfilename = new \OxidEsales\Eshop\Core\Field($oRs->fields['OXFILENAME']);
                $oFile->oxfiles__oxfilesize = new \OxidEsales\Eshop\Core\Field($oRs->fields['OXFILESIZE']);
                $oFile->oxfiles__oxstorehash = new \OxidEsales\Eshop\Core\Field($oRs->fields['OXSTOREHASH']);
                $oFile->oxfiles__oxpurchasedonly = new \OxidEsales\Eshop\Core\Field($oRs->fields['OXPURCHASEDONLY']);
                $oFile->save();
                $oRs->fetchRow();
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
        $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $oDb = DatabaseProvider::getDb();

        $sQ = "select oxselnid from oxobject2selectlist where oxobjectid = :oxobjectid";
        $oRs = $oDb->select($sQ, [
            ':oxobjectid' => $sOldId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $sUid = $myUtilsObject->generateUID();
                $sId = $oRs->fields[0];
                $sSql = "INSERT INTO oxobject2selectlist (oxid, oxobjectid, oxselnid) " .
                        "VALUES (:oxid, :oxobjectid, :oxselnid)";
                $oDb->execute($sSql, [
                    ':oxid' => $sUid,
                    ':oxobjectid' => $sNewId,
                    ':oxselnid' => $sId,
                ]);
                $oRs->fetchRow();
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
        $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $oDb = DatabaseProvider::getDb();

        $sQ = "select oxobjectid from oxobject2article where oxarticlenid = :oxarticlenid";
        $oRs = $oDb->select($sQ, [
            ':oxarticlenid' => $sOldId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $sUid = $myUtilsObject->generateUID();
                $sId = $oRs->fields[0];
                $sSql = "INSERT INTO oxobject2article (oxid, oxobjectid, oxarticlenid) " .
                        "VALUES (:oxid, :oxobjectid, :oxarticlenid)";
                $oDb->execute($sSql, [
                    ':oxid' => $sUid,
                    ':oxobjectid' => $sId,
                    ':oxarticlenid' => $sNewId
                ]);
                $oRs->fetchRow();
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
        $myUtilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $oDb = DatabaseProvider::getDb();

        $sQ = "select oxobjectid from oxaccessoire2article where oxarticlenid = :oxarticlenid";
        $oRs = $oDb->select($sQ, [
            ':oxarticlenid' => $sOldId
        ]);
        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $sUId = $myUtilsObject->generateUid();
                $sId = $oRs->fields[0];
                $sSql = "INSERT INTO oxaccessoire2article (oxid, oxobjectid, oxarticlenid) " .
                        "VALUES (:oxid, :oxobjectid, :oxarticlenid)";
                $oDb->execute($sSql, [
                    ':oxid' => $sUId,
                    ':oxobjectid' => $sId,
                    ':oxarticlenid' => $sNewId
                ]);
                $oRs->fetchRow();
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
        $oPriceList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oPriceList->init("oxbase", "oxprice2article");
        $sQ = "select * from oxprice2article where oxartid = :oxartid and oxshopid = :oxshopid " .
              "and (oxamount > 0 or oxamountto > 0) order by oxamount ";
        $oPriceList->selectString($sQ, [
            ':oxartid' => $sOldId,
            ':oxshopid' => $sShopId
        ]);
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
        $oExt = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
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
        $aJumpList = [];
        //fetching parent article variants
        $sOxIdField = 'oxarticles__oxid';
        if (isset($oParentArticle)) {
            $aJumpList[] = [$oParentArticle->$sOxIdField->value, $this->_getTitle($oParentArticle)];
            $sEditLanguageParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editlanguage");
            $oParentVariants = $oParentArticle->getAdminVariants($sEditLanguageParameter);
            if ($oParentVariants->count()) {
                foreach ($oParentVariants as $oVar) {
                    $aJumpList[] = [$oVar->$sOxIdField->value, " - " . $this->_getTitle($oVar)];
                    if ($oVar->$sOxIdField->value == $oArticle->$sOxIdField->value) {
                        $oVariants = $oArticle->getAdminVariants($sEditLanguageParameter);
                        if ($oVariants->count()) {
                            foreach ($oVariants as $oVVar) {
                                $aJumpList[] = [$oVVar->$sOxIdField->value, " -- " . $this->_getTitle($oVVar)];
                            }
                        }
                    }
                }
            }
        } else {
            $aJumpList[] = [$oArticle->$sOxIdField->value, $this->_getTitle($oArticle)];
            //fetching this article variants data
            $oVariants = $oArticle->getAdminVariants(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editlanguage"));
            if ($oVariants && $oVariants->count()) {
                foreach ($oVariants as $oVar) {
                    $aJumpList[] = [$oVar->$sOxIdField->value, " - " . $this->_getTitle($oVar)];
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
        $oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
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
        $oVendorlist = oxNew(\OxidEsales\Eshop\Application\Model\VendorList::class);
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
        $oManufacturerList = oxNew(\OxidEsales\Eshop\Application\Model\ManufacturerList::class);
        $oManufacturerList->loadManufacturerList();

        return $oManufacturerList;
    }

    /**
     * Loads language for article.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle
     * @param string                                      $sOxId
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
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
        $oDb = DatabaseProvider::getDb();
        return "insert into oxobject2category (oxid, oxobjectid, oxcatnid, oxtime) " .
            "VALUES (" . $oDb->quote($sUid) . ", " . $oDb->quote($newArticleId) . ", " .
            $oDb->quote($sCatId) . ", " . $oDb->quote($sTime) . ") ";
    }

    /**
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $base
     *
     * @return \OxidEsales\Eshop\Core\Model\BaseModel $base
     */
    protected function updateBase($base)
    {
        return $base;
    }

    /**
     * Customize article data for rendering.
     * Intended to be used by modules.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function customizeArticleInformation($article)
    {
        return $article;
    }

    /**
     * Save non standard article information if needed.
     * Intended to be used by modules.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     * @param array                                       $parameters
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function saveAdditionalArticleData($article, $parameters)
    {
        return $article;
    }

    /**
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function createArticle()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        return $oArticle;
    }
}
