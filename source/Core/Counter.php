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

namespace OxidEsales\EshopCommunity\Core;

use Exception;
use oxDb;

/**
 * Counter class
 *
 */
class Counter
{

    /**
     * Return the next counter value for a given type of counter
     *
     * @param string $ident Identifies the type of counter. E.g. 'oxOrder'
     *
     * @throws Exception
     *
     * @return int Next counter value
     */
    public function getNext($ident)
    {
        $database = oxDb::getDb();

        /** Current counter retrieval needs to be encapsulated in transaction */
        $database->startTransaction();
        try {
            /** Block row for reading until the counter is updated */
            $query = "SELECT `oxcount` FROM `oxcounters` WHERE `oxident` = ? FOR UPDATE";
            $currentCounter = (int) $database->getOne($query, array($ident));
            $nextCounter = $currentCounter + 1;

            /** Insert or increment the the counter */
            $query = "INSERT INTO `oxcounters` (`oxident`, `oxcount`) VALUES (?, 1) ON DUPLICATE KEY UPDATE `oxcount` = `oxcount` + 1";
            $database->execute($query, array($ident));

            $database->commitTransaction();
        } catch (Exception $exception) {
            $database->rollbackTransaction();

            throw $exception;
        }

        return $nextCounter;
    }

    /**
     * Update the counter value for a given type of counter, but only when it is greater than the current value
     *
     * @param string  $ident Identifies the type of counter. E.g. 'oxOrder'
     * @param integer $count New counter value
     *
     * @throws Exception
     *
     * @return int Number of affected rows
     */
    public function update($ident, $count)
    {
        $database = oxDb::getDb();

        /** Current counter retrieval needs to be encapsulated in transaction */
        $database->startTransaction();
        try {
            /** Block row for reading until the counter is updated */
            $query = "SELECT `oxcount` FROM `oxcounters` WHERE `oxident` = ? FOR UPDATE";
            $database->getOne($query, array($ident));

            /** Insert or update the counter, if the value to be updated is greater, than the current value */
            $query = "INSERT INTO `oxcounters` (`oxident`, `oxcount`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `oxcount` = IF(? > oxcount, ?, oxcount)";
            $result = $database->execute($query, array($ident, $count, $count, $count ));

            $database->commitTransaction();
        } catch (Exception $exception) {
            $database->rollbackTransaction();

            throw $exception;
        }

        return $result;
    }
}
