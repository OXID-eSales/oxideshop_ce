<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Str;

/**
 * Admin article list manager.
 * Collects base article information (according to filtering rules), performs sorting,
 * deletion of articles, etc.
 * Admin Menu: Manage Products -> Articles.
 */
class ArticleList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxarticle';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxarticlelist';

    /**
     * @return bool|string
     */
    private function getServerDateTime()
    {
        $sDateTimeAsTimestamp = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();

        return \OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBTimestamp($sDateTimeAsTimestamp);
    }

    /**
     * @param bool|string $sDateTime
     * @param bool        $blUseTimeCheck
     * @param Article     $oArticle
     *
     * @return bool
     */
    private function isArticleActive($sDateTime, $blUseTimeCheck, $oArticle)
    {
        if (!\is_bool($sDateTime) && isset($oArticle->oxarticles__oxactive) && '1' === $oArticle->oxarticles__oxactive->value) {
            return true;
        } else {
            if (
                !\is_bool($sDateTime) && isset($oArticle->oxarticles__oxactivefrom) &&
                isset($oArticle->oxarticles__oxactiveto) && $blUseTimeCheck &&
                $oArticle->oxarticles__oxactivefrom->value <= $sDateTime &&
                $oArticle->oxarticles__oxactiveto->value >= $sDateTime
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Collects articles base data and passes them according to filtering rules,
     * returns name of template file "article_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sPwrSearchFld = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pwrsearchfld');
        $sPwrSearchFld = $sPwrSearchFld ? strtolower($sPwrSearchFld) : 'oxtitle';

        $sDateTime = $this->getServerDateTime();
        $blUseTimeCheck = $this->getConfig()->getConfigParam('blUseTimeCheck');
        $oArticle = null;
        $oList = $this->getItemList();
        if ($oList) {
            foreach ($oList as $key => $oArticle) {
                $sFieldName = "oxarticles__{$sPwrSearchFld}";

                // formatting view
                if (!$myConfig->getConfigParam('blSkipFormatConversion')) {
                    if ('datetime' === $oArticle->$sFieldName->fldtype) {
                        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($oArticle->$sFieldName);
                    } elseif ('timestamp' === $oArticle->$sFieldName->fldtype) {
                        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBTimestamp($oArticle->$sFieldName);
                    } elseif ('date' === $oArticle->$sFieldName->fldtype) {
                        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($oArticle->$sFieldName);
                    }
                }

                $oArticle->showActiveCheckInAdminPanel = $this->isArticleActive($sDateTime, $blUseTimeCheck, $oArticle);
                $oArticle->pwrsearchval = $oArticle->$sFieldName->value;
                $oList[$key] = $oArticle;
            }
        }

        parent::render();

        // load fields
        if (!$oArticle && $oList) {
            $oArticle = $oList->getBaseObject();
        }
        $this->_aViewData['pwrsearchfields'] = $oArticle ? $this->getSearchFields() : null;
        $this->_aViewData['pwrsearchfld'] = strtoupper($sPwrSearchFld);

        $aFilter = $this->getListFilter();
        if (isset($aFilter['oxarticles'][$sPwrSearchFld])) {
            $this->_aViewData['pwrsearchinput'] = $aFilter['oxarticles'][$sPwrSearchFld];
        }

        $sType = '';
        $sValue = '';

        $sArtCat = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('art_category');
        if ($sArtCat && false !== strstr($sArtCat, '@@')) {
            list($sType, $sValue) = explode('@@', $sArtCat);
        }
        $this->_aViewData['art_category'] = $sArtCat;

        // parent categorie tree
        $this->_aViewData['cattree'] = $this->getCategoryList($sType, $sValue);

        // manufacturer list
        $this->_aViewData['mnftree'] = $this->getManufacturerlist($sType, $sValue);

        // vendor list
        $this->_aViewData['vndtree'] = $this->getVendorList($sType, $sValue);

        return 'article_list.tpl';
    }

    /**
     * Returns array of fields which may be used for product data search.
     *
     * @return array
     */
    public function getSearchFields()
    {
        $aSkipFields = [
            'oxblfixedprice',
            'oxvarselect',
            'oxamitemid',
            'oxamtaskid',
            'oxpixiexport',
            'oxpixiexported',
        ];
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        return array_diff($oArticle->getFieldNames(), $aSkipFields);
    }

    /**
     * Load category list, mark active category;.
     *
     * @param string $sType  active list type
     * @param string $sValue active list item id
     *
     * @return \OxidEsales\Eshop\Application\Model\CategoryList
     */
    public function getCategoryList($sType, $sValue)
    {
        /** @var \OxidEsales\Eshop\Application\Model\CategoryList $oCatTree parent category tree */
        $oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
        $oCatTree->loadList();
        if ('cat' === $sType) {
            foreach ($oCatTree as $oCategory) {
                if ($oCategory->oxcategories__oxid->value === $sValue) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        }

        return $oCatTree;
    }

    /**
     * Load manufacturer list, mark active category;.
     *
     * @param string $sType  active list type
     * @param string $sValue active list item id
     *
     * @return \OxidEsales\Eshop\Application\Model\ManufacturerList
     */
    public function getManufacturerList($sType, $sValue)
    {
        $oMnfTree = oxNew(\OxidEsales\Eshop\Application\Model\ManufacturerList::class);
        $oMnfTree->loadManufacturerList();
        if ('mnf' === $sType) {
            foreach ($oMnfTree as $oManufacturer) {
                if ($oManufacturer->oxmanufacturers__oxid->value === $sValue) {
                    $oManufacturer->selected = 1;
                    break;
                }
            }
        }

        return $oMnfTree;
    }

    /**
     * Load vendor list, mark active category;.
     *
     * @param string $sType  active list type
     * @param string $sValue active list item id
     *
     * @return \OxidEsales\Eshop\Application\Model\VendorList
     */
    public function getVendorList($sType, $sValue)
    {
        $oVndTree = oxNew(\OxidEsales\Eshop\Application\Model\VendorList::class);
        $oVndTree->loadVendorList();
        if ('vnd' === $sType) {
            foreach ($oVndTree as $oVendor) {
                if ($oVendor->oxvendor__oxid->value === $sValue) {
                    $oVendor->selected = 1;
                    break;
                }
            }
        }

        return $oVndTree;
    }

    /**
     * Builds and returns SQL query string.
     *
     * @param object $oListObject list main object
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "buildSelectString" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _buildSelectString($oListObject = null)
    {
        $sQ = parent::_buildSelectString($oListObject);
        if ($sQ) {
            $sTable = getViewName('oxarticles');
            $sQ .= " and $sTable.oxparentid = '' ";

            $sType = false;
            $sArtCat = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('art_category');
            if ($sArtCat && false !== strstr($sArtCat, '@@')) {
                list($sType, $sValue) = explode('@@', $sArtCat);
            }

            switch ($sType) {
                // add category
                case 'cat':
                    $oStr = Str::getStr();
                    $sViewName = getViewName('oxobject2category');
                    $sInsert = "from $sTable left join {$sViewName} on {$sTable}.oxid = {$sViewName}.oxobjectid " .
                               "where {$sViewName}.oxcatnid = " . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sValue) . ' and ';
                    $sQ = $oStr->preg_replace("/from\s+$sTable\s+where/i", $sInsert, $sQ);
                    break;
                // add category
                case 'mnf':
                    $sQ .= " and $sTable.oxmanufacturerid = " . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sValue);
                    break;
                // add vendor
                case 'vnd':
                    $sQ .= " and $sTable.oxvendorid = " . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sValue);
                    break;
            }
        }

        return $sQ;
    }

    /**
     * Builds and returns array of SQL WHERE conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        // we override this to select only parent articles
        $this->_aWhere = parent::buildWhere();

        // adding folder check
        $sFolder = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('folder');
        if ($sFolder && '-1' !== $sFolder) {
            $this->_aWhere[getViewName('oxarticles') . '.oxfolder'] = $sFolder;
        }

        return $this->_aWhere;
    }

    /**
     * Deletes entry from the database.
     */
    public function deleteEntry(): void
    {
        $sOxId = $this->getEditObjectId();
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        if ($sOxId && $oArticle->load($sOxId)) {
            parent::deleteEntry();
        }
    }
}
