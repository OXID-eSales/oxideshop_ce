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

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

use oxBase;

/**
 * Import object for Categories.
 */
class Category extends ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxcategories';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxcategory';

    /**
     * Issued before saving an object. can modify aData for saving.
     *
     * @param oxBase $shopObject        Shop object.
     * @param array  $data              Data to prepare.
     * @param bool   $allowCustomShopId If allow custom shop id.
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        $data = parent::preAssignObject($shopObject, $data, $allowCustomShopId);

        if (!$data['OXPARENTID']) {
            $data['OXPARENTID'] = 'oxrootid';
        }

        return $data;
    }
}
