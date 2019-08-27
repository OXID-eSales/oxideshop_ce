<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Class oxUserAddressList
 */
class UserAddressList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Call parent class constructor
     */
    public function __construct()
    {
        parent::__construct('oxaddress');
    }

    /**
     * Selects and loads all address for particular user.
     *
     * @param string $sUserId user id
     */
    public function load($sUserId)
    {
        $sViewName = getViewName('oxcountry');
        $oBaseObject = $this->getBaseObject();
        $sSelectFields = $oBaseObject->getSelectFields();

        $sSelect = "
                SELECT {$sSelectFields}, `oxcountry`.`oxtitle` AS oxcountry
                FROM oxaddress
                LEFT JOIN {$sViewName} AS oxcountry ON oxaddress.oxcountryid = oxcountry.oxid
                WHERE oxaddress.oxuserid = :oxuserid";
        $this->selectString($sSelect, [
            ':oxuserid' => $sUserId
        ]);
    }
}
