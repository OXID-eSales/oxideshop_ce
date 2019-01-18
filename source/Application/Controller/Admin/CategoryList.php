<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;
use stdClass;

/**
 * Admin category list manager.
 * Collects attributes base information (sorting, title, etc.), there is ability to
 * filter them by sorting, title or delete them.
 * Admin Menu: Manage Products -> Categories.
 */
class CategoryList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxcategory';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxcategorylist';

    /**
     * Returns sorting fields array
     *
     * @return array
     */
    public function getListSorting()
    {
        $sSortParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('sort');
        if ($this->_aCurrSorting === null && !$sSortParameter && ($oBaseObject = $this->getItemListBaseObject())) {
            $sCatView = $oBaseObject->getCoreTableName();

            $this->_aCurrSorting[$sCatView]["oxrootid"] = "desc";
            $this->_aCurrSorting[$sCatView]["oxleft"] = "asc";

            return $this->_aCurrSorting;
        } else {
            return parent::getListSorting();
        }
    }

    /**
     * Loads category tree, passes data to Smarty and returns name of
     * template file "category_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iLang = $oLang->getTplLanguage();

        // parent category tree
        $oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
        $oCatTree->loadList();

        // add Root as fake category
        // rebuild list as we need the root entry at the first position
        $aNewList = [];
        $oRoot = new stdClass();
        $oRoot->oxcategories__oxid = new \OxidEsales\Eshop\Core\Field(null, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oRoot->oxcategories__oxtitle = new \OxidEsales\Eshop\Core\Field($oLang->translateString("viewAll", $iLang), \OxidEsales\Eshop\Core\Field::T_RAW);
        $aNewList[] = $oRoot;

        $oRoot = new stdClass();
        $oRoot->oxcategories__oxid = new \OxidEsales\Eshop\Core\Field("oxrootid", \OxidEsales\Eshop\Core\Field::T_RAW);
        $oRoot->oxcategories__oxtitle = new \OxidEsales\Eshop\Core\Field("-- " . $oLang->translateString("mainCategory", $iLang) . " --", \OxidEsales\Eshop\Core\Field::T_RAW);
        $aNewList[] = $oRoot;

        foreach ($oCatTree as $oCategory) {
            $aNewList[] = $oCategory;
        }

        $oCatTree->assign($aNewList);
        $aFilter = $this->getListFilter();
        if (is_array($aFilter) && isset($aFilter["oxcategories"]["oxparentid"])) {
            foreach ($oCatTree as $oCategory) {
                if ($oCategory->oxcategories__oxid->value == $aFilter["oxcategories"]["oxparentid"]) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        }

        $this->_aViewData["cattree"] = $oCatTree;

        return "category_list.tpl";
    }
}
