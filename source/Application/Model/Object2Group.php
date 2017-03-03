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
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;

/**
 * Manages object (users, discounts, deliveries...) assignment to groups.
 */
class Object2Group extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /** @var boolean Load the relation even if from other shop */
    protected $_blDisableShopCheck = true;

    /** @var string Current class name */
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
            if ($exception->getCode() !== \OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database::DUPLICATE_KEY_ERROR_CODE) {
                throw $exception;
            }
        }
    }
}
