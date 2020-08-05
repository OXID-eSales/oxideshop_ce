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
     */
    public function getViewListSize()
    {
        return $this->getUserDefListSize();
    }

    /**
     * Sets SQL query parameters (such as sorting),
     * executes parent method parent::Init().
     */
    public function init()
    {
        oxAdminList::init();
    }

    /** @inheritdoc */
    public function render()
    {
        parent::render();
        $this->_aViewData["menustructure"] = $this->getNavigation()->getDomXml()->documentElement->childNodes;

        return "list_user";
    }
}
