<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Shop list manager.
 * Organizes list of shop objects.
 */
class ShopList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Calls parent constructor.
     */
    public function __construct()
    {
        parent::__construct('oxshop');
    }

    /**
     * Loads all shops to list.
     */
    public function getAll(): void
    {
        $this->selectString('SELECT `oxshops`.* FROM `oxshops`');
    }

    /**
     * Gets shop list into object.
     */
    public function getIdTitleList(): void
    {
        $this->setBaseObject(oxNew('oxListObject', 'oxshops'));
        $this->selectString('SELECT `OXID`, `OXNAME` FROM `oxshops`');
    }
}
