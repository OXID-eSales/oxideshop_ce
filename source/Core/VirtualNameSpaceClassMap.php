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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * @inheritdoc
 */
class VirtualNameSpaceClassMap extends \OxidEsales\EshopCommunity\Core\Edition\ClassMap
{

    /**
     * Returns leaf classes class map.
     *
     * @return array The classmap maps orignal calls to virtual class
     */
    public function getOverridableMap()
    {
        return [
            // 'OxidEsales\EshopCommunity\Application\Model\User' => 'OxidEsales\Eshop\Application\Model\User',
        ];
    }

    /**
     * Returns class map, of classes which can't be extended by modules.
     * There are no usecases for virtual namspaces in not overidable classes at the moment.
     * This function will return always an empty array.
     *
     * @return array The classmap maps orignal calls to virtual class
     */
    public function getNotOverridableMap()
    {
        return [];
    }
}
