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
 * List of additional shop information links widget.
 * Forms info link list.
 */
class oxwInformation extends oxWidget
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/footer/info.tpl';

    /**
     * @var oxContentList
     */
    protected $_oContentList;

    /**
     * Returns service keys.
     *
     * @return array
     */
    public function getServicesKeys()
    {
        $oContentList = $this->_getContentList();
        return $oContentList->getServiceKeys();
    }

    /**
     * Get services content list
     *
     * @return array
     */
    public function getServicesList()
    {
        $oContentList = $this->_getContentList();
        $oContentList->loadServices();

        return $oContentList;
    }

    /**
     * Returns content list object.
     *
     * @return object|oxContentList
     */
    protected function _getContentList()
    {
        if ( !$this->_oContentList ) {
            $this->_oContentList = oxNew( "oxContentList" );
        }

        return $this->_oContentList;
    }
}
