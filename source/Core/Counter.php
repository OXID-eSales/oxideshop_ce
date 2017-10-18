<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;

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
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        /** Current counter retrieval needs to be encapsulated in transaction */
        $database->startTransaction();
        try {
            /** Block row for reading until the counter is updated */
            $query = "SELECT `oxcount` FROM `oxcounters` WHERE `oxident` = ? FOR UPDATE";
            $currentCounter = (int) $database->getOne($query, [$ident]);
            $nextCounter = $currentCounter + 1;

            /** Insert or increment the the counter */
            $query = "INSERT INTO `oxcounters` (`oxident`, `oxcount`) VALUES (?, 1) ON DUPLICATE KEY UPDATE `oxcount` = `oxcount` + 1";
            $database->execute($query, [$ident]);

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
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        /** Current counter retrieval needs to be encapsulated in transaction */
        $database->startTransaction();
        try {
            /** Block row for reading until the counter is updated */
            $query = "SELECT `oxcount` FROM `oxcounters` WHERE `oxident` = ? FOR UPDATE";
            $database->getOne($query, [$ident]);

            /** Insert or update the counter, if the value to be updated is greater, than the current value */
            $query = "INSERT INTO `oxcounters` (`oxident`, `oxcount`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `oxcount` = IF(? > oxcount, ?, oxcount)";
            $result = $database->execute($query, [$ident, $count, $count, $count ]);

            $database->commitTransaction();
        } catch (Exception $exception) {
            $database->rollbackTransaction();

            throw $exception;
        }

        return $result;
    }
}
