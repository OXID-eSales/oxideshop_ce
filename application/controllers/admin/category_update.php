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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Class for updating category tree structure in DB.
 */
class Category_Update extends oxAdminView
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "category_update.tpl";

    /**
     * Category list object
     *
     * @var oxCategoryList
     */
    protected $_oCatList = null;

    /**
     * Returns category list object
     *
     * @return oxCategoryList
     */
    protected function _getCategoryList()
    {
        if ($this->_oCatList == null) {
            $this->_oCatList = oxNew("oxCategoryList");
            $this->_oCatList->updateCategoryTree(false);
        }

        return $this->_oCatList;
    }

    /**
     * Returns category list object
     *
     * @return array
     */
    public function getCatListUpdateInfo()
    {
        return $this->_getCategoryList()->getUpdateInfo();
    }
}
