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
use oxField;
use oxRegistry;

/**
 * user erp type subclass
 */
class User extends ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxuser';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxuser';

    /**
     * Imports user. Returns import status.
     *
     * @param array $data db row array
     *
     * @throws Exception If user exists with provided OXID, throw an exception.
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    public function import($data)
    {
        if (isset($data['OXUSERNAME'])) {
            $id = $data['OXID'];
            $userName = $data['OXUSERNAME'];

            $user = oxNew("oxUser", "core");
            $user->oxuser__oxusername = new oxField($userName, oxField::T_RAW);

            if ($user->exists($id) && $id != $user->getId()) {
                throw new Exception("USER $userName already exists!");
            }
        }

        return parent::import($data);
    }

    /**
     * Basic access check for writing data, checks for same shopid, should be overridden if field oxshopid does not exist
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

        $config = oxRegistry::getConfig();

        if (!$config->getConfigParam('blMallUsers')) {
            parent::checkWriteAccess($shopObject, $data);
        }
    }
}
