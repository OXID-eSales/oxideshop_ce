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

/**
 * Exception class for all adoDb problems, e.g.:
 * - connection problems
 * - wrong credentials
 * - incorrect queries
 */
class oxAdoDbException extends oxConnectionException
{
    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     *
     * @param string $sDbDriver   Database driver
     * @param string $sFunction   The name of the calling function (in uppercase)
     * @param int    $iErrorNr    The native error number from the database
     * @param string $sErrorMsg   the native error message from the database
     * @param string $sParam1     $sFunction specific parameter
     * @param string $sParam2     $sFunction specific parameter
     * @param string $oConnection Database connection object
     */
    public function __construct($sDbDriver, $sFunction, $iErrorNr, $sErrorMsg, $sParam1, $sParam2, $oConnection)
    {
        $sUser = $oConnection->username;
        $iErrorNr = is_numeric($iErrorNr) ? $iErrorNr : -1;

        $sMessage = "$sDbDriver error: [$iErrorNr: $sErrorMsg] in $sFunction ($sParam1, $sParam2) with user $sUser\n";

        parent::__construct($sMessage, $iErrorNr);
    }
}
