<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * User list manager.
 *
 */
class UserList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('oxuser');
    }


    /**
     * Load searched user list with wishlist
     *
     * @param string $sSearchStr Search string
     *
     * @return null;
     */
    public function loadWishlistUsers($sSearchStr)
    {
        $sSearchStr = trim($sSearchStr);

        if (!$sSearchStr) {
            return;
        }

        $sSelect = "select oxuser.oxid, oxuser.oxfname, oxuser.oxlname from oxuser ";
        $sSelect .= "left join oxuserbaskets on oxuserbaskets.oxuserid = oxuser.oxid ";
        $sSelect .= "where oxuserbaskets.oxid is not null and oxuserbaskets.oxtitle = 'wishlist' ";
        $sSelect .= "and oxuserbaskets.oxpublic = 1 ";
        $sSelect .= "and ( oxuser.oxusername = :search or oxuser.oxlname = :search)";
        $sSelect .= "and ( select 1 from oxuserbasketitems where oxuserbasketitems.oxbasketid = oxuserbaskets.oxid limit 1)";

        $this->selectString($sSelect, [
            ':search' => "$sSearchStr"
        ]);
    }
}
