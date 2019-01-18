<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Class for updating category tree structure in DB.
 */
class CategoryUpdate extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
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
            $this->_oCatList = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
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
