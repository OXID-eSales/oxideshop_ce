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

namespace OxidEsales\EshopCommunity\Core\Dao;

/**
 *  The Data Access Object (DAO) layer encapsulates the access to a database.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
abstract class BaseDao implements \OxidEsales\Eshop\Core\Dao\BaseDaoInterface
{
    /**
     * @var \OxidEsales\Eshop\Core\DatabaseProvider Database connection
     */
    private $connection;

    /**
     * BaseDao constructor.
     *
     * @param \OxidEsales\Eshop\Core\DatabaseProvider $databaseProvider Database connection class.
     */
    public function __construct($databaseProvider)
    {
        $this->connection = $databaseProvider;
    }

    /**
     * Start a database transaction.
     */
    public function startTransaction()
    {
        $this->getDb()->startTransaction();
    }

    /**
     * Commit a database transaction.
     */
    public function commitTransaction()
    {
        $this->getDb()->commitTransaction();
    }

    /**
     * RollBack a database transaction.
     */
    public function rollbackTransaction()
    {
        $this->getDb()->rollbackTransaction();
    }

    /**
     * Returns database connection class.
     *
     * @param int $fetchMode The fetch mode. Default is numeric (0).
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    private function getDb($fetchMode = \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_NUM)
    {
        return $this->connection->getDb($fetchMode);
    }

    /**
     * Returns database connection class with fetch mode - associative.
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    protected function getAssociativeDb()
    {
        return $this->getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
    }

    /**
     * Returns database connection class with fetch mode - numeric.
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    protected function getNumericDb()
    {
        return $this->getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_NUM);
    }
}
