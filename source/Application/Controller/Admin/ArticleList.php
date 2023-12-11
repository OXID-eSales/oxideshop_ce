<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

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
     * Collects articles base data and passes them according to filtering rules,
     * returns name of template file "article_list".
     *
     * @return string
     */
    public function render()
    {
        $listType = '';
        $activeItemId = '';
        $requestedCategory = Registry::getRequest()->getRequestEscapedParameter('art_category');
        $requestedSearchField = Registry::getRequest()->getRequestEscapedParameter('pwrsearchfld');
        $searchField = $requestedSearchField ? strtolower($requestedSearchField) : 'oxtitle';

        $productList = $this->getItemList();
        if ($productList && $productList->count()) {
            $this->convertSearchFieldValueForProductsInList($productList, $searchField);
            $this->setAdditionalSearchFieldForProductsInList($productList, $searchField);
            $this->setIsActiveFieldForProductsInList($productList);
        }

        parent::render();

        $this->_aViewData['pwrsearchfields'] = $productList->count() || $productList->getBaseObject()
            ? $this->getSearchFields()
            : null;
        $this->_aViewData['pwrsearchfld'] = strtoupper($searchField);

        $listFilter = $this->getListFilter();
        if (isset($listFilter['oxarticles'][$searchField])) {
            $this->_aViewData['pwrsearchinput'] = $listFilter['oxarticles'][$searchField];
        }

        if ($requestedCategory && strpos($requestedCategory, '@@') !== false) {
            [$listType, $activeItemId] = explode('@@', $requestedCategory);
        }
        $this->_aViewData['art_category'] = $requestedCategory;

        // parent categorie tree
        $this->_aViewData['cattree'] = $this->getCategoryList($listType, $activeItemId);

        // manufacturer list
        $this->_aViewData['mnftree'] = $this->getManufacturerlist($listType, $activeItemId);

        // vendor list
        $this->_aViewData['vndtree'] = $this->getVendorList($listType, $activeItemId);

        return "article_list";
    }

    /**
     * Returns array of fields which may be used for product data search
     *
     * @return array
     */
    public function getSearchFields()
    {
        $aSkipFields = [
            "oxblfixedprice",
            "oxvarselect",
            "oxamitemid",
            "oxamtaskid",
            "oxpixiexport",
            "oxpixiexported"
        ];
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        return array_diff($oArticle->getFieldNames(), $aSkipFields);
    }

    /**
     * Load category list, mark active category;
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
        if ($sType === 'cat') {
            foreach ($oCatTree as $oCategory) {
                if ($oCategory->oxcategories__oxid->value == $sValue) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        }

        return $oCatTree;
    }

    /**
     * Load manufacturer list, mark active category;
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
        if ($sType === 'mnf') {
            foreach ($oMnfTree as $oManufacturer) {
                if ($oManufacturer->oxmanufacturers__oxid->value == $sValue) {
                    $oManufacturer->selected = 1;
                    break;
                }
            }
        }

        return $oMnfTree;
    }

    /**
     * Load vendor list, mark active category;
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
        if ($sType === 'vnd') {
            foreach ($oVndTree as $oVendor) {
                if ($oVendor->oxvendor__oxid->value == $sValue) {
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
     */
    protected function buildSelectString($oListObject = null)
    {
        $sQ = parent::buildSelectString($oListObject);
        if ($sQ) {
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sTable = $tableViewNameGenerator->getViewName("oxarticles");
            $sQ .= " and $sTable.oxparentid = '' ";

            $sType = false;
            $sArtCat = Registry::getRequest()->getRequestEscapedParameter("art_category");
            if ($sArtCat && strstr($sArtCat, "@@") !== false) {
                list($sType, $sValue) = explode("@@", $sArtCat);
            }

            switch ($sType) {
                // add category
                case 'cat':
                    $oStr = Str::getStr();
                    $sViewName = $tableViewNameGenerator->getViewName("oxobject2category");
                    $sInsert = "from $sTable left join {$sViewName} on {$sTable}.oxid = {$sViewName}.oxobjectid " .
                               "where {$sViewName}.oxcatnid = " . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sValue) . " and ";
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
        $sFolder = Registry::getRequest()->getRequestEscapedParameter('folder');
        if ($sFolder && $sFolder != '-1') {
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $this->_aWhere[$tableViewNameGenerator->getViewName("oxarticles") . ".oxfolder"] = $sFolder;
        }

        return $this->_aWhere;
    }

    /**
     * Deletes entry from the database
     */
    public function deleteEntry()
    {
        $sOxId = $this->getEditObjectId();
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        if ($sOxId && $oArticle->load($sOxId)) {
            parent::deleteEntry();
        }
    }

    private function convertSearchFieldValueForProductsInList(ListModel $productList, string $searchField): void
    {
        $fieldName = "oxarticles__{$searchField}";
        if (
            Registry::getConfig()->getConfigParam('blSkipFormatConversion')
            || !$this->isDateField($this->getFieldTypeForCurrentProduct($productList, $fieldName))
        ) {
            return;
        }
        foreach ($productList as $key => $product) {
            $this->convertValueToDatabaseTimestamp($product->$fieldName);
            $productList[$key] = $product;
        }
    }

    private function getFieldTypeForCurrentProduct(ListModel $productList, string $fieldName): string
    {
        $currentProduct = $productList->offsetGet($productList->key());
        return $currentProduct->$fieldName->fldtype;
    }

    private function isDateField(string $fieldType): bool
    {
        return in_array(
            $fieldType,
            [
                'date',
                'datetime',
                'timestamp',
            ]
        );
    }

    private function setAdditionalSearchFieldForProductsInList(ListModel $productList, string $searchField): void
    {
        $fieldName = "oxarticles__{$searchField}";
        foreach ($productList as $key => $product) {
            $product->pwrsearchval = $product->$fieldName->value;
            $productList[$key] = $product;
        }
    }

    private function setIsActiveFieldForProductsInList(ListModel $productList): void
    {
        $useTimeCheck = Registry::getConfig()->getConfigParam('blUseTimeCheck');
        foreach ($productList as $key => $product) {
            $product->showActiveCheckInAdminPanel = $product->isProductAlwaysActive();

            if ($useTimeCheck) {
                $product->hasActiveTimeRange = $product->hasProductValidTimeRange();
                $product->isActiveNow = $product->hasActiveTimeRange();
            }
            $productList[$key] = $product;
        }
    }

    private function convertValueToDatabaseTimestamp(Field $field): void
    {
        if ($field->fldtype === 'datetime') {
            Registry::getUtilsDate()->convertDBDateTime($field);
        } elseif ($field->fldtype === 'timestamp') {
            Registry::getUtilsDate()->convertDBTimestamp($field);
        } elseif ($field->fldtype === 'date') {
            Registry::getUtilsDate()->convertDBDate($field);
        }
    }
}
