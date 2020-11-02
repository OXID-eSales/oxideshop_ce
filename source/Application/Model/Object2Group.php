<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;

/**
 * Manages object (users, discounts, deliveries...) assignment to groups.
 */
class Object2Group extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * @var bool Load the relation even if from other shop
     */
    protected $_blDisableShopCheck = true;

    /**
     * @var string Current class name
     */
    protected $_sClassName = 'oxobject2group';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxobject2group');
        $this->oxobject2group__oxshopid = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getConfig()->getShopId(), \OxidEsales\Eshop\Core\Field::T_RAW);
    }

    /**
     * Extends the default save method
     * to prevent from exception if same relationship already exist.
     * The table oxobject2group has an UNIQUE index on (OXGROUPSID, OXOBJECTID, OXSHOPID)
     * which ensures that a relationship would not be duplicated.
     *
     * @throws DatabaseErrorException
     *
     * @return bool
     */
    public function save()
    {
        try {
            return parent::save();
        } catch (\OxidEsales\Eshop\Core\Exception\DatabaseErrorException $exception) {
            if (\OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database::DUPLICATE_KEY_ERROR_CODE !== $exception->getCode()) {
                throw $exception;
            }
        }
    }
}
