<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxAdminList;

/**
 * user list "view" class.
 */
class ListUser extends \OxidEsales\Eshop\Application\Controller\Admin\UserList
{
    /**
     * Viewable list size getter
     *
     * @return int
     * @deprecated underscore prefix violates PSR12, will be renamed to "getViewListSize" in next version
     */
    protected function _getViewListSize() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->_getUserDefListSize();
    }

    /**
     * Sets SQL query parameters (such as sorting),
     * executes parent method parent::Init().
     */
    public function init()
    {
        oxAdminList::init();
    }

    /**
     * Executes parent method parent::render(), passes data to Smarty engine
     * and returns name of template file "list_review.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData["menustructure"] = $this->getNavigation()->getDomXml()->documentElement->childNodes;

        return "list_user.tpl";
    }
}
