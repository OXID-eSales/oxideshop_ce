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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Simple variant list.
 *
 * @package model
 */
class oxSimpleVariantList extends oxList
{
    /**
     * Parent article for list variants
     */
    protected $_oParent = null;

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxsimplevariant';

    /**
     * Sets parent variant
     *
     * @param oxArticle $oParent Parent article
     *
     * @return null
     */
    public function setParent($oParent)
    {
        $this->_oParent = $oParent;
    }

    /**
     * Sets parent for variant. This method is invoked for each element in oxList::assign() loop.
     *
     * @param oxSimleVariant $oListObject Simple variant
     * @param array          $aDbFields   Array of available
     *
     * @return null;
     */
    protected function _assignElement($oListObject, $aDbFields)
    {
        $oListObject->setParent($this->_oParent);
        parent::_assignElement($oListObject, $aDbFields);
    }
}
