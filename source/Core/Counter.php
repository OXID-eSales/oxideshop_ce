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

namespace OxidEsales\Eshop\Core;

use oxDb;

/**
 * Counter class
 *
 */
class Counter
{

    /**
     * Returns next counter value
     *
     * @param string $ident counter ident
     *
     * @return int
     */
    public function getNext($ident)
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = oxDb::getMaster();
        $masterDb->startTransaction();

        $query = "SELECT `oxcount` FROM `oxcounters` WHERE `oxident` = " . $masterDb->quote($ident) . " FOR UPDATE";

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        if (($cnt = $masterDb->getOne($query)) === false) {
            $query = "INSERT INTO `oxcounters` (`oxident`, `oxcount`) VALUES (?, '0')";
            $masterDb->execute($query, array($ident));
        }

        $cnt = ((int) $cnt) + 1;
        $query = "UPDATE `oxcounters` SET `oxcount` = ? WHERE `oxident` = ?";
        $masterDb->execute($query, array($cnt, $ident));

        $masterDb->commitTransaction();

        return $cnt;
    }

    /**
     * update counter value, only when it is greater than old one,
     * if counter ident not exist creates counter and sets value
     *
     * @param string  $ident counter ident
     * @param integer $count value
     *
     * @return int
     */
    public function update($ident, $count)
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = oxDb::getMaster();
        $masterDb->startTransaction();

        $query = "SELECT `oxcount` FROM `oxcounters` WHERE `oxident` = " . $masterDb->quote($ident) . " FOR UPDATE";

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        if (($cnt = $masterDb->getOne($query)) === false) {
            $query = "INSERT INTO `oxcounters` (`oxident`, `oxcount`) VALUES (?, ?)";
            $result = $masterDb->execute($query, array($ident, $count));
        } else {
            $query = "UPDATE `oxcounters` SET `oxcount` = ? WHERE `oxident` = ? AND `oxcount` < ?";
            $result = $masterDb->execute($query, array($count, $ident, $count));
        }

        $masterDb->commitTransaction();

        return $result;
    }
}
