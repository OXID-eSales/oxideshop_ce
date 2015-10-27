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

use Exception;
use oxBase;
use OxidEsales\Eshop\Core\GenericImport\GenericImport;
use oxRegistry;

/**
 * Order Article erp type subclass
 */
class OrderArticle extends ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxorderarticles';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxorderarticle';

    /**
     * Check for write access for id
     *
     * @param oxBase $shopObject Loaded shop object
     * @param array  $data       Fields to be written, null for default
     *
     * @throws Exception on now access
     *
     * @return null
     */
    public function checkWriteAccess($shopObject, $data = null)
    {
        return;

        if ($shopObject->oxorderarticles__oxordershopid->value != oxRegistry::getConfig()->getShopId()) {
            throw new Exception(GenericImport::ERROR_USER_NO_RIGHTS);
        }

        parent::checkWriteAccess($shopObject, $data);
    }

    /**
     * issued before saving an object. can modify aData for saving
     *
     * @param oxBase $shopObject        oxBase child for object
     * @param array  $data              Data for object
     * @param bool   $allowCustomShopId If true then AllowCustomShopId
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        $data = parent::preAssignObject($shopObject, $data, $allowCustomShopId);

        // check if data is not serialized
        $persParamValues = @unserialize($data['OXPERSPARAM']);
        if (!is_array($persParamValues)) {
            // data is a string with | separation, prepare for oxid
            $persParamValues = explode("|", $data['OXPERSPARAM']);
            $data['OXPERSPARAM'] = serialize($persParamValues);
        }

        if (isset($data['OXORDERSHOPID'])) {
            $data['OXORDERSHOPID'] = 'oxbaseshop';
        }

        return $data;
    }
}
