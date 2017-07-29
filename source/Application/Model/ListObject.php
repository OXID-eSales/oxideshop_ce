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
 * Simple list object
 */
class ListObject
{

    /**
     * @var string
     */
    private $_sTableName = '';

    /**
     * Class constructor
     *
     * @param string $sTableName Table name
     */
    public function __construct($sTableName)
    {
        $this->_sTableName = $sTableName;
    }

    /**
     * Assigns database record to object
     *
     * @param object $aData Database record
     *
     * @return null
     */
    public function assign($aData)
    {
        if (!is_array($aData)) {
            return;
        }
        foreach ($aData as $sKey => $sValue) {
            $sFieldName = strtolower($this->_sTableName . '__' . $sKey);
            $this->$sFieldName = new \OxidEsales\Eshop\Core\Field($sValue);
        }
    }

    /**
     * Returns object id
     *
     * @return int
     */
    public function getId()
    {
        $sFieldName = strtolower($this->_sTableName . '__oxid');
        return $this->$sFieldName->value;
    }
}
