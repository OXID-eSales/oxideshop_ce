<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Application\Model\ListObject;
use OxidEsales\Eshop\Application\Model\Shop;

/**
 * Shop list manager.
 * Organizes list of shop objects.
 */
class ShopList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Calls parent constructor
     */
    public function __construct()
    {
        parent::__construct(Shop::class);
    }

    /**
     * Loads all shops to list
     */
    public function getAll()
    {
        $this->selectString('SELECT `oxshops`.* FROM `oxshops`');
    }

    /**
     * Gets shop list into object
     */
    public function getIdTitleList()
    {
        $this->setBaseObject(oxNew(ListObject::class, 'oxshops'));
        $this->selectString('SELECT `OXID`, `OXNAME` FROM `oxshops`');
    }
}
