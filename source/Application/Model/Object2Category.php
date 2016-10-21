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

/**
 * Manages product assignment to category.
 */
class Object2Category extends \oxBase
{

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxobject2category';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()) and sets table name.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxobject2category');
    }

    /**
     * Returns assigned product id
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->oxobject2category__oxobjectid->value;
    }

    /**
     * Sets assigned product id
     *
     * @param string $sId assigned product id
     */
    public function setProductId($sId)
    {
        $this->oxobject2category__oxobjectid = new oxField($sId);
    }

    /**
     * Returns assigned category id
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->oxobject2category__oxcatnid->value;
    }

    /**
     * Sets assigned category id
     *
     * @param string $sId assigned category id
     */
    public function setCategoryId($sId)
    {
        $this->oxobject2category__oxcatnid = new oxField($sId);
    }
}
