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

namespace OxidEsales\Eshop\Core\GenericImport\ImportObject;

/**
 * Article extends type subclass
 */
class ArticleExtends extends ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxartextends';

    /**
     * Saves data by calling object saving.
     *
     * @param array $data              Data for saving
     * @param bool  $allowCustomShopId Allow custom shop id
     *
     * @return string|false
     */
    protected function saveObject($data, $allowCustomShopId)
    {
        $shopObject = oxNew('oxI18n');
        $shopObject->init('oxartextends');
        $shopObject->setLanguage(0);
        $shopObject->setEnableMultilang(false);

        foreach ($data as $key => $value) {
            $uppercaseKey = strtoupper($key);
            if (!isset($data[$uppercaseKey])) {
                unset($data[$key]);
                $data[$uppercaseKey] = $value;
            }
        }


        $isLoaded = false;
        if ($data['OXID']) {
            $isLoaded = $shopObject->load($data['OXID']);
        }

        $data = $this->preAssignObject($shopObject, $data, $allowCustomShopId);

        if ($isLoaded) {
            $this->checkWriteAccess($shopObject, $data);
        } else {
            $this->checkCreateAccess($data);
        }

        $shopObject->assign($data);

        if ($allowCustomShopId) {
            $shopObject->setIsDerived(false);
        }

        if ($this->preSaveObject($shopObject, $data)) {
            // store
            if ($shopObject->save()) {
                return $this->postSaveObject($shopObject, $data);
            }
        }

        return false;
    }
}
