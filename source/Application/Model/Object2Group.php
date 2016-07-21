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

namespace OxidEsales\Eshop\Application\Model;

use oxField;
use oxDb;

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
     * @return bool
     */
    public function save()
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = oxDb::getMaster();
        $sQ = "select 1 from oxobject2group where oxgroupsid = " . $masterDb->quote($this->oxobject2group__oxgroupsid->value);
        $sQ .= " and oxobjectid = " . $masterDb->quote($this->oxobject2group__oxobjectid->value);

        // does not exist
        if (!$masterDb->getOne($sQ)) {
            return parent::save();
        }
    }
}
