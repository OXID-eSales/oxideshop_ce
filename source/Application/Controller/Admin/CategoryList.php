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
        $myConfig = $this->getConfig();

        parent::render();

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iLang = $oLang->getTplLanguage();

        // parent category tree
        $oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
        $oCatTree->loadList();

        // add Root as fake category
        // rebuild list as we need the root entry at the first position
        $aNewList = array();
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
