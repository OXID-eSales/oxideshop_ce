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

namespace OxidEsales\EshopCommunity\Application\Model;

use oxField;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;

/**
 * Manages object (users, discounts, deliveries...) assignment to groups.
 */
class Object2Group extends \oxBase
{
    /**
     * Load the relation even if from other shop
     *
     * @var boolean
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxobject2group';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxobject2group');
        $this->oxobject2group__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
    }

    /**
     * Extends the default save method.
     * Saves only if this kind of entry do not exists.
     *
     * @throws DatabaseException
     *
     * @return bool
     */
    public function save()
    {
        try {
            return parent::save();
        } catch (DatabaseException $exception) {
            /**
             * The table oxobject2group has an UNIQUE index on (OXGROUPSID, OXOBJECTID, OXSHOPID)
             * If there is a DatabaseException and the exception code is 1062 i.e. "Duplicate entry",
             * the exception will be discarded and the record will not be inserted.
             */
            if ($exception->getCode() != '1062') {
                throw $exception;
            }
        }
    }
}
