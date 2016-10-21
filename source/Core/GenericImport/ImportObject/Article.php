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

use oxArticle;
use oxBase;

/**
 * Import object for Articles.
 */
class Article extends ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxarticles';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxArticle';

    /**
     * Imports article. Returns import status.
     *
     * @param array $data DB row array.
     *
     * @return string $oxid Id on success, bool FALSE on failure.
     */
    public function import($data)
    {
        if (isset($data['OXID'])) {
            $this->checkIdField($data['OXID']);
        }

        return parent::import($data);
    }

    /**
     * Issued before saving an object.
     * Can modify $data array before saving.
     * Set default value of OXSTOCKFLAG to 1 according to eShop admin functionality.
     *
     * @param oxBase $shopObject        shop object
     * @param array  $data              data to prepare
     * @param bool   $allowCustomShopId if allow custom shop id
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        if (!isset($data['OXSTOCKFLAG'])) {
            if (!$data['OXID'] || !$shopObject->exists($data['OXID'])) {
                $data['OXSTOCKFLAG'] = 1;
            }
        }

        return parent::preAssignObject($shopObject, $data, $allowCustomShopId);
    }

    /**
     * Post saving hook. can finish transactions if needed or ajust related data.
     *
     * @param oxArticle $shopObject Shop object.
     * @param array     $data       Data to save.
     *
     * @return mixed data to return
     */
    protected function postSaveObject($shopObject, $data)
    {
        $articleId = $shopObject->getId();
        $shopObject->onChange(null, $articleId, $articleId);

        return $articleId;
    }

    /**
     * Creates shop object.
     *
     * @return oxBase
     */
    protected function createShopObject()
    {
        /** @var oxArticle $shopObject */
        $shopObject = parent::createShopObject();
        $shopObject->setNoVariantLoading(true);

        return $shopObject;
    }
}
